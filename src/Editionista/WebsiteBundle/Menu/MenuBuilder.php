<?php

namespace Editionista\WebsiteBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class MenuBuilder
{
    private $factory;
    private $translator;
    private $securityContext;
    private $isLoggedIn;
    private $isSuperAdmin;
    private $isAdmin;
    private $isSwitched;
    private $isUser;
    private $user;
    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, Translator $translator, SecurityContextInterface $securityContext)
    {
        $this->factory         = $factory;
        $this->translator      = $translator;
        $this->securityContext = $securityContext;
        $this->isLoggedIn      = $this->securityContext->isGranted('IS_AUTHENTICATED_FULLY');
        $this->isSuperAdmin    = $this->securityContext->isGranted('ROLE_SUPER_ADMIN');
        $this->isAdmin         = $this->securityContext->isGranted('ROLE_ADMIN');
        $this->isSwitched      = $this->securityContext->isGranted('ROLE_PREVIOUS_ADMIN');
        $this->isUser          = $this->securityContext->isGranted('ROLE_USER');
        $this->user            = $this->securityContext->getToken()->getUser();
    }

    public function createMainNav(Request $request)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Editions', array('route' => 'home'));
        $menu->addChild('About', array('route' => 'about'));

        return $menu;
    }
    
    public function createRightNav(Request $request)
    {
        $menu = $this->factory->createItem('root');

        if ($this->isLoggedIn) {
            
            $menu->addChild('Submit Edition', array('route' => 'edition_add_list'));
            $menu->addChild('My Editions', array('route' => 'user_profile', 'routeParameters' => array('name' => $this->user->getUsername())));
            
            if ($this->isAdmin) {
                $menu->addChild('Manage Versions', array('route' => 'version'));
            }
            
            $dropdown = $menu->addChild($this->user->getUsername());
            $dropdown->addChild($this->translator->trans('menu.profile'), array('route' => 'fos_user_profile_show'));
            $dropdown->addChild('d1', array('attributes' => array('divider' => true)));
            $dropdown->addChild($this->translator->trans('menu.logout'), array('route'=>'logout'));

            if ($this->isSwitched) {
                $dropdown->addChild('d2', array('attributes' => array('divider' => true)));
                $dropdown->addChild($this->translator->trans('menu.switchback'), array('uri'=>'?_switch_user=_exit'));

            } elseif ($this->isAdmin) {
                                
                $dropdown->addChild('d2', array('attributes' => array('divider' => true)));
                $dropdown->addChild($this->translator->trans('menu.switchtouser'), array('uri'=>'?_switch_user=user'));

                if ($this->isSuperAdmin) {
                    $dropdown->addChild($this->translator->trans('menu.switchtoadmin'), array('uri'=>'?_switch_user=admin'));
                }
            }

        } else {
            $menu->addChild($this->translator->trans('menu.login'), array('route' => 'login'));
        }

        return $menu;
    }
}