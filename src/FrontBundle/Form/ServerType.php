<?php

namespace FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('host', null, array('attr' => array('placeholder' => 'domain.com or xxx.xxx.xxx.xxx')))
            ->add('port', null, array('attr' => array('placeholder' => '22')))
            ->add('username')
            ->add('default_directory', null, array('attr' => array('placeholder' => '/tmp')))
            ->add('save', SubmitType::class, array('label' => 'form.save',  'translation_domain' => 'FrontBundle'));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FrontBundle\Entity\Server'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'frontbundle_server';
    }


}
