<?php

namespace Editionista\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VersionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => false, 'attr' => array('placeholder' => 'Symfony2 Version used')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Editionista\WebsiteBundle\Entity\Version'
        ));
    }

    public function getName()
    {
        return 'editionista_websitebundle_versiontype';
    }
}
