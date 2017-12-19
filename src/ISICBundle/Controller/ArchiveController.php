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
use ISICBundle\Entity\Models\ArchiveModel;


class ArchiveController extends Controller
{

    /**
     * @Route("/search_xml", name="search_xml")
     */
    public function searchXMLAction(Request $request)
    { 

        $archiveModel = new ArchiveModel();
        $form = $this->createForm(new XMLType(), $archiveModel);
        //$request = $this->get('request');

        if ( $request->getMethod() == 'POST') {
            $form->handleRequest($request);

            $dateFrom = $form->get('generateDateFrom')->getData();
            $dateTo = $form->get('generateDateTo')->getData();
           
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT a
                FROM ISICBundle:Archive a
                WHERE a.generateDate > :dateFrom
                AND  a.generateDate < :dateTo'
            )->setParameter('dateFrom', $dateFrom)
             ->setParameter('dateTo', $dateTo);

            $archives = $query->getResult();

    	   
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

            