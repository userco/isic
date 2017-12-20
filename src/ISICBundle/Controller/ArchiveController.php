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

        $archives = array();
        $archiveModel = new ArchiveModel();
        $form = $this->createForm(new XMLType(), $archiveModel);
        //$request = $this->get('request');

        if ( $request->getMethod() == 'POST') {
            $form->handleRequest($request);

            $dateFrom = $form->get('generateDateFrom')->getData();
            $dateTo = $form->get('generateDateTo')->getData();

            $dateFrom = $dateFrom->format('Y-m-d');
            $dateTo = $dateTo->format('Y-m-d');
           //var_dump($dateFrom);
          // die();
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT a
                FROM ISICBundle:Archive a
                WHERE a.generateDate >= :dateFrom
                   AND  a.generateDate <= :dateTo
                '
            )->setParameter('dateFrom', $dateFrom)
             ->setParameter('dateTo', $dateTo);

            $archives = $query->getResult();

    	   
           
        if(!$archives){
                $session = new Session();
                $session->getFlashBag()->add('error', 'Данни между тези дати не са качвани.');
            }
        }

        return $this->render(
            'security/xml/search_xml.html.twig',array(
           'form' => $form->createView(),
           'archives'=>$archives,
        ));
    }

    /**
     * @Route("/get_xml", name="get_xml")
     */
    public function resultArchiveAction(Request $request, $archiveId){
        $archive =$this->getDoctrine()->getRepository('ISICBundle:Archive')->find($archiveId);
            $zipName1 = $archive->getArchiveName();

            $zipName = $this->container->getParameter('zip_path').'/'.$zipName1;
           if(file_exists($zipName)){
            $response = new BinaryFileResponse($zipName);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
            return $response;
        }
    }
}           

            