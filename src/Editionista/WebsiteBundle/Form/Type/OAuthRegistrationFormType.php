<?php

/*
 * This file is part of Packagist.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *     Nils Adermann <naderman@naderman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Editionista\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OAuthRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle', 'attr' => array('placeholder' => 'testuser')))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle', 'attr' => array('help_block' => 'Secret, required', 'placeholder' => 'user@example.com')))
            ->add('homepage', 'url', array('required' => false,'label' => 'Homepage:', 'attr' => array('help_block' => 'Public, not required', 'placeholder' => 'http://example.com')))
            ->add('gravatar', 'hidden', array('data' => 'test', 'attr' => array()))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Editionista\WebsiteBundle\Entity\User',
            'intention'  => 'registration',
            'validation_groups' => array('Default', 'Profile'),
        ));
    }

    public function getName()
    {
        return 'editionista_oauth_user_registration';
    }
}
