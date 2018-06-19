<?php
//автор Мария Пенелова
namespace  ISICBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use ISICBundle\Entity\Isic;
use ISICBundle\Entity\Role;
use ISICBundle\Form\RoleType;
use ISICBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Session\Session;
use ISICBundle\Form\XML1Type;
use Desperado\XmlBundle\Model\XmlPrepare;
use Desperado\XmlBundle\Model\XmlGenerator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
use ISICBundle\Entity\Archive;
use ISICBundle\Jobs\XMLJob;
use DateTime;
use BCC\ResqueBundle\Job\Resque_Job_Status;

class XMLController extends Controller
{

    /**
     * @Route("/generate_xml", name="generate_xml")
     */
public function generateXMLAction(Request $request)
{ 

    $errorCount = 0;
    $warningCount = 0;
    $okCount = 0;
    $processed = false;

    $isic_xml = new Isic();
    $form = $this->createForm(new XML1Type(), $isic_xml);
    $request = $this->get('request');
    $session = new Session();
    if(null ==$session->get('flag'))
        $session->set('flag', 0);
    $em = $this->getDoctrine()->getManager();
    $isics = $em->getRepository('ISICBundle:Isic')->findBy(array('isPublished'=>NULL));
    $isics2 = $em->getRepository('ISICBundle:Isic')->findBy(array('isPublished'=>1));
    if($session->get('flag') == 1){
            
            $session->getFlashBag()->add('error', 'Файлът се генерира...');
         }
    if(!$isics){
    $session->set('flag', 0);

    $session->getFlashBag()->add('error', 'Моля, вземете последния генериран файл от търсачката с XML архивите.');
}
    if ($request->getMethod() == 'POST'){
        $isics = $em->getRepository('ISICBundle:Isic')->findBy(array('isPublished'=>NULL));
        if(!$isics){
            $session->getFlashBag()->add('error', 'Няма нови данни за обработка.');
            $session->set('flag', 0);
            return $this->render(
                'security/xml/generate_xml.html.twig', array(
                'form' => $form->createView(),
            ));
	   
}
$session->set('flag', 1);
$session->getFlashBag()->add('error', 'Файлът се генерира...');
$resque = $this->get('bcc_resque.resque');


$job = new XMLJob();


$job->args = array();

// enqueue your job
$resque->enqueue($job);


}
    return $this->render(
            'security/xml/generate_xml.html.twig',array(
           'form' => $form->createView(),
        ));
    
}
}
