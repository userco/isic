<?php
namespace ISICBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity()
 */
class Role{
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	protected $id;
	
	protected $name;
	/***
     *
     * @ORM\ManyToMany(targetEntity="ISICBundle\Entity\Permission", mappedBy="roles")
     */
    private $permissions;
    protected $users;

	public function __construct() {
        $this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
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
	public function getPermissions(){
		return $this->permissions;
	}
}
