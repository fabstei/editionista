<?php

namespace Editionista\WebsiteBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Editionista\WebsiteBundle\Entity\User;
use Editionista\WebsiteBundle\Entity\Edition;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as Controller;

class UserController extends Controller
{
    public function myProfileAction(Request $req)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $editions = $user->getEditions();

        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:Profile:show.html.'.$this->container->getParameter('fos_user.template.engine'),
            array(
                'editions' => $editions,
                'user' => $user,
            )
        );
    }

    /**
     * @Template()
     * @Route("/user/{name}/", name="user_profile")
     * @ParamConverter("user", options={"mapping": {"name": "username"}})
     */
    public function profileAction(User $user)
    {
        $editions = $user->getEditions();

        return array(
            'editions' => $editions,
            'user' => $user,
        );
    }
}