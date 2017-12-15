<?php

namespace ISICBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPExcel;
use PHPExcel_IOFactory;
use ISICBundle\Entity\Isic;
use ISICBundle\Jobs\ImportJob;

class ImportController extends Controller
{
    public function importAction()//Request $request)
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

            $file = $form->get('file');
            $file1 = $form->get('file')->getData();
            if (!isset($file1)) {
                $message = 'Моля изберете файл за запис';
                return $this->render('ISICBundle:Import:Import.html.twig', array(
                    'uploadForm' => $form->createView(),
                    'message' => $message,
                ));
            }
            $date = time();

            $file->getData()->move('./uploads', "export_$date.xls");

            $inputFileName = "./uploads/export_$date.xls";

            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }

            $sheet = $objPHPExcel->getSheet(3);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            for ($row = 2; $row <= $highestRow; $row++) {
                //  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                    NULL,
                    TRUE,
                    FALSE);
                $newIsicCard = new Isic();


                $newIsicCard->setNames($rowData[0][0]);
                $newIsicCard->setEGN($rowData[0][1]);
                $newIsicCard->setBirthdate($row[0][2]);
                $newIsicCard->setIDWFacultyBG($rowData[0][3]); 
                $newIsicCard->setIDWFacultyNumber($rowData[0][4]);
                $newIsicCard->setSpecialty($rowData[0][5]); 
                $newIsicCard->setPhoneNumber($rowData[0][6]);
                $newIsicCard->setEmail($rowData[0][7]);
                $newIsicCard->setChipNumber($rowData[0][8]);
                $newIsicCard->setIDWLID($rowData[0][9]);
                $newIsicCard->setIDWBarCodeInt($rowData[0][10]);
                
                /*$newIsicCard->setIDWKeyColumn($rowData[0][0]);
                $newIsicCard->setIDWFirstNameBG($rowData[0][1]);
                $newIsicCard->setIDWFamilyNameBG($rowData[0][2]);
                $newIsicCard->setIDWFirstNameEN($rowData[0][3]);
                $newIsicCard->setIDWFamilyNameEN($rowData[0][4]);
                $newIsicCard->setIDWFacultyBG($rowData[0][5]);
                $newIsicCard->setIDWFacultyEN($rowData[0][6]);
                $newIsicCard->setIDWClass($rowData[0][7]);
                $newIsicCard->setIDWFacultyNumber($rowData[0][8]);
                $newIsicCard->setIDWLID($rowData[0][9]);
                $newIsicCard->setIDWBarCodeInt($rowData[0][10]);
                $newIsicCard->setIDWBarCodeField($rowData[0][11]);
                $newIsicCard->setIDWLIDBack($rowData[0][12]);
                $newIsicCard->setIDWBarCodeIntBack($rowData[0][13]);
                $newIsicCard->setIDWBarCodeFieldBack($rowData[0][14]);
                $newIsicCard->setIDWPhoto($rowData[0][15]);
                $newIsicCard->setEGN($rowData[0][16]);
                $newIsicCard->setBirthdate($rowData[0][17]);
                $newIsicCard->setSpecialty($rowData[0][18]);
                $newIsicCard->setChipNumber($rowData[0][19]);
                $newIsicCard->setPhoneNumber($rowData[0][20]);
                $newIsicCard->setEmail($rowData[0][21]);
                $newIsicCard->setNames($rowData[0][22]);
                */
                $em->persist($newIsicCard);
            }
            $em->flush();


            
            

            $message = 'Data has been imported!';
            return $this->render('ISICBundle:Import:Import.html.twig', array(
                'uploadForm' => $form->createView(),
                'message' => $message,
            ));
        }
        return $this->render('ISICBundle:Import:Import.html.twig', array(
            'uploadForm' => $form->createView(),
        ));

    }

}
