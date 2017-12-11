<?php
// src/Controller/SecurityController.php
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
use ISICBundle\Form\XMLType;
use Desperado\XmlBundle\Model\XmlPrepare;
use Desperado\XmlBundle\Model\XmlGenerator;

class XMLController extends Controller
{


    private function getFirstName($Names){
        $name_array = explode(' ', $Names);
        
        $firstname="";
        if(count($name_array)==3)
            $firstname = $name_array[0].",".$name_array[1];
       
        if(count($name_array)==2)
            $firstname = $name_array[0];
        return $firstname;
    }
    private function getLastName($Names){
        $name_array = explode(' ', $Names);
        if(count($name_array)==3)
        $lastname = $name_array[2];

        else $lastname = $name_array[1];
        return $lastname;
    }
    private function getBirthDate($birthdate){
        $array = explode('/', $birthdate);
        $date_string = $array[2].$array[1].$array[0];
        return $date_string;
    }
    /**
     * @Route("/generate_xml", name="generate_xml")
     */
    public function generateXMLAction(Request $request)
    {   

        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile('file.xml', "");
        
           $params = [
                        'Details' => [
                            'PaymentParameters' => [
                                'first_node'  => 'first_node_value',
                                'second_node' => 'second_node_value'
                            ]
                        ]
        ];

        $xmlPrepare = new XmlPrepare;
        $xmlGenerator = new XmlGenerator;

        $xml = $xmlGenerator->setRootName('request')->generateFromArray($xmlPrepare->prepareArrayBeforeToXmlConvert($params));



        $isic_xml = new Isic();
        $form = $this->createForm(new XMLType(), $isic_xml);
        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
    	$isics =$this->getDoctrine()->getRepository('ISICBundle:Isic')->findAll();
        $xml = "<?xml version='1.0'?>
                    <p-file-20>";
        foreach($isics as $isic){
            $VarIdNumber = $isic->getIDWLIDBack();
            $Names = $isic->getNames();
            if($Names){
            $VarLastName = $this->getFirstName($Names);
            $VarFirstName = $this->getLastName($Names);
             }
            $VarEmail = $isic->getEmail();
            $VarPhoneNumber = $isic->getPhoneNumber();
            $VarBarCode = $isic->getIDWBarCodeInt();
            $VarFacultyName = $isic->getIDWFacultyBG();
            $VarFacultyNumber = $isic->getIDWFacultyNumber();
            $birthdate = $isic->getBirthDate();

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
                    <z303-gender>VarGender</z303-gender>
                    <z303-birthplace>VarBirthPlace</z303-birthplace>
                    </z303>
                    <z304>
                    <record-action>A</record-action>
                    <z304-id>".$VarIdNumber."</z304-id>
                    <z304-sequence>01</z304-sequence>
                    <z304-address-0>".$VarLastName ." ".$VarFirstName."</z304-address-0>
                    <z304-address-1>VarAddressCity</z304-address-1>
                    <z304-address-2>VarAddressFirstLine</z304-address-2>
                    <z304-address-3>VarAddressSecondLine</z304-address-3>
                    <z304-address-4>VarAddressThirdLine</z304-address-4>
                    <z304-zip>VarZipCode</z304-zip>
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
                $fs->dumpFile('file.xml', $xml);
                
                    

            //}
        }
        return $this->render(
            'security/xml/generate_xml.html.twig',array(
           'form' => $form->createView(),
        ));
    }
}
