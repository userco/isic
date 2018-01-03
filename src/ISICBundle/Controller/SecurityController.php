<?php
// src/Controller/SecurityController.php
namespace  ISICBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use ISICBundle\Entity\User;
use ISICBundle\Entity\Role;
use ISICBundle\Form\RoleType;
use ISICBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Session\Session;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
    	$authUtils = $this->get('security.authentication_utils');

    	 // get the login error if there is one
	    $error = $authUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authUtils->getLastUsername();

	    return $this->render('security/login.html.twig', array(
	        'last_username' => $lastUsername,
	        'error'         => $error,
	    ));
    }

    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request)
    {
        // 1) build the form
        //$usr= $this->get('security.context')->getToken()->getUser();
        //var_dump($usr->getUsername());
        $session = new Session();
        //$session->start();
        $user = new User();
        $checked = array();
        $checked = $user->getUserRoles();
        $form = $this->createForm(new UserType($checked), $user);
       // var_dump($user->getUsername());
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form->get('username')->getData();
            $existing_user =$this->getDoctrine()->getRepository('ISICBundle:User')->findOneByUsername($username);
            if($existing_user){
                $session->getFlashBag()->add('error', 'Потребителското име вече съществува.');
                return $this->redirectToRoute('user_registration');
            }
            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            
             $array_roles = $form->get('userRoles')->getData();
            //var_dump($array_roles);
            // foreach($array_roles as $r){
            //     $array_roles = $this->getDoctrine()->getRepository('ISICBundle:Role')->findOneById($r)->getName();
            //     //$user->addRole($role);
            // }
            // var_dump($array_roles);
            // $user->setUserRoles($array_roles);
            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

           // return $this->redirectToRoute('login');
        }

        return $this->render(
            'security/register.html.twig',
            array('form' => $form->createView())
        );
    }
    /**
     * @Route("/edit_user/{userId}", name="edit_user")
     */
    public function editUserAction(Request $request, $userId)
    {
        // 1) build the form
        $session = new Session();
        $user = $this->getDoctrine()->getRepository('ISICBundle:User')->find($userId);
        $checked = array();
        $checked = $user->getUserRoles();
        $form = $this->createForm(new UserType($checked), $user);

        
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
           

            
        }

        return $this->render(
            'security/edit_user.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/list_users", name="list_users")
     */
    public function listUsersAction(Request $request){
    	$users = $this->getDoctrine()->getRepository("ISICBundle:User")->findAll();
    	return $this->render(
            'security/list_users.html.twig',
            array(
            	'users'=>$users)
        );
    }
    //*************************************************************************************************

    /**
     * @Route("/role_create", name="role_create")
     */
    public function roleCreateAction(Request $request)
    {
        // 1) build the form
        $session = new Session();
        $role = new Role();
        $checked = array();
        $checked = $role->getPermissions();
        
        $form = $this->createForm(new RoleType($checked), $role);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $name = $form->get('name')->getData();
            $existing_role =$this->getDoctrine()->getRepository('ISICBundle:Role')->findOneByName($name);
            if($existing_role){
                $session->getFlashBag()->add('error', 'Ролята вече съществува.');
              return $this->render(
            'security/roles/create_role.html.twig',
            array('form' => $form->createView())
        );
           }

            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $em->flush();

            
            return $this->redirectToRoute('list_roles');
        }

        return $this->render(
            'security/roles/create_role.html.twig',
            array('form' => $form->createView())
        );
    }
    /**
     * @Route("/edit_role/{roleId}", name="edit_role")
     */
    public function editRoleAction(Request $request, $roleId)
    {
        // 1) build the form
        $session = new Session();
        $role = $this->getDoctrine()->getRepository('ISICBundle:Role')->find($roleId);
        $checked = array();
        $checked = $role->getPermissions();
        
        $form = $this->createForm(new RoleType($checked), $role);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            
            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            
            
            
        }

        return $this->render(
            'security/roles/edit_role.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/list_roles", name="list_roles")
     */
    public function listRolesAction(Request $request){
        $roles = $this->getDoctrine()->getRepository("ISICBundle:Role")->findAll();
        return $this->render(
            'security/roles/list_roles.html.twig',
            array(
                'roles'=>$roles)
        );
    }
}
