<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('token', null, array('label' => 'form.token', 'translation_domain' => 'UserBundle'));
        $builder->add('save', SubmitType::class, array('label' => 'form.save',  'translation_domain' => 'UserBundle'));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}