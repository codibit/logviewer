<?php

namespace App\Controller;
use App\logfileProcessor;
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

    #[Route('/logfile/process', name: 'logfileProcess')]
    public function process(logfileProcessor $logfileProcessor): Response
    {

        try{
            $logfileProcessor->process();
        } catch (Exception $e){
            echo 'Sorry, the following error happened while processing the file: ',  $e->getMessage(), "\n";
        }

        return $this->render('logfile/processed.html.twig');
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
