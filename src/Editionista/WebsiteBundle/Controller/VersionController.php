<?php

namespace Editionista\WebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Editionista\WebsiteBundle\Entity\Version;
use Editionista\WebsiteBundle\Form\Type\VersionType;

/**
 * Version controller.
 *
 * @Route("/version")
 */
class VersionController extends Controller
{
    /**
     * Lists all Version entities.
     *
     * @Route("/", name="version")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EditionistaWebsiteBundle:Version')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Version entity.
     *
     * @Route("/", name="version_create")
     * @Method("POST")
     * @Template("EditionistaWebsiteBundle:Version:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Version();
        $form = $this->createForm(new VersionType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('version'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Version entity.
     *
     * @Route("/new", name="version_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Version();
        $form   = $this->createForm(new VersionType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Version entity.
     *
     * @Route("/{id}", name="version_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EditionistaWebsiteBundle:Version')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Version entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Version entity.
     *
     * @Route("/{id}/edit", name="version_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EditionistaWebsiteBundle:Version')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Version entity.');
        }

        $editForm = $this->createForm(new VersionType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Version entity.
     *
     * @Route("/{id}", name="version_update")
     * @Method("POST")
     * @Template("EditionistaWebsiteBundle:Version:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EditionistaWebsiteBundle:Version')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Version entity.');
        }

        $editForm = $this->createForm(new VersionType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('version_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Version entity.
     *
     * @Route("/{id}/delete", name="version_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EditionistaWebsiteBundle:Version')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Version entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('version'));
    }

}
