<?php

//автор Мария Пенелова
// src/Controller/SecurityController.php
namespace  ISICBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\Session;
use ISICBundle\Form\XMLType;
use Desperado\XmlBundle\Model\XmlPrepare;
use Desperado\XmlBundle\Model\XmlGenerator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
use ISICBundle\Entity\Archive;


class ArchiveController extends Controller
{

    /**
     * @Route("/search_xml", name="search_xml")
     */
    public function searchXMLAction(Request $request)
    { 

        $archive = new Archive();
        $form = $this->createForm(new XMLType(), $archive);
        //$request = $this->get('request');

        if ( $request->getMethod() == 'POST') {
            $form->handleRequest($request);

            $date = $form->get('generateDate')->getData();
            $d = $date->format('Y-m-d');
            //var_dump($d);
            //  die();
            //$date1 = $date->format('Y-m-d H:i:s');
    	    $archives = $this->getDoctrine()->getRepository('ISICBundle:Archive')->findBy(array('generateDate'=>$d));
            // var_dump($archives);
            // die();
            $dataPackage = $this->container->getParameter('archives').'/archives-'.time().".zip";
            $zip = new \ZipArchive();
            $zip->open($dataPackage,  \ZipArchive::CREATE);
            $zipName = "";

            foreach($archives as $ar){
                // die();
               // if($ar->getGenerateDate()==$d){
                $zipName1 = $ar->getArchiveName();
               // var_dump($ar->getGenerateDate());
                // die();
                $zipName = $this->container->getParameter('zip_path').'/'.$zipName1;

               // $zip->open($dataPackage,  \ZipArchive::CREATE);
                $zip->addFromString(basename($zipName),  file_get_contents($zipName));
            //}  
            }

            $zip->close();
            if(file_exists($zipName)){
            $response = new BinaryFileResponse($dataPackage);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
            
            return $response;
        }
            else{
                $session = new Session();
                //$session->start();
                $session->getFlashBag()->add('error', 'Данни на тази дата не са качвани.');
            }
        }

        return $this->render(
            'security/xml/search_xml.html.twig',array(
           'form' => $form->createView(),
        ));
    } 
}           

            