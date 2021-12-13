<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactUsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject')
            // ->add('from', EmailType::class)
            // ->add('to', EmailType::class)
            ->add('body', TextareaType::class);
    }
//     /**
//  * {@inheritdoc}
//  */
//     // public function configureOptions(OptionsResolver $resolver)
//     // {
//     //     $resolver->setDefaults(array(
//     //         'data_class' => 'AppBundle\Entity\ContactUs'
//     //     ));
//     // }

    // /**
    //  * {@inheritdoc}
    //  */
    // public function getBlockPrefix()
    // {
    //     return 'Sitebundle_contactUs';
    // }


}