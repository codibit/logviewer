<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;


class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ChartBuilderInterface $chartBuilder): Response
    {
        $data = json_decode( file_get_contents($this->getParameter('kernel.project_dir') . '/doc/3.1 Candidate Assignment - Public Folder/epa-http.json'));

        for ($i = 0; $i <= 9; $i++){
            $chart4_data[$i*100] = 0;
        }
        foreach ($data as $logline){
            $datetime = '1995-08-'.$logline->datetime->day.' '.$logline->datetime->hour.':'.$logline->datetime->minute;
            if(!isset($chart2_data[$datetime])){
                $chart2_data[$datetime] = 0;
            }
            $chart2_data[$datetime] ++;

            if(trim($logline->request->method) == ''){
                $logline->request->method = 'INVALID';
            }

            if(trim($logline->response_code) == ''){
                $logline->response_code = 'INVALID';
            }

            if(!isset($chart1_data[$logline->request->method])){
                $chart1_data[$logline->request->method] = 0;
            }

            $chart1_data[$logline->request->method] ++;


            if(!isset($chart3_data[$logline->response_code])){
                $chart3_data[$logline->response_code] = 0;
            }

            $chart3_data[$logline->response_code] ++;

            if($logline->response_code == 200 && $logline->document_size < 1000){



                $chart4_data[floor($logline->document_size/100)*100]++;
            }


        }


        //print_r($chart1_data);
        foreach($chart1_data as $label => $value){
            $chart1_labels[] = $label .' ('. ( round(($value/count($data)*100),2)). '%)';
            $chart1_values[] = $value;
        }

        foreach($chart3_data as $label => $value){
           // $chart3_labels[] = $label;
            $chart3_labels[] = $label .' ('. ( round(($value/count($data)*100),2)). '%)';
            $chart3_values[] = $value;
        }
        foreach($chart4_data as $label => $value){
            $chart4_labels[] = $label . '-' .$label+100;
            $chart4_values[] = $value;
        }
        $chart1 = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart1->setData([
            'labels' => $chart1_labels, //['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'Methods',
                    'backgroundColor' => ['rgb(255, 205, 86)', 'rgb(54, 162, 235)', 'rgb(140, 99, 132)', 'rgb(255, 40, 40)' ],
                    //'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $chart1_values, //[0, 10, 5, 2, 20, 30, 45], // ['One' => 0,'Two' => 10,'Three' => 5,'Four' => 2,'Five' => 20,'Six' => 30,'Seven' => 45], //$chart1_data //['1995-08-30 22:00' => 10,  '1995-08-30 22:01' =>5,  '1995-08-30 22:02' => 20,  '1995-08-30 22:03' => 22] //$chart1_data //$chart1_data,
                ],
            ],
        ]);

        $chart1->setOptions([]);

        foreach ($data as $logline){
            $datetime = '1995-08-'.$logline->datetime->day.' '.$logline->datetime->hour.':'.$logline->datetime->minute;
            if(!isset($chart2_data[$datetime])){
                $chart2_data[$datetime] = 0;
            }
            $chart2_data[$datetime] ++;
        }

        $chart2 = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart2->setData([
            //'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'Requests in a minute',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'borderWidth' => 1,
                    'radius' => 0,
                    'data' => $chart2_data,
                ],
            ],
        ]);

        $chart2->setOptions([/*
            'scales'=>[
                'xAxes'=>[
                    'type'=>'time',
                    'time'=>[
                        'displayFormats'
                    ]
                ]
            ],*/
            'elements' =>[
                'point' =>[
                    'radius' => 0
                ]
            ]
        ]);

        $chart3 = $chartBuilder->createChart(Chart::TYPE_PIE);
        $chart3->setData([
            'labels' => $chart3_labels, //['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'Methods',
                    'backgroundColor' => [ 'rgb(30, 200, 30)', 'rgb(40, 150, 40)', 'rgb(40, 100, 40)', 'rgb(255, 40, 40)' ,    'rgb(255, 80, 80)' ,  'rgb(255, 120, 120)', 'rgb(255, 150, 150)','rgb(255, 180, 180)' ],
                    // 'backgroundColor' => 'rgb(255, 99, 132)',
                    //'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $chart3_values //[0, 10, 5, 2, 20, 30, 45],
                ],
            ],
        ]);

        $chart3->setOptions([/*
            'plugins'=>[
                'legend'=>[
                    'position'=>'left',
                ]
            ]
        */]);

        $chart4 = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart4->setData([
            'labels' => $chart4_labels,
            'datasets' => [
                [
                    'label' => 'Document Size < 1000',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $chart4_values,
                ],
            ],
        ]);

        $chart4->setOptions([/* ... */]);


        return $this->render('home/index.html.twig', [
            'chart1' => $chart1,
            'chart2' => $chart2,
            'chart3' => $chart3,
            'chart4' => $chart4,
        ]);

    }
}
