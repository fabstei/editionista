<?php

namespace Editionista\WebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as Controller;

class AboutController extends Controller
{
    /**
     * @Template()
     * @Route("/about", name="about")
     */
    public function aboutAction()
    {
        return array();
    }
}
