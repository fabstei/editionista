<?php

namespace Editionista\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EditionType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text',
                    array('label' => 'Edition name', 'attr' => array('placeholder' => 'Awesome Edition')))
            ->add('reference', 'text',
                    array('label' => 'Exact version or commit', 'required' => false,
                        'attr' => array('help_block' => '(Commit with version "dev-master")', 'placeholder' => 'f.e. "2.1.10" or "efabb1e"')))
            ->add('homepage', 'text',
                    array('required' => false,
                        'attr' => array('placeholder' => 'http://example.com')))
            ->add('readme', 'textarea',
                    array('required' => false,
                        'attr' => array('placeholder' => 'Markdown readme Information', 'rows'  => '12', 'class' => 'input-xxlarge')))
            ->add('license', 'textarea',
                    array('required' => false,
                        'attr' => array('placeholder' => 'License Information', 'rows'  => '10', 'class' => 'input-xxlarge')))
            ->add('licenseType', 'choice',
                    array('choices'   => array('other' => 'other', 'MIT' => 'MIT', 'BSD' => 'BSD', 'Apache' => 'Apache'), 'required' => true,
                        'attr' => array('placeholder' => 'License Type')))
            ->add('description', 'textarea',
                    array('required' => true,
                        'attr' => array('placeholder' => 'Short description', 'rows'  => '14', 'class' => 'input-xxlarge')))
            ->add('version', null, array('label' => 'Symfony2 version used', 'required' => true, 'attr' => array('help_block' => 'Symfony2 version this Edition is based on.')))
                
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Editionista\WebsiteBundle\Entity\Edition'
        ));
    }

    public function getName()
    {
        return 'editionista_websitebundle_editiontype';
    }
}
