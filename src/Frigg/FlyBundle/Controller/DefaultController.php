<?php

namespace Frigg\FlyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="frigg_fly_homepage")
     */
    public function indexAction($name)
    {
        return $this->render('FriggFlyBundle:Default:index.html.twig', array('name' => $name));
    }
}
