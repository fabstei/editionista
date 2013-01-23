<?php

namespace Editionista\WebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as Controller;
use Editionista\WebsiteBundle\Entity\Tag;

/**
 * Edition controller.
 *
 * @Route("/tags")
 */
class TagController extends Controller
{
    /**
     * @Route("/", name="tags")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tags = $em->getRepository('EditionistaWebsiteBundle:Tag')->findAll();
        
        return array('tags' => $tags);
    }
    
    /**
     * @Route("/{tag}", name="show_tag")
     * @Template()
     */
    public function showAction($tag)
    {
        $em = $this->getDoctrine()->getManager();

        $tag = $em->getRepository('EditionistaWebsiteBundle:Tag')->findOneByName($tag);
        $editions = $tag->getEditions();
        
        return array('tag' => $tag, 'editions' => $editions);
    }
    
    /**
     * @Route("/push", name="push_tags")
     * @Method({"POST"})
     */
    public function pushAction(Request $request)
    {
        $pushedTag = $request->request->get('tag');

        $em = $this->getDoctrine()->getManager();

        $storedTag = $em->getRepository('EditionistaWebsiteBundle:Tag')->findOneByName($pushedTag);
        
        if($storedTag == null) {
            $tag = new Tag();
            $tag->setName($pushedTag);

            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();
        }

        $response = new Response(json_encode(array('tag' => $pushedTag)));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
    
    /**
     * @Route("/pull", name="pull_tags")
     */
    public function pullAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EditionistaWebsiteBundle:Tag')->findAll();
        
        $tags = array();
        
        foreach($entities as $entity) {
            $tags['tags'][] = array('tag' => $entity->getName());
        }

        $response = new Response(json_encode($tags));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
