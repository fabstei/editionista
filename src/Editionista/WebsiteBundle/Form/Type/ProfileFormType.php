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

use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('homepage', 'url', array('required' => false, 'label' => 'Website'));
        $builder->remove('current_password');
    }

    public function getName()
    {
        return 'editionista_user_profile';
    }
}
