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

class GenerateFileController extends Controller
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
     * @Route("/generate_file", name="generate_file")
     */
public function generateFileAction(Request $request)
{ 

    $personalNumber = new PersonalNumber();
    $form = $this->createForm(new XML2Type(), $personalNumber);
    $request = $this->get('request');
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

    $log = "";
    $directory = $this->container->getParameter('log_path');
    $generateDate =  new DateTime();
           
    $f1 = $directory . "/log-egn" . $generateDate->format('Y-m-d_H:i') . '.csv';
    $handle = fopen($f1, 'w');

    $headers = 'ЕГН, Име, Телефон, Имейл, Факултет, Специалност, Курс, Форма на обучение, Факултетен номер, Чип на картата, Библиотечен номер, Баркод,  Статус'. "\r\n";   

    fwrite($handle, $headers);
    $test = 0;
    

    foreach($isics as $isic){
       
        $susi_record = NULL;
        $log = '';

        $egn = $isic->getPersonalNumber();
            $susi_record = $em->getRepository('ISICBundle:Susi')->findOneByEgn(array('egn'=>$egn));
        
            //$log = "";

    
            if(!$susi_record)
            {
                
                $isic->setIsPublished(1);
                $isic->setStatus("ERROR");
                

                    $log .= "ERROR: Няма студент с ЕГН: ".$egn. ";";
            }    
                    
            
            $susi_names = "";
            $susi_phone = "";
            $susi_email = "";
            $VarFacultyName = "";
            $speciality = "";
            $course = "";
            $educationalTypeName = "";
            $VarFacultyNumber = "";      
            
        if($susi_record){
            
    
            $isic->setIsPublished(1);
            $egn = $isic->getPersonalNumber();

            $susi_names = $susi_record->getName();
            $susi_names = $this->normalize_name($susi_names);
            $susi_names = $this->normalizeName($susi_names);
            

           
            $susi_faculty_number = $susi_record->getFacultyNumber();
            
            $susi_email = $susi_record->getEmail();

            
            $susi_phone = $susi_record->getPhoneNumber();
            $susi_phone = $this->normalize_phone($susi_phone);
            $speciality = $susi_record->getSpeciality();
            $course = $susi_record->getCourse();
            $educationalTypeName = $susi_record->getEducationalTypeName();
                

            
            $VarFacultyName = $susi_record->getFaculty();

                
            $VarFacultyNumber = $susi_record->getFacultyNumber();
            
        }
            
            
            
            


    

    $out = array(
        $egn,
        $susi_names,
        $susi_phone,
        $susi_email,
        $VarFacultyName,
        $speciality,
        $course,
        $educationalTypeName,
        $VarFacultyNumber
        );


    fputcsv($handle, $out, ","); 
        //}   
   
  }
    
   
     
    $zip = new \ZipArchive();
   
    $zipName0 = 'Documents-'.$generateDate->format('Y-m-d_H:i:s').".zip";
    $zipName = $this->container->getParameter('zip_path').'/'.$zipName0;
    $zip->open($zipName,  \ZipArchive::CREATE);
    
   
    

    $zip->addFromString(basename($f1),  file_get_contents($f1));
   
    $zip->close();

    $archive = new \ISICBundle\Entity\Archive();
    
    $archive->setGenerateDate($generateDate->format('Y-m-d'));
    $archive->setArchiveName($zipName0);
    $em->persist($archive);
    $em->flush();
    
   
        $response = new BinaryFileResponse($zipName);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
       
        return $response;
    }
    
  

    return $this->render(
            'security/generateFile/generate_file.html.twig',array(
           'form' => $form->createView(),
        ));
    }
}