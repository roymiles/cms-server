<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AboutController extends Controller
{
    /**
     * @Route("/", name="About")
     */
    public function aboutAction(Request $request)
    {
        return $this->render('default/pages/about.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'activeTab' => 'about',
        ]);
    }
}
