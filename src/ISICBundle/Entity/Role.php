<?php
namespace ISICBundle\Entity;

//use Doctrine\ORM\Mapping as ORM;
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
	/**
     * @ORM\Column(type="string", length="100")
     */
	protected $name;
	/***
     *
     * @ORM\ManyToMany(targetEntity="ISICBundle\Entity\Permission", mappedBy="roles")
     */
    private $permissions;

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
}
