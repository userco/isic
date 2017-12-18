<?php

namespace ISICBundle\Security;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use ISICBundle\Entity as AdminEntity;

class Authorization {
	
	public static $allowed = array(
		//'uciindex',
		'logout',
		'login'
	);

	/**
	 * @var \Symfony\Component\Security\Core\SecurityContextInterface
	 */
	protected $securityContext;

	/**
	 * @var \Symfony\Component\Routing\RouterInterface
	 */
	protected $router;

	/**
	 * @var \Symfony\Bridge\Doctrine\RegistryInterface
	 */
	protected $registry;
	private $_queryCountRoute;

	function __construct(SecurityContextInterface $securityContext, RouterInterface $router, RegistryInterface $registry) {
		$this->securityContext = $securityContext;
		$this->router = $router;
		$this->registry = $registry;
	}

	public function onKernelController(FilterControllerEvent $event) {
		if (!$this->securityContext->getToken() instanceof AdminToken) {
			return;
		}
		
		$routeId = $event->getRequest()->attributes->get('_route');
		$route = $this->router->getRouteCollection()->get($routeId);
		// if (!($route instanceof Route)) {
		// 	return;
		// }

		// if (!preg_match('#^/admin/.*#', $route->getPath())) {
		// 	return;
		// }
		$user = $this->securityContext->getToken()->getUser();
		if (!($user instanceof AdminEntity\User)) {
			return;
		}
		 if(!$user) //return;
		 	throw new AccessDeniedException('Вие нямате достъп до тази страница.');
		/* @var $user \ISICBundle\Entity\User */
		if(!$this->isAllowed($user, $routeId)) {
			throw new AccessDeniedException('Вие нямате достъп до тази страница.');
		}
	}

	public function isAllowed( $user, $routeId) {

		if (!($user instanceof AdminEntity\User)) {
			die("Нямате достъп до тази страница");
			return false;
		}
		if(in_array($routeId, static::$allowed, TRUE)) {
			return true;
		}
		//if($routeId == 'login') return true;
		if (!$this->_queryCountRoute) {
			$builder = $this->registry->getManager()->createQueryBuilder();
			/* @var $builder \Doctrine\ORM\QueryBuilder */
			$this->_queryCountRoute = $builder->select($builder->expr()->count('t.id'))
				->from('ISICBundle:User', 'u')
				->leftJoin('u.userRoles', 'r')
				->leftJoin('r.permissions', 't')
				->where($builder->expr()->eq('t.name', ':route'))
				//->andWhere($builder->expr()->eq('r.active', $builder->expr()->literal(true)))
				->andWhere($builder->expr()->eq('u', ':user'))
				->getQuery();
		}
		
		return (boolean)$this->_queryCountRoute->setParameter('route', $routeId)
			->setParameter('user', $user)
			->getSingleScalarResult();
	}
	
	public function getUser() {
		if($this->securityContext->getToken() instanceof AdminToken) {
			return $this->securityContext->getToken()->getUser();
		}
		return null;
	}

}
