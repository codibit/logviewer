<?php
namespace App;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use App\Entity\Logfile;
use App\Entity\Httprequest;

/*
 *
 * This Class will process the provided file and create a json file from it.
 *
 * */
class logfileProcessor
{
    private $jsonFilePath;

    private $jsonResource;

    private int $line = 0;

    public function __destruct()
    {
        $this->closeJson();
    }

    private function openJson(){
        file_put_contents( $this->jsonFilePath, '');

        $this->jsonResource = fopen( $this->jsonFilePath, 'wb');

        fwrite($this->jsonResource, '[');
    }

    private function closeJson(): void
    {

        if (is_resource($this->jsonResource)) {
            fwrite($this->jsonResource, ']');

            fclose($this->jsonResource);
        }
    }

    private function addLine($lineData): void
    {
        if ($this->line !== 0) {
            fwrite($this->jsonResource, ',');
        }else{
            $this->openJson();
        }

        fwrite($this->jsonResource, json_encode($lineData));
        $this->line++;

    }

    private function getLineData($logline){

        preg_match('/(?<host>[^ ]*) \[(?<datetime_day>[\d]*)\:(?<datetime_hour>[\d]*):'.
            '(?<datetime_minute>[\d]*):(?<datetime_second>[\d]*)[^"]*"(?<request>.*)"'.
            ' (?<response_code>[\d]*) (?<document_size>[\d\-]*)/',
            $logline,
            $linedata);

        return $linedata;
    }

    private function getRequestData($request){

        preg_match('/\"(?<method>[A-Z]*)? ?(?<url>(?:(?! [A-Z]*\/[0-9.]*\"$|\"$).)*) ?(?<protocol>[A-Z]*)?\/?(?<protocol_version>[0-9.]*)?/',
            $request, $requestdata);

        return $requestdata;
    }

    private function verifyLineData($logline, $linedata){
        $verify_line = $linedata['host']
            . ' [' .$linedata['datetime_day']
            . ':'.$linedata['datetime_hour']
            . ':'.$linedata['datetime_minute']
            . ':'.$linedata['datetime_second']
            . '] "'.($linedata['request']['method']? $linedata['request']['method'].' ' : '')
            . $linedata['request']['url']
            . ($linedata['request']['protocol']? ' '.$linedata['request']['protocol'] : '')
            . ($linedata['request']['protocol_version']? '/'.$linedata['request']['protocol_version'] : ''). '" '
            . $linedata['response_code']
            . ' '. $linedata['document_size'];

        if(trim(str_replace($verify_line, '' , $logline)) === ''){
            return $verify_line;
        }else{
            return false;
        }
    }
    public function process(Logfile $logfile){

        $filesystem = new Filesystem();

        $filepath = '../var/storage/'.$logfile->getFilename();

        $this->jsonFilePath = '../var/storage/'.$logfile->getJson();;

        if($filesystem->exists($filepath)){

            $file = fopen($filepath, "r");

            if(is_resource($file)){



                while (($logline = fgets($file)) !== false){

                    if(trim($logline) === ''){
                        continue;
                    }

                    /* Find basic items in the line */
                    $linedata = $this->getLineData();

                    /* Extract request string and find request items */

                    $linedata['request'] = $this->getRequestData(
                        substr($logline,strpos($logline, '"'),
                            strrpos($logline,
                                '"') - strpos($logline, '"') +1 )
                    );




                    /* Before proceeding, check if the data we processed matches the data we received 1:1
                       TODO: Take this into a function.
                    */
                    try{
                        $this->verifyLine($logline, $linedata);
                    }  catch (Exception $e) {
                        throw new \RuntimeException('Line processing failed.' . "\nInput Line: $logline\n");
                    }


                    /* add line to json file */
                    $this->addLine([
                        'host' => $linedata['host'],
                        'datetime' => [
                            'day' => $linedata['datetime_day'],
                            'hour' =>  $linedata['datetime_hour'],
                            'minute' => $linedata['datetime_minute'] ,
                            'second' =>  $linedata['datetime_second']
                        ],
                        'request' => [
                            'method'=> $linedata['request']['method']?? null,
                            'url'=> $linedata['request']['url'],
                            'protocol' => $linedata['request']['protocol'] ?? null,
                            'protocol_version'=> $linedata['request']['protocol_version'] ?? null
                        ],
                        'response_code' => $linedata['response_code'],
                        'document_size' => $linedata['document_size']
                    ]);

                }
            }else{
                throw \RuntimeException('$logline is not a resource.');
            }

        }else{
            throw new \RuntimeException('The provided file does not exist.');
        }

        return true;
    }
}