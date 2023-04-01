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

        return $this->render('blog/index.html.twig', [
            'form' => $form->createView()
        ]);

    }

    public function entradasegonpas(Blog $entrada,Request $request,ManagerRegistry $doctrine,SluggerInterface $slugger){
       
        if ($request->getMethod() == Request::METHOD_POST){
            
            $em = $doctrine->getManager();
            $contingutRepo = $doctrine->getRepository(Contingut::class);
            $continguts = $contingutRepo->findBy([
                'entradaid' => $entrada->getId()    
            ]);
            if($continguts){
                for($i=0;$i<count($continguts);$i++){
                    $em->remove($continguts[$i]);
                }
                $em->flush();
                
            }

            $filesystem = new Filesystem();
            $path = $this->getParameter('kernel.project_dir') . '\public\uploads\images\\'; 
            if($filesystem->exists($path)){
    
                $filesystem->remove([$path, $entrada->getId().'*.*']);
            }

            if($entrada->getTemplatetype()=="Plantilla1"){

            
                $contingut = array();
                $contingut[0] = $request->request->get('_Primertext');
                $contingut[1] = $request->files->get('_PrimeraImatge');
                $contingut[2] = $request->request->get('_Segontext');
                $contingut[3] = $request->files->get('_SegonaImatge');

                $error = false;

                if (strlen($contingut[0]) > 0 && strlen(trim($contingut[0])) == 0){
                    $session = new Session();
                    $session->getFlashBag()->add('note','no has introduit el primer text');
                    $error = true;
                }
                if (!$contingut[1] && $continguts[1]=="") {
                    $session = new Session();
                    $session->getFlashBag()->add('note','no has seleccionat la primera imatge');
                    $error = true;
                }
                if (strlen($contingut[2]) > 0 && strlen(trim($contingut[2])) == 0) {
                    $session = new Session();
                    $session->getFlashBag()->add('note','no has introduit el segon text');
                    $error = true;
                }

                if (!$contingut[3]) {
                    $session = new Session();
                    $session->getFlashBag()->add('note','no has seleccionat la segona imatge');
                    $error = true;
                }


                
                if ($contingut[1]) {
                    $originalFilename = pathinfo($contingut[1]->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $entrada->getId() . '-' . $safeFilename.'-'.uniqid().'.'.$contingut[1]->guessExtension();

                    try {
                        $contingut[1]->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                        $contingut[1] = $newFilename;
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                }

                if ($contingut[3]) {
                    $originalFilename = pathinfo($contingut[3]->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $entrada->getId() . '-' . $safeFilename.'-'.uniqid().'.'.$contingut[3]->guessExtension();

                    try {
                            $contingut[3]->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                        $contingut[3] = $newFilename;
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                }
                for($i=0;$i<count($contingut);$i++){
                    if($contingut[$i]){
                        $contingutpujadaentrada = new Contingut();
                        $contingutpujadaentrada->setContingut($contingut[$i]);
                        $contingutpujadaentrada->setOrdre($i);
                        $contingutpujadaentrada->setEntradaid($entrada);
                        $em->persist($contingutpujadaentrada);
                    }
                }
                $em->flush();
                
                if(!$error){
                    echo "totok";exit;
                    //return $this->redirectToRoute('vistaprevia', [
                    //    'id' => $entrada->getId(),
                    //]); 
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


    public function vistaprevia(Blog $entrada): Response
    {
        
       

        
        return $this->render('blog/vistaprevia.html.twig', [
            'objentrada' => $entrada
        ]);

    }
}
