<?php
namespace ISICBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ISICBundle\Entity\Permission;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
/// install with:
///$ composer require ddeboer/data-import:@stable
use Ddeboer\DataImport\Reader\CsvReader;

class LoadPermissionData extends AbstractFixture implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface

{

     /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager){
        
        $em = $this->container->get('doctrine')->getManager();
    	$dir = $this->container->getParameter('csv_path');


        $fileInvoice = new \SplFileObject( $dir."/permission.csv");
        $readerInvoice = new CsvReader($fileInvoice, ',');
        
        foreach ($readerInvoice as $row) {

            $permission = new Permission();
            $permission->setId($row[0]);
            
            $permission->setName($row[1]);
           

            $manager->persist($permission);

            $metadata = $em->getClassMetaData(get_class($permission));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

            $manager->flush();
            
        }

    } 

    public function getOrder(){
        return 1;
    }
} 