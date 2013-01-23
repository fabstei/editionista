<?php

namespace Editionista\WebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Collections\ArrayCollection;
use Editionista\WebsiteBundle\Entity\Edition;
use Editionista\WebsiteBundle\Entity\Tag;
use Editionista\WebsiteBundle\Form\Type\EditionType;
use Gedmo\Sluggable\Util\Urlizer;

/**
 * Edition controller.
 *
 * @Route("/")
 */
class EditionController extends Controller
{
    
    // Todo: Github Hooks to update the repositories; http://developer.github.com/v3/repos/hooks/#create-a-hook / https://help.github.com/articles/post-receive-hooks
    
    /**
     * Lists all Edition entities.
     *
     * @Route("/", name="home")
     * @Route("/editions", name="editions")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EditionistaWebsiteBundle:Edition')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Displays a form to create a new Edition entity.
     *
     * @Route("edition/add", name="edition_add_list")
     * @Route("edition/add/{user}", name="edition_add_list_user")
     * @Method("GET")
     * @Template()
     */
    public function addListAction()
    {
        
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EditionistaWebsiteBundle:Edition')->findAll();
        $localRepos = array();
        
        foreach($entities as $entity) {
            $localRepos[] = $entity->getSlug();
        }
        
        $client = new \Github\Client(
            new \Github\HttpClient\CachedHttpClient(array('cache_dir' => '/tmp/github-api-cache'))
        );
        
        try {
            $user = $this->getUser();
            if($user) {
                $githubToken = $user->getGithubToken();
                
                $method = \Github\Client::AUTH_HTTP_TOKEN;
                $client->authenticate($githubToken, null, $method);

                $user = $client->api('current_user')->show();
                $username = $user['login'];
                
                // Todo: Investigate why the sorting is ignored
                $parameters = array(
                    'sort' => 'pushed',
                );
                
                $repositories = $client->api('user')->repositories($username, $parameters);
            }
        }
        catch (\Exception $e) {
            $user = null;
            $repositories = null;
        }
        
        $githubRepos = array();
        
        foreach($repositories as $key=>$repository) {
            $slug = Urlizer::transliterate($repository['full_name']);
            if(in_array($slug, $localRepos)) {
                $repositories[$key]['exists'] = $slug;
            }
            
        }
                        
        return array('user' => $user, 'repositories' => $repositories);
    }

    /**
     * Displays a form to create a new Edition entity.
     *
     * @Route("edition/add/{name}/{repo}", name="edition_add")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function addAction($name, $repo, Request $request)
    {
        if ($request->getMethod() == 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            $entity  = new Edition();
            
            $user = $this->get('security.context')->getToken()->getUser();
            $entity->addUser($user);

            $postedTags = $request->request->get('hidden-tags');
            $postedTags = explode(',', $postedTags);
            
            foreach($postedTags as $key => $tagName)
            {
                // Not required when Tags are already submitted to /tags/push
                // $tag[$key] = new Tag();
                // $tag[$key]->setName($tagName);
                // $entity->addTag($tag[$key]);
                
                $tag = $em->getRepository('EditionistaWebsiteBundle:Tag')->findOneByName($tagName);
                $entity->addTag($tag);
            }
                        
            $fetch_repository = $this->get('session')->getFlashBag()->get('github-reponse');
            $fetch_repository = $fetch_repository[0];
            
            //Todo Better integration https://api.travis-ci.org/docs/#POST%20/auth/github
            $travis = $this->get('session')->getFlashBag()->get('travis');
            if(isset($travis[0])) { $entity->setTravis(1); }            
                        
            $entity->setRepository($fetch_repository['full_name']);
            $entity->setTravis('test');
            $entity->setPackagist('test');
            $entity->setUpdatedAt(new \DateTime());
            $entity->setUpdatedRepositoryAt(new \DateTime($fetch_repository['pushed_at']));
                        
            $form = $this->createForm(new EditionType(), $entity);
            $form->bind($request);

            if ($form->isValid()) {
                
                $em->persist($entity);
                $em->flush();
                
                // creating the ACL
                $aclProvider = $this->get('security.acl.provider');
                $objectIdentity = ObjectIdentity::fromDomainObject($entity);
                $acl = $aclProvider->createAcl($objectIdentity);

                // retrieving the security identity of the currently logged-in user
                $securityContext = $this->get('security.context');
                $user = $securityContext->getToken()->getUser();
                $securityIdentity = UserSecurityIdentity::fromAccount($user);
                $adminSecurityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');

                // grant owner access
                $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
                $acl->insertObjectAce($adminSecurityIdentity, MaskBuilder::MASK_OWNER);
                $aclProvider->updateAcl($acl);
            
            
                return $this->redirect($this->generateUrl('edition_show', array('slug' => $entity->getSlug())));
            
                // Form invalid
            } else {
                $updatedRepositoryAt = null;
            }
        
        // GET Request
        } else {
            $client = new \Github\Client(
                new \Github\HttpClient\CachedHttpClient(array('cache_dir' => '/tmp/github-api-cache'))
            );

            try {
                $user = $this->getUser();

                if($user) {
                    $githubToken = $user->getGithubToken();

                    $method = \Github\Client::AUTH_HTTP_TOKEN;
                    $client->authenticate($githubToken, null, $method);

                    $fetch_repository = $client->api('repo')->show($name, $repo);

                    $description = $fetch_repository['description'];
                    $homepage = $fetch_repository['homepage'];
                    $repository = $fetch_repository['full_name'];
                                        
                    $updatedRepositoryAt = new \DateTime($fetch_repository['pushed_at']);

                    $this->get('session')->getFlashBag()->add('github-reponse', $fetch_repository);

                }

            }
            catch (\Exception $e) {
                $fetch_repository = null;
                $description = null;
                $homepage = null;
                $updatedRepositoryAt = null;
            }

            try {
                $readme = $client->api('repo')->contents()->readme($name, $repo);
                $readme = base64_decode($readme['content']);
            }
            catch (\Exception $e) {
                $readme = null;
            }
            
            try {
                $license = $client->api('repo')->contents()->show($name, $repo, 'LICENSE');
                $license = base64_decode($license['content']);
                
                // TODO: Better License detection
                if(strpos($license, 'Permission is hereby granted, free of charge') !== false) {
                    $licenseType = 'mit';
                } elseif(strpos($license, 'Redistribution and use in source and binary forms') !== false) {
                    $licenseType = 'bsd';
                } elseif(strpos($license, 'Apache') !== false) {
                    $licenseType = 'apache';
                } else {
                    $licenseType = 'other';
                }
                
            }
            catch (\Exception $e) {
                $license     = null;
                $licenseType = null;
            }
            
            try {
                $travis = $client->api('repo')->contents()->show($name, $repo, '.travis.yml');
                
                if($travis != null) {
                    $this->get('session')->getFlashBag()->add('travis', 'exists');
                }
            }
            catch (\Exception $e) {
                $travis     = false;
            }
            
            $entity = new Edition();

            $entity->setHomepage($homepage);
            $entity->setReadme($readme);
            $entity->setLicense($license);
            $entity->setLicenseType($licenseType);
            $entity->setDescription($description);
        }
                
        $form   = $this->createForm(new EditionType(), $entity);
        
        return array(
            'entity'    => $entity,
            'form'      => $form->createView(),
            'name'      => $name,
            'repo'      => $repo,
            'last_push' => $updatedRepositoryAt,
        );
    }

    /**
     * Finds and displays a Edition entity.
     *
     * @Route("edition/{slug}", name="edition_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EditionistaWebsiteBundle:Edition')->findOneBySlug($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Edition entity.');
        }
        
        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Edition entity.
     *
     * @Route("edition/{slug}/edit", name="edition_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EditionistaWebsiteBundle:Edition')->findOneBySlug($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Edition entity.');
        }
        
        $editForm = $this->createForm(new EditionType(), $entity);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Edition entity.
     *
     * @Route("edition/{slug}/edit", name="edition_update")
     * @Method("POST")
     * @Template("EditionistaWebsiteBundle:Edition:edit.html.twig")
     */
    public function updateAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EditionistaWebsiteBundle:Edition')->findOneBySlug($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Edition entity.');
        }
        
        $postedTags = $request->request->get('hidden-tags');
        $postedTags = explode(',', $postedTags);
        
        $tags = array();
        
        foreach($postedTags as $tagName)
        {
            $tags[] = $em->getRepository('EditionistaWebsiteBundle:Tag')->findOneByName($tagName);
        }
        
        $entity->setTags($tags);

        $editForm = $this->createForm(new EditionType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('edition_edit', array('slug' => $slug)));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Edition entity.
     *
     * @Route("edition/{slug}/delete", name="edition_delete")
     */
    public function deleteAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EditionistaWebsiteBundle:Edition')->findOneBySlug($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Edition entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('user_profile', $this->get('security.context')->getToken()->getUser()->getUsername()));
    }
}
