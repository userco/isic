<?php
namespace ISICBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ISICBundle\Entity\Permission;
use ISICBundle\Entity\Card;
use ISICBundle\Entity\User;
use ISICBundle\Entity\Role;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
// private $encoder;

// public function setEncoder(UserPasswordEncoderInterface $encoder=null)
// {
//     $this->encoder = $encoder;
// }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager){
        
        $em = $this->container->get('doctrine')->getManager();
    	$dir = $this->container->getParameter('csv_path');


        $fileInvoice = new \SplFileObject( $dir."/permission.csv");
	$fileFreshman = new \SplFileObject( $dir."/susi_freshman.csv");
        $readerInvoice = new CsvReader($fileInvoice, ',');
	$readerFreshman = new CsvReader($fileFreshman, ',');
        $i=0;
	 foreach ($readerFreshman as $row) {
	    if($i>=2){
            $susi = new Susi();
            
            $susi->setName($row[1]);
           
	    $susi->setEgn($row[9]);
	    $susi->setFaculty($row[0]);
            $susi->setFacultyNumber($row[2]);
            $susi->setEmail($row[23]);
            $susi->setPhoneNumber($row[27]);
            $susi->setAddressCity($row[30]);
            //$susi->setPostCode($row[NULL]);
            $susi->setAddressStreet($row[31]);
            $birthdate = $row[17];
            $date_array = explode(".",$birthdate);
	    $birthdate1 = $date_array[2]."-".$date_array[1]."-".$date_array[0];
            $susi->setBirthDate($birthdate1);
            $susi->setGenderName($row[12]);
            $susi->setCourse($row[6]);

	    $eduplan = $row[4];
	    $edutype = explode('/', $eduplan);
	    $edutype1 = $edutype[1];
            $susi->setEducationalTypeName($edutype1);
            $susi->setSpeciality($row[3]);
            $manager->persist($susi);

            //$metadata = $em->getClassMetaData(get_class($permission));
            //$metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

            $manager->flush();
	     }
	    $i++;
            
            
        }
        
        foreach ($readerInvoice as $row) {

            $permission = new Permission();
            $permission->setId($row[0]);
            
            $permission->setName($row[1]);
           

            $manager->persist($permission);

            $metadata = $em->getClassMetaData(get_class($permission));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

            $manager->flush();
            
        }
        $fileCardType = new \SplFileObject( $dir."/card_type.csv");
        $readerCardType = new CsvReader($fileCardType, ',');
        
        foreach ($readerCardType as $row) {

            $cardType = new Card();
            $cardType->setId($row[0]);
            
            $cardType->setName($row[1]);
           

            $manager->persist($cardType);

            $metadata = $em->getClassMetaData(get_class($cardType));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

            $manager->flush();
            
        }
		
    $permissions = $em->getRepository("ISICBundle\Entity\Permission")->findAll();
    $role = new Role();
    $role->setName("superadmin");
    foreach($permissions as $perm){
    
    $role->addPermission($perm);


    }
    $manager->persist($role);
    $manager->flush();
    
	$user = new User();
	$user->setUsername("test");
	$password = $this->container->get('security.password_encoder')
                ->encodePassword($user, "test");
        $user->setPassword($password);
	$user->setEmail("test@test.bg");
	$user->setIsActive("1");
	$user->addUserRole($role);

	$manager->persist($user);
	$manager->flush();
	
	
    } 

    public function getOrder(){
        return 1;
    }
} 
