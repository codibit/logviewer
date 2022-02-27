<?php

namespace App\Controller;

use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;


class LogfileController extends AbstractController
{

    #[Route('/logfile', name: 'logfile')]
    public function index(): Response
    {
        return $this->render('logfile/index.html.twig', [
            'controller_name' => 'LogfileController',
        ]);
    }

    #[Route('/logfile/process', name: 'logfileProcess')]
    public function process($filepath = false): Response
    {
        $filesystem = new Filesystem();

        if(!$filepath){
            $filepath =  '/doc/3.1 Candidate Assignment - Public Folder/epa-http.txt';
        }

        $filepath = $this->getParameter('kernel.project_dir') . $filepath;
        $destination = dirname($filepath).'/epa-http.json';

        if($filesystem->exists($filepath)){
           // $logtxt = file_get_contents($filepath);
            $logfile = fopen($filepath, "r");
            $linetotal = count(file($filepath));
            $linecount = 0;
            file_put_contents($destination, '');
            $destination = fopen($destination, 'wb');

            if($logfile){

                fwrite($destination, '[');
                while (($logline = fgets($logfile)) !== false){

                    if(trim($logline) === ''){
                        continue;
                    }

                    $linecount++;
                    preg_match('/(?<host>[^ ]*) \[(?<datetime_day>[\d]*)\:(?<datetime_hour>[\d]*):(?<datetime_minute>[\d]*):(?<datetime_second>[\d]*)[^"]*"(?<request>.*)" (?<response_code>[\d]*) (?<document_size>[\d\-]*)/', $logline, $linedata);
                    $request = substr($logline,strpos($logline, '"'),strrpos($logline, '"') - strpos($logline, '"') +1 );
                    preg_match('/\"(?<method>[A-Z]*)? ?(?<url>(?:(?! [A-Z]*\/[0-9.]*\"$|\"$).)*) ?(?<protocol>[A-Z]*)?\/?(?<protocol_version>[0-9.]*)?/',  $request, $linedata['request']);

                    if ($linecount !== 1) {
                        fwrite($destination, ',');
                    }

                    fwrite($destination, json_encode([
                        'line' => $linecount,
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
                    ]));

                    $linedata['request']['method'] = $linedata['request']['method']? $linedata['request']['method'].' ' : '';
                    $linedata['request']['protocol'] = $linedata['request']['protocol']? ' '.$linedata['request']['protocol'] : '';
                    $linedata['request']['protocol_version'] = $linedata['request']['protocol_version']? '/'.$linedata['request']['protocol_version'] : '';

                    /*
                    $verify_line = $linedata['host']
                        .' [' .$linedata['datetime_day'].':'.$linedata['datetime_hour'].':'.$linedata['datetime_minute'].':'.$linedata['datetime_second'] . '] "'
                        .$linedata['request']['method']. $linedata['request']['url'] . $linedata['request']['protocol'] . $linedata['request']['protocol_version']. '" '
                        . $linedata['response_code'] .' '. $linedata['document_size'];


                    if(trim(str_replace($verify_line, '' , $logline)) !== ''){
                        echo "\n".'========== LINE DOES NOT MATCH =============='."\n$logline\n$verify_line\n";
                        echo str_replace($verify_line, '' , $logline);
                    }*/
                }
                fwrite($destination, ']');
                fclose($destination);
            }

        }else{
            throw new createNotFoundException('The provided file does not exist.');
        }



    }

    #[Route('/logfile/epadata.json', name: 'jsonfile')]
    public function jsonfile($filepath = false): Response
    {
        $filepath =  '/doc/3.1 Candidate Assignment - Public Folder/epa-http.json';
        return new Response(file_get_contents($this->getParameter('kernel.project_dir') . $filepath),
            Response::HTTP_OK,
            ['content-type' => 'application/json']);

    }
}
