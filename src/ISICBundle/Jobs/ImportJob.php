<?php

namespace ISICBundle\Jobs;

use BCC\ResqueBundle\Job;

class ImportJob extends Job
{
    public function run($args)
    {
        $doctrine = $this->getContainer()->getDoctrine();
          $isic_xml = new Isic();
        
        
    	$isics =$this->getDoctrine()->getRepository('ISICBundle:Isic')->findAll();
        $log = "";
        $xml = "<?xml version='1.0'?>
                    <p-file-20>";
        foreach($isics as $isic){
            $VarIdNumber = $isic->getIDWLIDBack();
            $Names = $isic->getNames();
            $egn = $isic->getEGN();

            $em = $this->getDoctrine()->getManager();

            $susi_record = $em->getRepository('ISICBundle:Susi')->findOneByEgn(array('egn'=>$egn));

            if(!$susi_record)
            {
                //$isic->setIsPublished(1);
                $isic->setStatus("ERROR");

                $log .= "ERROR: Няма студент с ЕГН: ".$egn. "\n\n";
                continue;
            }
            if($susi_record){

                $VarEmail = $isic->getEmail();
                $VarPhoneNumber = $isic->getPhoneNumber();

                if($susi_record->getEmail()!=$VarEmail){
                    //$isic->setIsPublished(1);
                    $isic->setStatus("WARNING");
                     $log .= "WARNING: Email-ът на студентa с ЕГН: ".$egn. " е ".$VarEmail.".\n\n";
                }

                if($susi_record->getPhoneNumber()!=$VarPhoneNumber){
                    
                    //$isic->setIsPublished(1);
                    $isic->setStatus("WARNING");
                     $log .= "WARNING: Телефонът на студентa с ЕГН: ".$egn. " е ".$VarPhoneNumber.".\n\n";
                }

                 //Проверка за трите имена на студента

            }
            if($Names){
            $VarLastName = $this->getFirstName($Names);
            $VarFirstName = $this->getLastName($Names);
             }
            $VarEmail = $isic->getEmail();
            $VarPhoneNumber = $isic->getPhoneNumber();
            $VarBarCode = $isic->getIDWBarCodeInt();
            $VarFacultyName = $susi_record->getFaculty();
            $VarFacultyNumber = $susi_record->getFacultyNumber();
            $birthdate = $isic->getBirthDate();
            $gender =$susi_record->getGenderName();
            $address_city = $susi_record->getAddressCity();
            $address_street = $susi_record->getAddressStreet();
            $postCode = ($susi_record->getPostCode())? $susi_record->getPostCode(): "+";
            $xml .= "<patron-record>
                    <z303>
                    <match-id-type>00</match-id-type>
                    <match-id>".$VarIdNumber."</match-id>
                    <record-action>A</record-action>
                    <z303-id>".$VarIdNumber."</z303-id>
                    <z303-user-type>REG</z303-user-type>
                    <z303-con-lng>BUL</z303-con-lng>
                    <z303-name>".$VarLastName ." ".$VarFirstName."</z303-name>
                    <z303-last-name>".$VarLastName."</z303-last-name>
                    <z303-first-name>".$VarFirstName."</z303-first-name>
                    <z303-title>+</z303-title>
                    <z303-delinq-1>+</z303-delinq-1>
                    <z303-delinq-n-1>+</z303-delinq-n-1>
                    <z303-delinq-3>+</z303-delinq-3>
                    <z303-delinq-n-3>+</z303-delinq-n-3>
                    <z303-budget>+</z303-budget>
                    <z303-profile-id>+</z303-profile-id>
                    <z303-ill-library>ILL_CUL</z303-ill-library>
                    <z303-home-library>+</z303-home-library>
                    <z303-note-1>".$VarFacultyName.",". $VarFacultyNumber."</z303-note-1>
                    <z303-note-2>20180930/z303-note-2>
                    <z303-ill-total-limit>0100</z303-ill-total-limit>
                    <z303-ill-active-limit>0100</z303-ill-active-limit>
                    <z303-birth-date>".$birthdate."</z303-birth-date>
                    <z303-export-consent>N</z303-export-consent>
                    <z303-proxy-id-type>00</z303-proxy-id-type>
                    <z303-send-all-letters>Y</z303-send-all-letters>
                    <z303-plain-html>B</z303-plain-html>
                    <z303-want-sms>N</z303-want-sms>
                    <z303-title-req-limit>0100</z303-title-req-limit>
                    <z303-gender>".$gender."</z303-gender>
                    <z303-birthplace>".$address_city."</z303-birthplace>
                    </z303>
                    <z304>
                    <record-action>A</record-action>
                    <z304-id>".$VarIdNumber."</z304-id>
                    <z304-sequence>01</z304-sequence>
                    <z304-address-0>".$VarLastName ." ".$VarFirstName."</z304-address-0>
                    <z304-address-1>".$address_city."</z304-address-1>
                    <z304-address-2>".$address_street."</z304-address-2>
                    <z304-address-3>+</z304-address-3>
                    <z304-address-4>+</z304-address-4>
                    <z304-zip>".$postCode."</z304-zip>
                    <z304-email-address>".$VarEmail."</z304-email-address>
                    <z304-telephone>".$VarPhoneNumber."</z304-telephone>
                    <z304-date-from>+</z304-date-from>
                    <z304-date-to>+</z304-date-to>
                    <z304-address-type>01</z304-address-type>
                    <z304-telephone-2>+</z304-telephone-2>
                    </z304>
                    <z305>
                    <record-action>A</record-action>
                    <z305-id>".$VarIdNumber."</z305-id>
                    <z305-sub-library>LSU50</z305-sub-library>
                    <z305-bor-type>+</z305-bor-type>
                    <z305-bor-status>01</z305-bor-status>
                    <z305-registration-date>20171001</z305-registration-date>
                    <z305-expiry-date>20180930</z305-expiry-date>
                    <z305-note>+</z305-note>
                    <z305-delinq-1>+</z305-delinq-1>
                    <z305-delinq-n-1>+</z305-delinq-n-1>
                    <z305-field-1>+</z305-field-1>
                    <z305-field-2>+</z305-field-2>
                    <z305-field-3>+</z305-field-3>
                    </z305>
                    <z308>
                    <record-action>A</record-action>
                    <z308-key-type>00</z308-key-type>
                    <z308-key-data>".$VarIdNumber."</z308-key-data>
                    <z308-verification>".$VarIdNumber."</z308-verification>
                    <z308-verification-type>00</z308-verification-type>
                    <z308-status>AC</z308-status>
                    <z308-encryption>H</z308-encryption>
                    </z308>
                    <z308>
                    <record-action>A</record-action>
                    <z308-key-type>01</z308-key-type>
                    <z308-key-data>".$VarBarCode."</z308-key-data>
                    <z308-verification>".$VarBarCode."</z308-verification>
                    <z308-verification-type>00</z308-verification-type>
                    <z308-status>AC</z308-status>
                    <z308-encryption>H</z308-encryption>
                    </z308>
                    </patron-record>";
                }
                $xml .= "</p-file-20>";
                $fs = new \Symfony\Component\Filesystem\Filesystem();
                $fs->dumpFile($this->container->getParameter('path').'/xml.xml', $xml);
                

                $fs1 = new \Symfony\Component\Filesystem\Filesystem();
                $fs1->dumpFile($this->container->getParameter('log_path').'/log.txt', $log);
                    

                $zip = new \ZipArchive();
                $zipName = $this->container->getParameter('zip_path').'/Documents-'.time().".zip";
                $zip->open($zipName,  \ZipArchive::CREATE);
                
                $f1= $this->container->getParameter('log_path').'/log.txt';
                $f2= $this->container->getParameter('path').'/xml.xml';

                $zip->addFromString(basename($f1),  file_get_contents($f1));
                $zip->addFromString(basename($f2),  file_get_contents($f2));  
                $zip->close();
                
                
    }
}