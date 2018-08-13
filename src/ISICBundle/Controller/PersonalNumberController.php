<?php

namespace ISICBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPExcel;
use PHPExcel_IOFactory;
use ISICBundle\Entity\Isic;
use ISICBundle\Jobs\ImportJob;
use PHPExcel_Shared_Date;
use ISICBundle\Entity\PersonalNumber;
use Symfony\Component\HttpFoundation\Session\Session;

class PersonalNumberController extends Controller
{
    public function importAction()
    {
        $request = $this->getRequest();

        $form = $this->createFormBuilder()
            ->add('file', 'file', array(
                'label' => 'Избери',
                'required' => false,
            ))
            ->add('Submit', 'submit', array('label' => 'Запиши'))
            ->getForm();

        $em = $this->getDoctrine()->getManager();
        $form->bind($this->getRequest());

        if ($form->get('Submit')->isClicked()) {
            $records = $this->getDoctrine()->getRepository('ISICBundle:PersonalNumber')->findBy(array('isPublished'=>NULL));
            if($records){
                $session = new Session();
                $session->getFlashBag()->add('error', 'В системата има данни за обработване. Моля, генерирайте CSV-файл преди да качвате нови.');
                return $this->render('ISICBundle:Import:Import.html.twig', array(
                    'uploadForm' => $form->createView(),
                ));
            }

            $file = $form->get('file');
            $file1 = $form->get('file')->getData();
            
            if (!isset($file1)) {
                $message = 'Моля изберете файл за запис';
                return $this->render('ISICBundle:PersonalNumber:Import.html.twig', array(
                    'uploadForm' => $form->createView(),
                    'message' => $message,
                ));
            }
            $date = time();

            $file->getData()->move('./uploads', "export_egn_$date.xls");

            $inputFileName = "./uploads/export_egn_$date.xls";

            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }

            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            for ($row = 2; $row <= $highestRow; $row++) {
                //  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                    NULL,
                    TRUE,
                    FALSE);
                $newIsic = new PersonalNumber();
                $egnString = $rowData[0][0];
                $egnString1 = ltrim($egnString, "_");
                $newIsic->setPersonalNumber($egnString1);
                $em->persist($newIsic);
            }
            $em->flush();


            
            

            $message = 'Data has been imported!';
            return $this->render('ISICBundle:PersonalNumber:Import.html.twig', array(
                'uploadForm' => $form->createView(),
                'message' => $message,
            ));
        }
        return $this->render('ISICBundle:PersonalNumber:Import.html.twig', array(
            'uploadForm' => $form->createView(),
        ));

    }
    

}
