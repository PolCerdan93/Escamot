<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Blog;
use App\Entity\Contingut;
use Doctrine\Persistence\ManagerRegistry;

class VistapreviaController extends AbstractController
{
    
    public function index(Blog $entrada): Response
    {
        return $this->render('vistaprevia/index.html.twig', [
            'objentrada' => $entrada
        ]);
    }

    public function guardarentrada(Blog $entrada, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $entrada->setFinalitzada(true);
        $em->persist($entrada);
        $em->flush();
        
        return $this->redirectToRoute('Blog'); 
    }

    public function publicarentrada(Blog $entrada, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $entrada->setFinalitzada(true);
        $entrada->setActivada(true);
        $em->persist($entrada);
        $em->flush();
        
        return $this->redirectToRoute('index', []); 
    }

    
}
