<?php

//автор Мария Пенелова
namespace  ISICBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use ISICBundle\Entity\Isic;
use ISICBundle\Entity\Role;
use ISICBundle\Form\RoleType;
use ISICBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Session\Session;
use ISICBundle\Form\XML2Type;
use Desperado\XmlBundle\Model\XmlPrepare;
use Desperado\XmlBundle\Model\XmlGenerator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
use ISICBundle\Entity\Archive;
use DateTime;
use ISICBundle\Entity\PersonalNumber;
use ISICBundle\Jobs\CSVJob;

class GenerateFileController extends Controller
{

    /**
     * @Route("/generate_file", name="generate_file")
     */
public function generateFileAction(Request $request)
{ 

    $personalNumber = new PersonalNumber();
    $form = $this->createForm(new XML2Type(), $personalNumber);
    $request = $this->get('request');
    $isics =$this->getDoctrine()->getRepository('ISICBundle:PersonalNumber')->findBy(array('isPublished'=>NULL));
    $session = new Session();
    if(null ==$session->get('flag'))
        $session->set('flag', 0);
    if($session->get('flag') == 1){
            
            $session->getFlashBag()->add('error', 'Файлът се генерира...');
    }
    if(!$isics){
        $session->set('flag', 0);

        $session->getFlashBag()->add('error', 'Моля, вземете последния генериран файл от търсачката с CSV архивите.');
    }
    $em = $this->getDoctrine()->getManager();
    if ($request->getMethod() == 'POST'){

	    $isics =$this->getDoctrine()->getRepository('ISICBundle:PersonalNumber')->findBy(array('isPublished'=>NULL));
        if(!$isics){
            $session = new Session();
            $session->getFlashBag()->add('error', 'Няма нови данни за обработка.');

            return $this->render(
                'security/generateFile/generate_file.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    $session->set('flag', 1);
    $session->getFlashBag()->add('error', 'Файлът се генерира...');

    $resque = $this->get('bcc_resque.resque');


    $job = new CSVJob();


    $job->args = array();

    // enqueue your job
    $resque->enqueue($job);
    
   
        // $response = new BinaryFileResponse($zipName);
        // $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
       
        // return $response;
    }
    
  

    return $this->render(
            'security/generateFile/generate_file.html.twig',array(
           'form' => $form->createView(),
        ));
    }
}
