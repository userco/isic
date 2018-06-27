<?php

namespace ISICBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPExcel;
use PHPExcel_IOFactory;
use ISICBundle\Entity\Isic;
use ISICBundle\Jobs\ImportJob;
use PHPExcel_Shared_Date;
use Symfony\Component\HttpFoundation\Session\Session;

class ImportController extends Controller
{
    public function importAction()//Request $request)
    {
        $request = $this->getRequest();

        $form = $this->createFormBuilder()
            ->add('cardType', 'entity', array(
                    // query choices from this entity
                    'class' => 'ISICBundle:Card',
                    'label' => 'Тип карта',
                    // use the User.username property as the visible option string
                    'choice_label' => 'name',

                    // used to render a select box, check boxes or radios
                    // 'multiple' => true,
                    // 'expanded' => true,
                ))
            ->add('file', 'file', array(
                'label' => 'Избери',
                'required' => false,
            ))
            ->add('Submit', 'submit', array('label' => 'Запиши'))
            ->getForm();

        $em = $this->getDoctrine()->getManager();
        $form->bind($this->getRequest());

        if ($form->get('Submit')->isClicked()) {
            $records = $this->getDoctrine()->getRepository('ISICBundle:Isic')->findBy(array('isPublished'=>NULL));
            if($records){
                $session = new Session();
                $session->getFlashBag()->add('error', 'В системата има данни за обработване. Моля, генерирайте XML-файл преди да качвате нови.');
                return $this->render('ISICBundle:Import:Import.html.twig', array(
                    'uploadForm' => $form->createView(),
                ));
            }

            $file = $form->get('file');
            $file1 = $form->get('file')->getData();
            $cardType = $form->get('cardType')->getData();
            if (!isset($file1)) {
                $message = 'Моля изберете файл за запис';
                return $this->render('ISICBundle:Import:Import.html.twig', array(
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
                $newIsicCard = new Isic();

                
                $newIsicCard->setEGN($rowData[0][0]);
                $newIsicCard->setNames($rowData[0][1]);
                
                $newIsicCard->setBirthdate($rowData[0][9]);
                $newIsicCard->setIDWFacultyBG($rowData[0][4]); 
                $newIsicCard->setIDWFacultyNumber($rowData[0][8]);
                $newIsicCard->setSpecialty($rowData[0][5]); 
                $newIsicCard->setPhoneNumber($rowData[0][2]);
                $newIsicCard->setEmail($rowData[0][3]);
                $newIsicCard->setCourse($rowData[0][6]);
                $newIsicCard->setEducationalTypeName($rowData[0][7]);
                $newIsicCard->setChipNumber($rowData[0][10]);
                $newIsicCard->setIDWLID($rowData[0][11]);
                $newIsicCard->setIDWBarCodeInt($rowData[0][12]);
                $newIsicCard->setImportDate(new \DateTime());
                $newIsicCard->setCardType($cardType);
               
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
