<?php

namespace ISICBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class PersonalNumber
{
    protected $id;
    protected $personalNumber;
    protected $isPublished;
    protected $status;

    public function getId(){
        return $this->id;
    }
    public function setId($id){
        $this->id = $id;
    }
    public function getPersonalNumber(){
        return $this->personalNumber;
    }
    public function setPersonalNumber($personalNumber){
        $this->personalNumber = $personalNumber;
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
}