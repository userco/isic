<?php
//автор Мария Пенелова
namespace ISICBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
class Card{

	protected $id;
	protected $name;
	protected $isics;

	public function __construct() {
        $this->isics = new ArrayCollection();
    }
	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getName(){
		return $this->name;
	}
	public function setName($name){
		$this->name = $name;
	}

	public function getIsics(){
		return $this->isics;
	}
	public function setIsics($isics){
		$this->isics = $isics;
	}
}