<?php

namespace App\Controller;

use App\Entity\Manifest;
use App\Entity\Blog;
use App\Entity\Newsletter;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Safe\DateTime;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class DefaultController extends AbstractController
{
    /**
     * @Route("/default", name="app_default")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $manifest_repo = $doctrine->getRepository(Manifest::class);
        $blog_repo = $doctrine->getRepository(Blog::class);

        $lastUpdate = $manifest_repo->findBy(array(),array('id'=>'DESC'),1,0);

        $all_news = $blog_repo->findBy(['activada'=>true],['id' => 'DESC']);
     
        if(count($lastUpdate)==0){
            $lastUpdate = "Encara no existeix manifest";
        }else{
            $lastUpdate = $lastUpdate[0]->getContent();
        }

        $newsletter = new Newsletter();

        $form = $this->createFormBuilder($newsletter)
            ->add('email', EmailType::class)
            ->add('save', ButtonType::class, ['label' => "subscriure'm a la newsletter"])
            ->getForm();
        
        return $this->render('default/index.html.twig', [
            'LatestUpdate' => $lastUpdate,
            'TotesNoticies' => $all_news,
            'newsletter' => $form->createView()
        ]);
    }

    public function registreNewsletter(Request $request,ManagerRegistry $doctrine): Response
    {


        if ($request->isMethod('POST')) {
            $newsletter = new Newsletter();
            $newsletter->setEmail($request->request->get('mail'));
            $newsletter->setDataAlta(new DateTime());
            $newsletter->setActiu(TRUE);
            $newsletter->setBaixa(FALSE);
            $newsletter->setToken(random_bytes(40));
            $em = $doctrine->getManager();
            $em->persist($newsletter);
            $em->flush();     
        }



        return new Response(Response::HTTP_OK);
    }

}
