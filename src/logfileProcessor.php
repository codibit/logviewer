<?php
namespace App;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

/*
 *
 * This Class will process the provided file in the documentation and create a json file from it.
 *
 * */
class logfileProcessor
{
    public $jsonFilePath = '../doc/3.1 Candidate Assignment - Public Folder/epadata.json';

    private $jsonResource;

    private int $line = 1;

    public function __construct(){

        file_put_contents( $this->jsonFilePath, '');

        $this->jsonResource = fopen( $this->jsonFilePath, 'wb');

        fwrite($this->jsonResource, '[');

    }

    public function __destruct()
    {
        $this->close();
    }

    public function close(): void
    {

        if (is_resource($this->jsonResource)) {
            fwrite($this->jsonResource, ']');

            fclose($this->jsonResource);
        }
    }

    public function addLine($lineData): void
    {
        if ($this->line !== 0) {
            fwrite($this->jsonResource, ',');
        }

        fwrite($this->jsonResource, json_encode($lineData));
        $this->line++;

    }

    public function process($filepath = '../doc/3.1 Candidate Assignment - Public Folder/epa-http.txt', ){

        $filesystem = new Filesystem();


        if($filesystem->exists($filepath)){
            $logfile = fopen($filepath, "r");
            if(is_resource($logfile)){

                while (($logline = fgets($logfile)) !== false){

                    if(trim($logline) === ''){
                        continue;
                    }

                    /* Find basic items in the line */
                    preg_match('/(?<host>[^ ]*) \[(?<datetime_day>[\d]*)\:(?<datetime_hour>[\d]*):'.
                        '(?<datetime_minute>[\d]*):(?<datetime_second>[\d]*)[^"]*"(?<request>.*)"'.
                        ' (?<response_code>[\d]*) (?<document_size>[\d\-]*)/',
                        $logline,
                        $linedata);

                    /* Extract request string */
                    $request = substr($logline,strpos($logline, '"'),strrpos($logline, '"') - strpos($logline, '"') +1 );

                    /* Find request items */
                    preg_match('/\"(?<method>[A-Z]*)? ?(?<url>(?:(?! [A-Z]*\/[0-9.]*\"$|\"$).)*) ?(?<protocol>[A-Z]*)?\/?(?<protocol_version>[0-9.]*)?/',  $request, $linedata['request']);


                    /* Before proceeding, check if the data we processed matches the data we received 1:1
                       TODO: Take this into a function.
                    */

                    $linedata['request']['method_verify'] = $linedata['request']['method']? $linedata['request']['method'].' ' : '';
                    $linedata['request']['protocol_verify'] = $linedata['request']['protocol']? ' '.$linedata['request']['protocol'] : '';
                    $linedata['request']['protocol_version_verify'] = $linedata['request']['protocol_version']? '/'.$linedata['request']['protocol_version'] : '';


                    $verify_line = $linedata['host']
                        .' [' .$linedata['datetime_day'].':'.$linedata['datetime_hour'].':'.$linedata['datetime_minute'].':'.$linedata['datetime_second'] . '] "'
                        .$linedata['request']['method_verify']. $linedata['request']['url'] . $linedata['request']['protocol_verify'] . $linedata['request']['protocol_version_verify']. '" '
                        . $linedata['response_code'] .' '. $linedata['document_size'];


                    if(trim(str_replace($verify_line, '' , $logline)) !== ''){
                        throw new \RuntimeException('INPUT AND OUTPUT LINES DO NOT MATCH.' . "\nInput Line: $logline\nProcessed line: $verify_line\n");
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