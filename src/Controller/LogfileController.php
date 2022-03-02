<?php

namespace App\Controller;
use App\logfileProcessor;
use App\Entity\Logfile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class LogfileController extends AbstractController
{

    #[Route('/logfile', name: 'logfile')]
    public function index(): Response
    {
        return $this->render('logfile/index.html.twig', [
            'controller_name' => 'LogfileController',
        ]);
    }

    #[Route('/logfile/process/{id}', name: 'logfileProcess')]
    public function process(Logfile $logfile, logfileProcessor $logfileProcessor): Response
    {

        try{
            $logfileProcessor->process($logfile);
        } catch (Exception $e){
            echo 'Sorry, the following error happened while processing the file: ',  $e->getMessage(), "\n";
        }

        return $this->render('logfile/processed.html.twig',
        [
        'logfile' => $logfile
        ]);
    }

    #[Route('/logfile/getJson/{id}', name: 'jsonfile')]
    public function jsonfile(Logfile $logfile): Response
    {
        $filepath =  '/var/storage/'.$logfile->getJson();

        return new Response(file_get_contents($this->getParameter('kernel.project_dir') . $filepath),
            Response::HTTP_OK,
            ['content-type' => 'application/json']);

    }
}
