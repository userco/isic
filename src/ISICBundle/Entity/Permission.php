<?php
namespace ISICBundle\Entity;

//use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity()
 */
class Permission{
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
	/**
     * Many Roles have Many Pemissions.
     * @ORM\ManyToMany(targetEntity="ISICBundle\Entity\Role", inversedBy="permissions")
     * @ORM\JoinTable(name="roles_permissions",
     *  joinColumns={
     *      @ORM\JoinColumn(name="permission_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *  })
     */
	private $userRoles;
	public function __construct() {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
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
