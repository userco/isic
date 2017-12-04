<?php

namespace ISICBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ISICBundle:Default:index.html.twig');
    }
}
