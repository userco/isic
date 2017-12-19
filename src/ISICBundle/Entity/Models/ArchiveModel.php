<?php
//автор Мария Пенелова
namespace ISICBundle\Entity\Models;

class ArchiveModel{

	protected $id;
	protected $generateDateFrom;
	protected $generateDateTo;

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getGenerateDateFrom(){
		return $this->generateDateFrom;
	} 

	public function setGenerateDateFrom($date){
		$this->generateDateFrom = $date;
	}

	public function getGenerateDateTo(){
		return $this->generateDateTo;
	} 

	public function setGenerateDateTo($date){
		$this->generateDateTo = $date;
	}
}
