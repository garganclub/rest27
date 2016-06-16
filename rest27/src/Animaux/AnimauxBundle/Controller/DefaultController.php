<?php

namespace Animaux\AnimauxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $animaux = $em->getRepository('AnimauxBundle:Animal')->findBy(array(), array('id' => 'DESC'));
        return $this->render('AnimauxBundle:Default:index.html.twig', array(
        	'animaux' => $animaux
        ));
    }
}
