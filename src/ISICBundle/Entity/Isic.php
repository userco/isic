<?php
// src/ISICBundle/Entity/User.php
namespace ISICBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Isic
{
    private $id;
    private $IDWKeyColumn;
    private $IDWFirstNameBG;
    private $IDWFamilyNameBG;
    private $IDWFirstNameEN;
    private $IDWFamilyNameEN;
    private $IDWFacultyBG;
    private $IDWFacultyEN;
    protected $isPublished;
    protected $status;
    protected $importDate;

    /**
     * @return mixed
     */
    public function getIDWKeyColumn()
    {
        return $this->IDWKeyColumn;
    }

    /**
     * @param mixed $IDWKeyColumn
     */
    public function setIDWKeyColumn($IDWKeyColumn)
    {
        $this->IDWKeyColumn = $IDWKeyColumn;
    }

    /**
     * @return mixed
     */
    public function getIDWFirstNameBG()
    {
        return $this->IDWFirstNameBG;
    }

    /**
     * @param mixed $IDWFirstNameBG
     */
    public function setIDWFirstNameBG($IDWFirstNameBG)
    {
        $this->IDWFirstNameBG = $IDWFirstNameBG;
    }

    /**
     * @return mixed
     */
    public function getIDWFamilyNameBG()
    {
        return $this->IDWFamilyNameBG;
    }

    /**
     * @param mixed $IDWFamilyNameBG
     */
    public function setIDWFamilyNameBG($IDWFamilyNameBG)
    {
        $this->IDWFamilyNameBG = $IDWFamilyNameBG;
    }

    /**
     * @return mixed
     */
    public function getIDWFirstNameEN()
    {
        return $this->IDWFirstNameEN;
    }

    /**
     * @param mixed $IDWFirstNameEN
     */
    public function setIDWFirstNameEN($IDWFirstNameEN)
    {
        $this->IDWFirstNameEN = $IDWFirstNameEN;
    }

    /**
     * @return mixed
     */
    public function getIDWFamilyNameEN()
    {
        return $this->IDWFamilyNameEN;
    }

    /**
     * @param mixed $IDWFamilyNameEN
     */
    public function setIDWFamilyNameEN($IDWFamilyNameEN)
    {
        $this->IDWFamilyNameEN = $IDWFamilyNameEN;
    }

    /**
     * @return mixed
     */
    public function getIDWFacultyBG()
    {
        return $this->IDWFacultyBG;
    }

    /**
     * @param mixed $IDWFacultyBG
     */
    public function setIDWFacultyBG($IDWFacultyBG)
    {
        $this->IDWFacultyBG = $IDWFacultyBG;
    }

    /**
     * @return mixed
     */
    public function getIDWFacultyEN()
    {
        return $this->IDWFacultyEN;
    }

    /**
     * @param mixed $IDWFacultyEN
     */
    public function setIDWFacultyEN($IDWFacultyEN)
    {
        $this->IDWFacultyEN = $IDWFacultyEN;
    }

    /**
     * @return mixed
     */
    public function getIDWClass()
    {
        return $this->IDWClass;
    }

    /**
     * @param mixed $IDWClass
     */
    public function setIDWClass($IDWClass)
    {
        $this->IDWClass = $IDWClass;
    }

    /**
     * @return mixed
     */
    public function getIDWFacultyNumber()
    {
        return $this->IDWFacultyNumber;
    }

    /**
     * @param mixed $IDWFacultyNumber
     */
    public function setIDWFacultyNumber($IDWFacultyNumber)
    {
        $this->IDWFacultyNumber = $IDWFacultyNumber;
    }

    /**
     * @return mixed
     */
    public function getIDWLID()
    {
        return $this->IDWLID;
    }

    /**
     * @param mixed $IDWLID
     */
    public function setIDWLID($IDWLID)
    {
        $this->IDWLID = $IDWLID;
    }

    /**
     * @return mixed
     */
    public function getIDWBarCodeInt()
    {
        return $this->IDWBarCodeInt;
    }

    /**
     * @param mixed $IDWBarCodeInt
     */
    public function setIDWBarCodeInt($IDWBarCodeInt)
    {
        $this->IDWBarCodeInt = $IDWBarCodeInt;
    }

    /**
     * @return mixed
     */
    public function getIDWBarCodeField()
    {
        return $this->IDWBarCodeField;
    }

    /**
     * @param mixed $IDWBarCodeField
     */
    public function setIDWBarCodeField($IDWBarCodeField)
    {
        $this->IDWBarCodeField = $IDWBarCodeField;
    }

    /**
     * @return mixed
     */
    public function getIDWLIDBack()
    {
        return $this->IDWLIDBack;
    }

    /**
     * @param mixed $IDWLIDBack
     */
    public function setIDWLIDBack($IDWLIDBack)
    {
        $this->IDWLIDBack = $IDWLIDBack;
    }

    /**
     * @return mixed
     */
    public function getIDWBarCodeIntBack()
    {
        return $this->IDWBarCodeIntBack;
    }

    /**
     * @param mixed $IDWBarCodeIntBack
     */
    public function setIDWBarCodeIntBack($IDWBarCodeIntBack)
    {
        $this->IDWBarCodeIntBack = $IDWBarCodeIntBack;
    }

    /**
     * @return mixed
     */
    public function getIDWBarCodeFieldBack()
    {
        return $this->IDWBarCodeFieldBack;
    }

    /**
     * @param mixed $IDWBarCodeFieldBack
     */
    public function setIDWBarCodeFieldBack($IDWBarCodeFieldBack)
    {
        $this->IDWBarCodeFieldBack = $IDWBarCodeFieldBack;
    }

    /**
     * @return mixed
     */
    public function getIDWPhoto()
    {
        return $this->IDWPhoto;
    }

    /**
     * @param mixed $IDWPhoto
     */
    public function setIDWPhoto($IDWPhoto)
    {
        $this->IDWPhoto = $IDWPhoto;
    }

    /**
     * @return mixed
     */
    public function getEGN()
    {
        return $this->EGN;
    }

    /**
     * @param mixed $EGN
     */
    public function setEGN($EGN)
    {
        $this->EGN = $EGN;
    }

    /**
     * @return mixed
     */
    public function getBirthdate()
    {
        return $this->Birthdate;
    }

    /**
     * @param mixed $Birthdate
     */
    public function setBirthdate($Birthdate)
    {
        $this->Birthdate = $Birthdate;
    }

    /**
     * @return mixed
     */
    public function getSpecialty()
    {
        return $this->Specialty;
    }

    /**
     * @param mixed $Specialty
     */
    public function setSpecialty($Specialty)
    {
        $this->Specialty = $Specialty;
    }

    /**
     * @return mixed
     */
    public function getChipNumber()
    {
        return $this->ChipNumber;
    }

    /**
     * @param mixed $ChipNumber
     */
    public function setChipNumber($ChipNumber)
    {
        $this->ChipNumber = $ChipNumber;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->PhoneNumber;
    }

    /**
     * @param mixed $PhoneNumber
     */
    public function setPhoneNumber($PhoneNumber)
    {
        $this->PhoneNumber = $PhoneNumber;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->Email;
    }

    /**
     * @param mixed $Email
     */
    public function setEmail($Email)
    {
        $this->Email = $Email;
    }

    /**
     * @return mixed
     */
    public function getNames()
    {
        return $this->Names;
    }

    /**
     * @param mixed $Names
     */
    public function setNames($Names)
    {
        $this->Names = $Names;
    }
    private $IDWClass;
    private $IDWFacultyNumber;
    private $IDWLID;
    private $IDWBarCodeInt;
    private $IDWBarCodeField;
    private $IDWLIDBack;
    private $IDWBarCodeIntBack;
    private $IDWBarCodeFieldBack;
    private $IDWPhoto;
    private $EGN;
    private $Birthdate;
    private $Specialty;
    private $ChipNumber;
    private $PhoneNumber;
    private $Email;
    private $Names;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIsPublished(){
        return $this->isPublished;
    }

    public function setIsPublished($isPublished){
        $this->isPublished = $isPublished;
    }

    public function getStatus(){
        return $this->status;
    }

    public function setStatus($status){
        $this->status = $status;
    }

    public function getImportDate(){
        return $this->importDate;
    }

    public function setImportDate($importDate){
        $this->importDate = $importDate;
    }
}