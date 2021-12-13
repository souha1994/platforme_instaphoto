<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType1 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            // ->add('roles', ChoiceType::class, [
            //     'required' => true,
            //     'multiple' => false,
            //     'expanded' => false,
            //     'choices' => [
            //         'User' => 'ROLE_USER',
            //         'Admin' => 'ROLE_ADMIN'
            //     ]
            // ])
            ->add('password', PasswordType::class)
        ;
        // $builder->get('roles')->addModelTransformer(new CallbackTransformer(
        //     function ($rolesArray){
        //         return count($rolesArray) ? $rolesArray[0] : null;
        //     },
        //     function ($rolesString){
        //         return [$rolesString];
        //     }
        // ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
