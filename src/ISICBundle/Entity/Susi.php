<?php
//автор Мария Пенелова
// src/ISICBundle/Entity/User.php
namespace ISICBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Susi{

	protected $id;
	protected $name;
	protected $egn;
	protected $faculty;
	protected $facultyNumber;
	protected $email;
	protected $phoneNumber;
	protected $addressCity;
	protected $postCode;
	protected $addressStreet;
	protected $birthDate;
	protected $genderName;
	protected $course;
	protected $educationalTypeName;
	protected $speciality;


	public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
	public function getName(){
		return $this->name;
	}
	public function setName($name){
		$this->name = $name;
	}

	public function getEgn(){
		return $this->egn;
	}
	public function setEgn($egn){
		$this->egn = $egn;
	}

	public function getFaculty(){
		return $this->faculty;
	}
	public function setFaculty($faculty){
		$this->faculty = $faculty;
	}
	public function getFacultyNumber(){
		return $this->facultyNumber;
	}
	public function setFacultyNumber($facultyNumber){
		$this->facultyNumber = $facultyNumber;
	}

	public function getEmail(){
		return $this->email;
	}
	public function setEmail($email){
		$this->email = $email;
	}
	public function getPhoneNumber(){
		return $this->phoneNumber;
	}
	public function setPhoneNumber($phoneNumber){
		$this->phoneNumber = $phoneNumber;
	}
	public function getAddressCity(){
		return $this->addressCity;
	}
	public function setAddressCity($addressCity){
		$this->addressCity = $addressCity;
	}

	public function getAddressStreet(){
		return $this->addressStreet;
	}
	public function setAddressStreet($addressStreet){
		$this->addressStreet = $addressStreet;
	}
	public function getBirthDate(){
		return $this->birthDate;
	}
	public function setBirthDate($birthDate){
		$this->birthDate = $birthDate;
	}
	public function getPostCode(){
		return $this->postCode;
	}
	public function setPostCode($postCode){
		$this->postCode = $postCode;
	}
	public function getGenderName(){
		return $this->genderName;
	}
	public function setGenderName($genderName){
		$this->genderName= $genderName;
	}
	public function getCourse(){
		return $this->course;
	}
	public function setCourse($course){
		$this->course = $course;
	
	}
	public function getEducationalTypeName(){
		return $this->educationalTypeName;
	}
	public function setEducationalTypeName($educationalTypeName){
	       $this->educationalTypeName = $educationalTypeName;
	}
	public function getSpeciality(){
		return $this->speciality;
	}
	public function setSpeciality($speciality){
	        $this->speciality = $speciality;
	}
}
