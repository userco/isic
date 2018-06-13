<?php

//автор Мария Пенелова

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
use ISICBundle\Entity\EGNArchive;
use ISICBundle\Entity\Models\ArchiveModel;


class EGNArchiveController extends Controller
{

    /**
     * @Route("/search_data", name="search_data")
     */
    public function searchDataAction(Request $request)
    { 

        $archives = array();
        $archiveModel = new ArchiveModel();
        $form = $this->createForm(new XMLType(), $archiveModel);
        

        if ( $request->getMethod() == 'POST') {
            $form->handleRequest($request);

            $dateFrom = $form->get('generateDateFrom')->getData();
            $dateTo = $form->get('generateDateTo')->getData();

            $dateFrom = $dateFrom->format('Y-m-d');
            $dateTo = $dateTo->format('Y-m-d');
           
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT a
                FROM ISICBundle:EGNArchive a
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
            'security/data/search_data.html.twig',array(
           'form' => $form->createView(),
           'archives'=>$archives,
        ));
    }

    /**
     * @Route("/get_data", name="get_data")
     */
    public function resultDataAction(Request $request, $archiveId){
        $archive =$this->getDoctrine()->getRepository('ISICBundle:EGNArchive')->find($archiveId);
            $zipName1 = $archive->getArchiveName();

            $zipName = $this->container->getParameter('egn_path').'/'.$zipName1;
           if(file_exists($zipName)){
            $response = new BinaryFileResponse($zipName);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
            return $response;
        }
    }
}           

            
