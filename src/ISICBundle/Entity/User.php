<?php
// src/ISICBundle/Entity/User.php
namespace ISICBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="ISICBundle\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    private $plainPassword;
    protected $userRoles;
    protected $roles;
    public function __construct()
    {
        $this->isActive = true;
        $this->userRoles = new \Doctrine\Common\Collections\ArrayCollection();
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid('', true));
    }

    public function setUsername($username){
        $this->username = $username;
    }
    public function getUsername()
    {
        return $this->username;
    }

    public function setEmail($email){
        $this->email = $email;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }
    public function setPassword($password){
        $this->password = $password;
    }
    public function getPassword()
    {
        return $this->password;
    }

    // public function getRoles()
    // {
    //     return array('ROLE_USER');
    // }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }
     public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }
    public function getId(){
        return $this->id;
    }
    public function setId($id){
        $this->id = $id;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    public function addUserRole(ISICBundle\Entity\Role $role)
    {
        $this->userRoles[] = $role;
    }

    public function getUserRoles(){
        return $this->userRoles;

    }
    public function getRoles(){
         return array('ROLE_USER');
    }
   
}
