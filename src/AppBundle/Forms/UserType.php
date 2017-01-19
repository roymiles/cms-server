<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
         * This will generate a registration form
         */
        $builder->add('Username', TextType::class)
                ->add('Email', EmailType::class)
                ->add('Password', PasswordType::class);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => 'AppBundle\Entity\Users'
        ));
    }
    
    public function getName(){
        return "app_bundle_user_type";
    }
    
}