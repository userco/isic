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
private function my_mb_ucfirst($str) {
    $str = mb_convert_case($str, MB_CASE_LOWER, "UTF-8"); 
    $fc = mb_strtoupper(mb_substr($str, 0, 1));
    return $fc.mb_substr($str, 1);
}

private function getFirstName($Names){
    $name_array = explode(' ', $Names);
    
    $firstname="";
    if(count($name_array)<2) return $firstname;
    if(count($name_array)==3)
        $firstname = $name_array[0]." ".$name_array[1];
   
    if(count($name_array)==2)
        $firstname = $name_array[0];
    return $firstname;
}

private function getLastName($Names){
    $name_array = explode(' ', $Names);
    $lastname = "";
    if(count($name_array)<2) return $lastname;
    if(count($name_array)==3)
        $lastname = $name_array[2];

    else $lastname = $name_array[1];
    return $lastname;
}

private function normalizeName($Names){
    $name_array = explode(' ', $Names);
    $firstName = $name_array[0];
    if(count($name_array)< 2)
        return $this->my_mb_ucfirst($firstName); 
    else{
        if(count($name_array)== 2){
            $lastName = $name_array[1];
        
        $name = $this->my_mb_ucfirst($firstName). " ". $this->my_mb_ucfirst($lastName);
        return $name;
    }
    if(count($name_array)== 3){
            $secondName = $name_array[1];
            $lastName = $name_array[2];
        $name = $this->my_mb_ucfirst($firstName). " ". $this->my_mb_ucfirst($secondName). " ". $this->my_mb_ucfirst($lastName);
        return $name;
    }
    }
    return;
}
private function getBirthDate($birthdate){
    $array = explode('/', $birthdate);
    $date_string = $array[2].$array[1].$array[0];
    return $date_string;
}

private function normalize_phone($gsm) {
    $gsmSanitized = preg_replace('/\s+|\(|\)/', '', $gsm);
    $gsmSanitized = preg_replace('/^\+359/', '0', $gsmSanitized);
    $gsmSanitized = preg_replace('/^00359/', '0', $gsmSanitized);
    //$gsmSanitized = preg_replace('.', '', $gsmSanitized);
    $first_char = mb_substr($gsmSanitized, 0, 1);
    
    if($first_char != "0")
        $gsmSanitized = "0".$gsmSanitized;

    return $gsmSanitized;
}

private function normalize_name($name) {
    $name1 = preg_replace('/\s+/', ' ', $name);
    return $name1;
}

private function normalize_date($date) {
    //$day = substr($date,3,2);
    //$month = substr($date,0,2);
    //$year = substr($date,6,4);
    $date1 = preg_replace('/\//', '/', $date);
    //return $day . "." . $month . "." . $year;
    return $date1;
}
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

    $em = $this->getDoctrine()->getManager();
    $isics = $em->getRepository('ISICBundle:Isic')->findBy(array('isPublished'=>NULL));
    $isics2 = $em->getRepository('ISICBundle:Isic')->findBy(array('isPublished'=>1));
    if($isics && $isics2){
            $session = new Session();
            $session->getFlashBag()->add('error', 'Файлът се генерира...');
         }
    if(!$isics){
    $session = new Session();
    $session->getFlashBag()->add('error', 'Моля, вземете последния генериран файл от търсачката с XML архивите.');
}
    if ($request->getMethod() == 'POST'){

	 
        $isics = $em->getRepository('ISICBundle:Isic')->findBy(array('isPublished'=>NULL));

        if(!$isics){
            $session = new Session();
            $session->getFlashBag()->add('error', 'Няма нови данни за обработка.');

            return $this->render(
                'security/xml/generate_xml.html.twig', array(
                'form' => $form->createView(),
            ));

}
$session = new Session();
$session->getFlashBag()->add('error', "Файлът се генерира...");    
$resque = $this->get('bcc_resque.resque');


$job = new XMLJob();


$job->args = array(
    //'container'    => $this->container,
    

);

// enqueue your job
 $token = $resque->enqueue($job);


}
    return $this->render(
            'security/xml/generate_xml.html.twig',array(
           'form' => $form->createView(),
        ));
    
}
}
