<?php

namespace ISICBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPExcel;
use PHPExcel_IOFactory;

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

            $sheet = $objPHPExcel->getSheet(1);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            for ($row = 2; $row <= $highestRow; $row++){
                //  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                    NULL,
                    TRUE,
                    FALSE);
                dump($rowData);
                //  Insert row data array into your database of choice here
            }
            exit;

//            if (($handle = fopen('./uploads/a.csv', 'r')) !== FALSE) {
//                $row = fgetcsv($handle);
//
//                $documents = $em->getRepository('PriemBundle:Document');
//
//                while (($row = fgetcsv($handle)) !== FALSE) {
//
//                    $specialtyID = (int)$row[2];
//
//                    $entryNumber = $row[0];
//                    $EN = strlen($entryNumber);
//                    switch ($EN) {
//                        case 0:
//                            $entryNumber = $row[0];
//                            break;
//                        case 1:
//                            $entryNumber = '0000' . $row[0];
//                            break;
//                        case 2:
//                            $entryNumber = '000' . $row[0];
//                            break;
//                        case 3:
//                            $entryNumber = '00' . $row[0];
//                            break;
//                        case 4:
//                            $entryNumber = '0' . $row[0];
//                            break;
//                        default:
//                            $entryNumber = $row[0];
//                    }
//
//                    if (($student = $documents->findBy(array('entryNumber' => $entryNumber))) != NULL) {
//                        $i = 0;
//
//                        foreach ($student as $docs) {
//                            foreach ($docs->getExams() as $ex) {
//                                if ($ex->getId() == $specialtyID) {
//                                    $i += 1;
//                                }
//                            }
//                        }
//                        if ($i == 0) {
//                            $message = 'Не можете да въведете несъществуваща специалност.' . ' - ' . $entryNumber . '  ' . $row[1] . "  - " . $row[3];
//                            return $this->render('ISICBundle:Import:Import.html.twig', array(
//                                'uploadForm' => $form->createView(),
//                                'message' => $message,
//                            ));
//                        } else {
//                            $specialty = $this->getDoctrine()->getRepository('NomenclatureBundle:Exam')->findOneById($specialtyID);
//                        }
//                    } else {
//                        $message = 'Не можете да въведете несъществуващ входящ номер.' . ' - ' . $entryNumber . '  ' . $row[1];
//                        return $this->render('ISICBundle:Import:Import.html.twig', array(
//                            'uploadForm' => $form->createView(),
//                            'message' => $message,
//                        ));
//                    }
//                    $whiteSpace = str_replace(" ", "", $row[4]);
//                    if ($whiteSpace == '') {
//                        $message = 'Не сте въвели всички оценки.';
//                        return $this->render('ISICBundle:Import:Import.html.twig', array(
//                            'uploadForm' => $form->createView(),
//                            'message' => $message,
//                        ));
//                    }
//                    //start checking marks
//                    $markExplodeComma = explode(',', $row[4]);
//                    $markExplodePoint = explode('.', $row[4]);
//
//
//                    if (count($markExplodePoint) == 1) {
//                        if (count($markExplodeComma) == 2) {
//                            if (is_numeric($markExplodeComma[0]) && $markExplodeComma[0] >= 0 && $markExplodeComma[0] <= 6) {
//                                if (is_numeric($markExplodeComma[1]) && $markExplodeComma[1] >= 0 && $markExplodeComma[1] <= 99 && $this->isUpToSix($markExplodeComma[0], $markExplodeComma[1]) == 1) {
//                                    $validatedMark = $markExplodeComma[0] . '.' . $markExplodeComma[1];
//                                    $message = 'Оценките са записани успешно.';
//                                } else {
//                                    $message = 'Грешен формат или не сте въвели всички оценки. Прегледайте файла и опитайте отново.' . ' - ' . $entryNumber;
//                                    return $this->render('ISICBundle:Import:Import.html.twig', array(
//                                        'uploadForm' => $form->createView(),
//                                        'message' => $message,
//                                    ));
//                                }
//                            } else {
//                                $message = 'Грешен формат или не сте въвели всички оценки. Прегледайте файла и опитайте отново.' . ' - ' . $entryNumber;
//                                return $this->render('ISICBundle:Import:Import.html.twig', array(
//                                    'uploadForm' => $form->createView(),
//                                    'message' => $message,
//                                ));
//                            }
//                        } elseif (count($markExplodeComma) == 1) {
//                            if (is_numeric($markExplodeComma[0]) && $markExplodeComma[0] >= 0 && $markExplodeComma[0] <= 6) {
//                                $validatedMark = $row[4];
//                                $message = 'Оценките са записани успешно.';
//                            } else {
//                                $message = 'Грешен формат или не сте въвели всички оценки. Прегледайте файла и опитайте отново.' . ' - ' . $entryNumber;
//                                return $this->render('ISICBundle:Import:Import.html.twig', array(
//                                    'uploadForm' => $form->createView(),
//                                    'message' => $message,
//                                ));
//                            }
//                        } else {
//                            $message = 'Грешен формат или не сте въвели всички оценки. Прегледайте файла и опитайте отново.' . ' - ' . $entryNumber;
//                            return $this->render('ISICBundle:Import:Import.html.twig', array(
//                                'uploadForm' => $form->createView(),
//                                'message' => $message,
//                            ));
//                        }
//                    } elseif (count($markExplodePoint) == 2) {
//                        if (is_numeric($markExplodePoint[0]) && $markExplodePoint[0] >= 0 && $markExplodePoint[0] <= 6) {
//                            if (is_numeric($markExplodePoint[1]) && $markExplodePoint[1] >= 0 && $markExplodePoint[1] <= 99 && $this->isUpToSix($markExplodePoint[0], $markExplodePoint[1]) == 1) {
//                                $validatedMark = $row[4];
//                                $message = 'Оценките са записани успешно.';
//                            } else {
//                                $message = 'Грешен формат или не сте въвели всички оценки. Прегледайте файла и опитайте отново.' . ' - ' . $entryNumber;
//                                return $this->render('ISICBundle:Import:Import.html.twig', array(
//                                    'uploadForm' => $form->createView(),
//                                    'message' => $message,
//                                ));
//                            }
//                        } else {
//                            $message = 'Грешен формат или не сте въвели всички оценки. Прегледайте файла и опитайте отново.' . ' - ' . $entryNumber;
//                            return $this->render('ISICBundle:Import:Import.html.twig', array(
//                                'uploadForm' => $form->createView(),
//                                'message' => $message,
//                            ));
//                        }
//                    } else {
//                        $message = 'Грешен формат или не сте въвели всички оценки. Прегледайте файла и опитайте отново.' . ' - ' . $entryNumber;
//                        return $this->render('ISICBundle:Import:Import.html.twig', array(
//                            'uploadForm' => $form->createView(),
//                            'message' => $message,
//                        ));
//                    }
////end checking marks
//                    $mark = (float)$validatedMark;
//
//                    if (($markedStudent = $alreadyMarked->findOneBy(array('entryNumber' => $entryNumber, 'exam' => $specialty))) != NULL) {
//                        $markedStudent->setExamMark($mark);
//                        $em->persist($markedStudent);
//                    } else {
//                        $newMark = new ExamNotes();
//                        $newMark->setExam($specialty);
//                        $newMark->setEntryNumber($entryNumber);
//                        $newMark->setExamMark($mark);
//                        $em->persist($newMark);
//                    }
//                }
//                $em->flush();
//
//                return $this->render('ISICBundle:Import:Import.html.twig', array(
//                    'uploadForm' => $form->createView(),
//                    'message' => $message,
//                ));
//            }
        }
        return $this->render('ISICBundle:Import:Import.html.twig', array(
            'uploadForm' => $form->createView(),
        ));

    }

}

//    {
//        return $this->render('');
//    }
//}
