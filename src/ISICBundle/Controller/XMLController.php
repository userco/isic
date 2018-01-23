<?php
// src/Controller/SecurityController.php
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
use ISICBundle\Form\XML1Type;
use Desperado\XmlBundle\Model\XmlPrepare;
use Desperado\XmlBundle\Model\XmlGenerator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
use ISICBundle\Entity\Archive;
use DateTime;

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

    $isic_xml = new Isic();
    $form = $this->createForm(new XML1Type(), $isic_xml);
    $request = $this->get('request');
    if ($request->getMethod() == 'POST'){

	    $isics =$this->getDoctrine()->getRepository('ISICBundle:Isic')->findBy(array('isPublished'=>NULL));
        if(!$isics){
            $session = new Session();
            $session->getFlashBag()->add('error', 'Няма нови данни за обработка.');

            return $this->render(
                'security/xml/generate_xml.html.twig', array(
                'form' => $form->createView(),
            ));
    }

    $log = "";
    $directory = $this->container->getParameter('log_path');
    $generateDate =  new DateTime();
           
    $f1 = $directory . "/log-" . $generateDate->format('Y-m-d_H:i') . '.csv';
    $handle = fopen($f1, 'w');

    $headers = 'Име, ЕГН, Рождена дата, Факултет, Факултетен номер, Специалност, Телефон, Имейл, Чип на картата, Библиотечен номер, Баркод, Тип карта, Статус, Грешки'. "\r\n";   

    fwrite($handle, $headers);
$test = 0;
    $xml = "<?xml version='1.0'?>\r\n<p-file-20>\r\n";

    foreach($isics as $isic){
        $VarIdNumber = $isic->getIDWLID();
        $Names = $isic->getNames();
        $egn = $isic->getEGN();
        $erasym_flag = 0;
        $foreigner_flag = 0;
        
        $em = $this->getDoctrine()->getManager();
        $VarEmail = $isic->getEmail();
        $VarPhoneNumber = $isic->getPhoneNumber();
        $VarPhoneNumber1 = $this->normalize_phone($VarPhoneNumber);
        //**********************************************

        $susi_record = NULL;
        $log = '';

        if($isic->getCardType()->getId()==5){
            $foreigner_flag = 1;
            $facNumber = $isic->getIDWFacultyNumber();
            $fac       = $isic->getIDWFacultyBG();
            $birthdate = $isic->getBirthdate();
            $name      = $isic->getNames();
            //$susi_record_arr = $em->getRepository('ISICBundle:Susi')->findBy(array( 'faculty'=>$fac,'facultyNumber'=>$facNumber, 'birthDate' => $birthdate));
            $susi_record_arr = $em->getRepository('ISICBundle:Susi')->findBy(array('name'=>$name));//,'birthDate' => $birthdate));
            if($susi_record_arr)
                $susi_record =$susi_record_arr[0];
            if(!$susi_record)
            {   
                
                $isic->setIsPublished(1);
                    
                $susi_record_arr = $em->getRepository('ISICBundle:Susi')->findBy(array('faculty'=>"ЕФ"));
                if($susi_record_arr){
                    foreach($susi_record_arr as $susi_data){
                        $fn = $susi_data->getFacultyNumber();
                        $fac_n_arr = explode(" ",$fn);
                        $fac_n = $fac_n_arr[0];
                        if($fac_n == $facNumber){

                            $susi_record = $susi_data;
                            $erasym_flag = 1;
                            //$test= $test + 1;
                            
                            break;
                        }
                        if(strpos($fn, $facNumber)){
                            
                            $susi_record = $susi_data;
                            $erasym_flag = 1;
                            $test= $test + 1;
                            
                            break;
                        }

                    }
                }   
            if(!$susi_record)
            {   
                
                $isic->setIsPublished(1);
                    
                $susi_record_arr = $em->getRepository('ISICBundle:Susi')->findBy(array('faculty'=>$fac));
                if($susi_record_arr){
                    foreach($susi_record_arr as $susi_data){
                        $fn = $susi_data->getFacultyNumber();
                        $fac_n_arr = explode(" ",$fn);
                        $fac_n = $fac_n_arr[0];
                        if($fac_n == $facNumber){

                            $susi_record = $susi_data;
                            //$erasym_flag = 1;
                            //$test= $test + 1;
                            
                            break;
                        }
                        if(strpos($fn, $facNumber)){
                            
                            $susi_record = $susi_data;
                            //$erasym_flag = 1;
                            $test= $test + 1;
                            
                            break;
                        }

                    }
                }
                }  
                if(!$susi_record)
                {   
                    
                    $susi_record_arr = $em->getRepository('ISICBundle:Susi')->findBy(array('faculty'=>$fac,
                        //'facultyNumber'=>$facNumber, 
                        'birthDate' => $birthdate));
                    if($susi_record_arr){
                       $susi_record = $susi_record_arr[0];
                    }    
                    
             
                }   
                if(!$susi_record)
                {   
                    $family_name = $this->getLastName($name);
                    $susi_record_arr = $em->getRepository('ISICBundle:Susi')->findBy(array(//'faculty'=>$fac,
                        'facultyNumber'=>$facNumber, 
                        ));
                    if($susi_record_arr){
                       
                    foreach($susi_record_arr as $susi_data){
                        $susi_name = $susi_data->getName();
                        $susi_family_name = $this->getLastName($susi_name);
                        
                        if($family_name == $susi_family_name){

                            $susi_record = $susi_data;
                            //$erasym_flag = 1;
                            //$test= $test + 1;
                            
                            break;
                        }
                        

                    }
                
                    }    
                    
             
                }   
                if(!$susi_record)
                {   
                    
                    $susi_record_arr = $em->getRepository('ISICBundle:Susi')->findBy(array( 'faculty'=>$fac,'facultyNumber'=>$facNumber, 'birthDate' => $birthdate));
                    if($susi_record_arr){
                       $susi_record = $susi_record_arr[0];
                    }    
                    if(!$susi_record)
                        {   
                            
                            $isic->setIsPublished(1);
                            $isic->setStatus("ERROR");

                            $log = "ERROR: Няма  чуждестранен студент с факултет: ".$fac. ", факултетен номер: ". $facNumber. " и дата на раждане: ".$birthdate. ".;";
                    }
             
                }
            
            }

        }

        //******************************************
        else{
            $susi_record = $em->getRepository('ISICBundle:Susi')->findOneByEgn(array('egn'=>$egn));
        
            //$log = "";

    
            if(!$susi_record)
            {
                
                $isic->setIsPublished(1);
                $isic->setStatus("ERROR");

                $log .= "ERROR: Няма студент с ЕГН: ".$egn. ";";
                    
                    
            }
    }
        if($susi_record){
            $isic->setIsPublished(1);
            
            $susi_names = $susi_record->getName();
            $susi_names = $this->normalize_name($susi_names);
            $susi_names = $this->normalizeName($susi_names);
            $isic_names = $isic->getNames();
            $isic_names = $this->normalize_name($isic_names);
            $isic_names = $this->normalizeName($isic_names);

            if($susi_names != $isic_names && $isic->getCardType()->getId()!=5){
                $isic->setStatus("ERROR");
                $log .= " ERROR: Имена: - СУСИ са ".$susi_names.";";
                 
            }
            elseif($susi_names != $isic_names && $isic->getCardType()->getId()==5){
                if($isic->getStatus()!="ERROR")
                $isic->setStatus("WARNING");
                $log .= " Имена: - СУСИ са ".$susi_names.";";
                 
            }
            $susi_faculty = $susi_record->getFaculty();
            if($susi_faculty && $susi_faculty!=$isic->getIDWFacultyBG()&& $isic->getCardType()->getId()!=5){
                
                $isic->setStatus("ERROR");
                $log .= " ERROR: Фaкултет - СУСИ е ".$susi_record->getFaculty().";";
                 
            }
            else if($susi_faculty && $susi_faculty!=$isic->getIDWFacultyBG()&& $isic->getCardType()->getId()==5){
                if($isic->getStatus()!="ERROR")
                $isic->setStatus("WARNING");
                $log .= " Фaкултет - СУСИ е ".$susi_record->getFaculty().";";
                if($susi_faculty =="ЕФ")
                    $erasym_flag = 1;
                $test++;
                 
            }
            $susi_faculty_number = $susi_record->getFacultyNumber();
            if($isic->getCardType()->getId()!=4 && $susi_faculty_number && $susi_faculty_number !=$isic->getIDWFacultyNumber()&& $isic->getCardType()->getId()!=5){
                
                $isic->setStatus("ERROR");
                $log .= " ERROR: Фaкултетен номер - СУСИ е ".$susi_record->getFacultyNumber().";";
                 
            }
            else if($isic->getCardType()->getId()!=4 && $susi_faculty_number && $susi_faculty_number !=$isic->getIDWFacultyNumber()&& $isic->getCardType()->getId()==5){
                if($isic->getStatus()!="ERROR")
                $isic->setStatus("WARNING");
                $log .= " Фaкултетен номер - СУСИ е ".$susi_record->getFacultyNumber().";";
                 
            }

            if($susi_record->getBirthDate()!=$isic->getBirthdate()&& $isic->getCardType()->getId()!=5){
                $isic->setStatus("ERROR");
                $log .= " ERROR: Рождена дата - СУСИ е ".$susi_record->getBirthDate().";";
                 
            }
            else if($susi_record->getBirthDate()!=$isic->getBirthdate()&& $isic->getCardType()->getId()==5){
                if($isic->getStatus()!="ERROR")
                $isic->setStatus("WARNING");
                $log .= " Рождена дата - СУСИ е ".$susi_record->getBirthDate().";";
            }
            $susi_email = $susi_record->getEmail();

            if($susi_email !=NULL && $susi_email!=$VarEmail){
                if($isic->getStatus()!="ERROR")
                    $isic->setStatus("WARNING");
                    $log .= " Email - СУСИ  е ".$susi_email.";";
                 
                    if(!$VarEmail)
                        $VarEmail = ($susi_email)?$susi_email:"+";
                 
            }
            $susi_phone = $susi_record->getPhoneNumber();
            $susi_phone = $this->normalize_phone($susi_phone);

                

            if( $susi_phone && strlen($susi_phone) == 10  && $susi_phone!=$VarPhoneNumber1){
                if($isic->getStatus()!="ERROR")
                    $isic->setStatus("WARNING");
                 
            
                
                $log .= "Телефон - СУСИ е ".$susi_phone.";";
                if(!$VarPhoneNumber1 || strlen($VarPhoneNumber1)!=10)
                    $VarPhoneNumber = ($susi_phone && strlen($susi_phone)==10)?$susi_phone:"+";
            }
            $VarFacultyName = $susi_record->getFaculty();

                
            $VarFacultyNumber = $susi_record->getFacultyNumber();
            $facultyData = '';
            if($erasym_flag == 0)
                $facultyData = $VarFacultyName.", ".$VarFacultyNumber;
            else if($erasym_flag == 1)
                $facultyData = $isic->getIDWFacultyBG(). ", ".$isic->getIDWFacultyNumber(). ", Еразъм+";
            if($isic->getCardType()->getId()==4){

                    $facultyData = $VarFacultyName;
            }
            if($isic->getStatus()!="ERROR" && $isic->getStatus()!= "WARNING")
                    $isic->setStatus("OK"); 
             //Проверка за трите имена на студента
            $em->persist($isic);
            $em->flush();
        
            if($Names){
                $VarLastName = $this->getLastName($susi_names);
                $VarFirstName = $this->getFirstName($susi_names);
             }
            
            $VarBarCode = $isic->getIDWBarCodeInt();
            
            
            $date1 = $isic->getBirthdate();
            $array =  array();
            $array = explode("-",$date1 );

            $year = $array[0];
            $month= $array[1];
            $day = $array[2];
            $birthdate = $year.$month.$day;
            $gender =$susi_record->getGenderName();
            $address_city = ($susi_record->getAddressCity())?$susi_record->getAddressCity(): "+" ;
            $address_street = ($susi_record->getAddressStreet())?$susi_record->getAddressStreet(): "+";
            $secondPhone = "+";


            if($susi_phone!=$VarPhoneNumber && $susi_phone && strlen($susi_phone)==10){
                $secondPhone = $susi_phone;
            }
            $postCode = ($susi_record->getPostCode())? $susi_record->getPostCode(): "+";

            if($isic->getStatus()!="ERROR"){
                //$test++;
                $xml .= "   <patron-record>"."\r\n";
                $xml .= "       <z303>\r\n";
                $xml .= "           <match-id-type>00</match-id-type>\r\n";
                $xml .= "           <match-id>".$VarIdNumber."</match-id>\r\n";
                $xml .= "           <record-action>A</record-action>\r\n";
                $xml .= "           <z303-id>".$VarIdNumber."</z303-id>\r\n";
                $xml .= "           <z303-user-type>REG</z303-user-type>\r\n";
                $xml .= "           <z303-con-lng>BUL</z303-con-lng>\r\n";
                $xml .= "           <z303-name>".$VarLastName ." ".$VarFirstName."</z303-name>\r\n";
                $xml .= "           <z303-last-name>".$VarLastName."</z303-last-name>\r\n";
                $xml .= "           <z303-first-name>".$VarFirstName."</z303-first-name>\r\n";
                $xml .= "           <z303-title>+</z303-title>\r\n";
                $xml .= "           <z303-delinq-1>+</z303-delinq-1>\r\n";
                $xml .= "           <z303-delinq-n-1>+</z303-delinq-n-1>\r\n";
                $xml .= "           <z303-delinq-3>+</z303-delinq-3>\r\n";
                $xml .= "           <z303-delinq-n-3>+</z303-delinq-n-3>\r\n";
                $xml .= "           <z303-budget>+</z303-budget>\r\n";
                $xml .= "           <z303-profile-id>+</z303-profile-id>\r\n";
                $xml .= "           <z303-ill-library>ILL_CUL</z303-ill-library>\r\n";
                $xml .= "           <z303-home-library>+</z303-home-library>\r\n";
                $xml .= "           <z303-note-1>".$facultyData."</z303-note-1>\r\n";
                $xml .= "           <z303-note-2>20180930</z303-note-2>\r\n";
                $xml .= "           <z303-ill-total-limit>0100</z303-ill-total-limit>\r\n";
                $xml .= "           <z303-ill-active-limit>0100</z303-ill-active-limit>\r\n";
                $xml .= "           <z303-birth-date>".$birthdate."</z303-birth-date>\r\n";
                $xml .= "           <z303-export-consent>N</z303-export-consent>\r\n";
                $xml .= "           <z303-proxy-id-type>00</z303-proxy-id-type>\r\n";
                $xml .= "           <z303-send-all-letters>Y</z303-send-all-letters>\r\n";
                $xml .= "           <z303-plain-html>B</z303-plain-html>\r\n";
                $xml .= "           <z303-want-sms>N</z303-want-sms>\r\n";
                $xml .= "           <z303-title-req-limit>0100</z303-title-req-limit>\r\n";
                $xml .= "           <z303-gender>".$gender."</z303-gender>\r\n";
                $xml .= "           <z303-birthplace>".$address_city."</z303-birthplace>\r\n";
                $xml .= "       </z303>\r\n";
                $xml .= "       <z304>\r\n";
                $xml .= "           <record-action>A</record-action>\r\n";
                $xml .= "           <z304-id>".$VarIdNumber."</z304-id>\r\n";
                $xml .= "           <z304-sequence>01</z304-sequence>\r\n";
                $xml .= "           <z304-address-0>".$VarLastName ." ".$VarFirstName."</z304-address-0>\r\n";
                $xml .= "           <z304-address-1>".$address_city."</z304-address-1>\r\n";
                $xml .= "           <z304-address-2>".$address_street."</z304-address-2>\r\n";
                $xml .= "           <z304-address-3>+</z304-address-3>\r\n";
                $xml .= "           <z304-address-4>+</z304-address-4>\r\n";
                $xml .= "           <z304-zip>".$postCode."</z304-zip>\r\n";
                $xml .= "           <z304-email-address>".$VarEmail."</z304-email-address>\r\n";
                $xml .= "           <z304-telephone>".$VarPhoneNumber1."</z304-telephone>\r\n";
                $xml .= "           <z304-date-from>+</z304-date-from>\r\n";
                $xml .= "           <z304-date-to>+</z304-date-to>\r\n";
                $xml .= "           <z304-address-type>01</z304-address-type>\r\n";
                $xml .= "           <z304-telephone-2>".$secondPhone."</z304-telephone-2>\r\n";
                $xml .= "       </z304>\r\n";
                $xml .= "       <z305>\r\n";
                $xml .= "           <record-action>A</record-action>\r\n";
                $xml .= "           <z305-id>".$VarIdNumber."</z305-id>\r\n";
                $xml .= "           <z305-sub-library>LSU50</z305-sub-library>\r\n";
                $xml .= "           <z305-bor-type>+</z305-bor-type>\r\n";
                $xml .= "           <z305-bor-status>01</z305-bor-status>\r\n";
                $xml .= "           <z305-registration-date>20171001</z305-registration-date>\r\n";
                $xml .= "           <z305-expiry-date>20180930</z305-expiry-date>\r\n";
                $xml .= "           <z305-note>+</z305-note>\r\n";
                $xml .= "           <z305-delinq-1>+</z305-delinq-1>\r\n";
                $xml .= "           <z305-delinq-n-1>+</z305-delinq-n-1>\r\n";
                $xml .= "           <z305-field-1>+</z305-field-1>\r\n";
                $xml .= "           <z305-field-2>+</z305-field-2>\r\n";
                $xml .= "           <z305-field-3>+</z305-field-3>\r\n";
                $xml .= "       </z305>\r\n";
                $xml .= "       <z308>\r\n";
                $xml .= "           <record-action>A</record-action>\r\n";
                $xml .= "           <z308-key-type>00</z308-key-type>\r\n";
                $xml .= "           <z308-key-data>".$VarIdNumber."</z308-key-data>\r\n";
                $xml .= "           <z308-verification>".$VarIdNumber."</z308-verification>\r\n";
                $xml .= "           <z308-verification-type>00</z308-verification-type>\r\n";
                $xml .= "           <z308-status>AC</z308-status>\r\n";
                $xml .= "           <z308-encryption>H</z308-encryption>\r\n";
                $xml .= "       </z308>\r\n";
                $xml .= "       <z308>\r\n";
                $xml .= "           <record-action>A</record-action>\r\n";
                $xml .= "           <z308-key-type>01</z308-key-type>\r\n";
                $xml .= "           <z308-key-data>".$VarBarCode."</z308-key-data>\r\n";
                $xml .= "           <z308-verification>".$VarBarCode."</z308-verification>\r\n";
                $xml .= "           <z308-verification-type>00</z308-verification-type>\r\n";
                $xml .= "           <z308-status>AC</z308-status>\r\n";
                $xml .= "           <z308-encryption>H</z308-encryption>\r\n";
                $xml .= "       </z308>\r\n";
                $xml .= "   </patron-record>\r\n";
                        
    }
}

    if($isic->getStatus()=="ERROR")
        $errorCount++;
    if($isic->getStatus()=="WARNING")
        $warningCount++;
    if($isic->getStatus()=="OK")
        $okCount++;

    $out = array(

        $Names,
        $egn,
        $isic->getBirthdate(),// $month."/".$day."/".$year,
        $isic->getIDWFacultyBg(),
        $isic->getIDWFacultyNumber(),
        $isic->getSpecialty(),
        $isic->getPhoneNumber(),//$VarPhoneNumber1,
        $isic->getEmail(),
        $isic->getChipNumber(),
        $isic->getIDWLID(),
        $isic->getIDWBarCodeInt(),
        $isic->getCardType()->getName(),
        $isic->getStatus(),
        $log
        );


    fputcsv($handle, $out, ","); 
        }   
    $xml .= "</p-file-20>";
  
  //var_dump($test);
  //die();
    $fs = new \Symfony\Component\Filesystem\Filesystem();
    $xmlFileName = $this->container->getParameter('path').'/xml-'.$generateDate->format('Y-m-d_H:i:s').'.xml';
    $fs->dumpFile($xmlFileName, $xml);
    $errors = 'ERRORS(Записи - не са включени в XML файла): '.$errorCount.',
               WARNINGS(Записи, включени в XML файла)'.$warningCount. ',
                OK (Записи, включени в XML файла)'.$okCount;
    $fs3 = new \Symfony\Component\Filesystem\Filesystem();
    $errorFileName = $this->container->getParameter('log_path').'/errors-'.$generateDate->format('Y-m-d_H:i').'.txt';
    $fs3->dumpFile($errorFileName, $errors);
     
    $zip = new \ZipArchive();
   
    $zipName0 = 'Documents-'.$generateDate->format('Y-m-d_H:i:s').".zip";
    $zipName = $this->container->getParameter('zip_path').'/'.$zipName0;
    $zip->open($zipName,  \ZipArchive::CREATE);
    
   
    $f2= $this->container->getParameter('path').'/xml.xml';
    $f3= $this->container->getParameter('log_path').'/errors.txt';

    $zip->addFromString(basename($f1),  file_get_contents($f1));
    $zip->addFromString(basename($xmlFileName),  file_get_contents($xmlFileName));
    $zip->addFromString(basename($errorFileName),  file_get_contents($errorFileName));  
    $zip->close();

    $archive = new \ISICBundle\Entity\Archive();
    
    $archive->setGenerateDate($generateDate->format('Y-m-d'));
    $archive->setArchiveName($zipName0);
    $em->persist($archive);
    $em->flush();
    
    if(file_exists($xmlFileName)){
        $response = new BinaryFileResponse($zipName);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
       
        return $response;
    }
    else{
    $session = new Session();
    $session->getFlashBag()->add('error', 'Няма нови данни за обработка.');
    }
  
}
    return $this->render(
            'security/xml/generate_xml.html.twig',array(
           'form' => $form->createView(),
        ));
    }
}
