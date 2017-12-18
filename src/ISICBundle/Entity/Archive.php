<?php
//автор Мария Пенелова
namespace ISICBundle\Entity;

class Archive{

	protected $id;
	protected $generateDate;
	protected $archiveName;

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getGenerateDate(){
		return $this->generateDate;
	} 

	public function setGenerateDate($date){
		$this->generateDate = $date;
	}

	public function getArchiveName(){
		return $this->archiveName;
	}
	public function setArchiveName($archive){
		$this->archiveName = $archive;
	}
}