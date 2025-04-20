<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Contingut;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\BlogtemplateType;
use Safe\DateTime;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;



class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="app_blog")
     */
    public function index(Request $request, UserInterface $User, ManagerRegistry $doctrine): Response
    {
        

        $Blog = new Blog();
        $form = $this->createForm(BlogtemplateType::class, $Blog);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $entradesDelBlogAEliminar = $doctrine->getRepository(Blog::class)->findBy([
                'finalitzada' => 0,
                'user' => $User
            ]);
            for($i=0;$i<count($entradesDelBlogAEliminar);$i++){
                $em->remove($entradesDelBlogAEliminar[$i]);
            }
            $em->flush();
            
            $dataPublicacio = new DateTime();
            $Blog->setDataPublicació($dataPublicacio); 
            $Blog->setActivada(false);
            $Blog->setFinalitzada(false);
            $Blog->setUser($User);
            
            $em->persist($Blog);
            $em->flush();

            return $this->redirectToRoute('entradastep2', [
                'id' => $Blog->getId(),
            ]); 
        }
        $repository = $doctrine->getRepository(Blog::class);
        $currentEntrades = $repository->findBy( 
            ['finalitzada' => 1],
            ['id' => 'DESC'],
        );
        return $this->render('blog/index.html.twig', [
            'form' => $form->createView(),
            'currentEntrades' => $currentEntrades,
        ]);

    }

    public function entradasegonpas(Blog $entrada,Request $request,ManagerRegistry $doctrine,SluggerInterface $slugger){
       
        if ($request->getMethod() == Request::METHOD_POST){
            
            $em = $doctrine->getManager();
            $contingutRepo = $doctrine->getRepository(Contingut::class);
            $contingutsbbdd = $contingutRepo->findBy([
                'entradaid' => $entrada->getId()    
            ],[
                'ordre' => 'ASC'
            ]);
           
            $error = false;

            if($entrada->getTemplatetype()=="Plantilla1"){

            
                $contingut = array();
                $contingut[0] = $request->request->get('_Primertext');
                $contingut[1] = $request->files->get('_PrimeraImatge');
                
                $contingut[2] = $request->files->get('_SegonaImatge');
                $contingut[3] = $request->request->get('_Segontext');
              
                if (strlen($contingut[0]) == 0 || strlen(trim($contingut[0])) == 0){
                    $session = new Session();
                    $session->getFlashBag()->add('note','no has introduit el primer text');
                    $error = true;
                }else{
                    //comprovo primer que no hi hagi un text
                    if(isset($contingutsbbdd)){
                        $this->borrarcontentifexists($contingutsbbdd,$entrada, $em,0);
                    }
                    // si no hi ha error grabo a la BBDD
                    $this->gravarcontingut($contingut[0],$entrada,$em,0);
                }
                if (!isset($contingut[1]) && !isset($contingutsbbdd[1])) {
                    $session = new Session();
                    $session->getFlashBag()->add('note','no has seleccionat la primera imatge');
                    $error = true;
                }
                if (strlen($contingut[3]) == 0 || strlen(trim($contingut[3])) == 0) {
                    $session = new Session();
                    $session->getFlashBag()->add('note','no has introduit el segon text');
                    $error = true;
                }else{
                    if(isset($contingutsbbdd)){
                        $this->borrarcontentifexists($contingutsbbdd,$entrada, $em,3);
                    }
                    $this->gravarcontingut($contingut[3],$entrada,$em,3);
                }

                if (!isset($contingut[2]) && !isset($contingutsbbdd[2])) {
                    $session = new Session();
                    $session->getFlashBag()->add('note','no has seleccionat la segona imatge');
                    $error = true;
                }

                //he de borrar sempre i quant el text nou no estigui en blanc


                
                if (isset($contingut[1])) {
                    if(isset($contingutsbbdd)){
                        $this->borrarcontentifexists($contingutsbbdd,$entrada, $em,1);
                        $this->buscarimatgeEliminar($contingutsbbdd,$entrada,$em,1);
                    }
                    $this->crearnomimatgeimoure($contingut,$entrada,1,$slugger);
                    $this->gravarcontingut($contingut[1],$entrada,$em,1);
                }

                if (isset($contingut[2])) {
                    if(isset($contingutsbbdd)){
                        $this->borrarcontentifexists($contingutsbbdd,$entrada, $em,2);
                        $this->buscarimatgeEliminar($contingutsbbdd,$entrada,$em,2);
                    }
                    $this->crearnomimatgeimoure($contingut,$entrada,2,$slugger);
                    $this->gravarcontingut($contingut[2],$entrada,$em,2);
                }
               
                
                $em->flush();
                
                if(!$error){
                    return $this->redirectToRoute('vistaprevia', [
                        'id' => $entrada->getId(),
                    ]); 
                }
                
            }
            elseif($entrada->getTemplatetype()=="Plantilla2"){
                
            }
        }
        
        $contingut_repo = $doctrine->getRepository(Contingut::class);
        $continguts = $contingut_repo->findBy([
            'entradaid' => $entrada->getId()
        ],[
            'ordre' => 'ASC'
        ]);

        return $this->render('blog/segonpas.html.twig', [
            'EntradaBlogIdActual' => $entrada->getId(),
            'plantilla' => $entrada->getTemplatetype(),
            'continguts' => $continguts
        ]);
        
    }

    public function borrar($content,$entrada,$em): void
    {
        $em->remove($content);
        $em->flush();   
    }
    
    public function borrarcontentifexists($content,$entrada, &$em,$posicio): void
    {
        for($i=0;$i<count($content);$i++){
            if($content[$i]->getOrdre() == $posicio){
                $this->borrar($content[$i],$entrada, $em);  
            }
        }
    }

    public function gravarcontingut($contingut,$entrada,&$em,$posicio){
        $contingutpujadaentrada = new Contingut();
        $contingutpujadaentrada->setContingut($contingut);
        $contingutpujadaentrada->setOrdre($posicio);
        $contingutpujadaentrada->setEntradaid($entrada);
        $em->persist($contingutpujadaentrada);
    }

    public function buscarimatgeEliminar($contingutsbbdd,$entrada,&$em,$posicio){
        for($i=0;$i<count($contingutsbbdd);$i++){
            if($contingutsbbdd[$i]->getOrdre() == $posicio){
                $this->eliminarimatge($contingutsbbdd[$i]->getContingut());
            }
        }
    }

    public function eliminarimatge($pathaeliminar){
        $filesystem = new Filesystem();
        $path = $this->getParameter('kernel.project_dir') . '\public\uploads\images\\'; 
        if($filesystem->exists($path)){
            $filesystem->remove([$path, $pathaeliminar]);
        }
    }

    public function crearnomimatgeimoure(&$contingut,&$entrada,$posicio,&$slugger){

        $originalFilename = pathinfo($contingut[$posicio]->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $entrada->getId() . '-' . $posicio . '-' . $safeFilename.'-'.uniqid().'.'.$contingut[$posicio]->guessExtension();

        $contingut[$posicio]->move(
            $this->getParameter('images_directory'),
            $newFilename
        );
        $contingut[$posicio] = $newFilename;
    }

    
}
