<?php

namespace App\Form;

use App\Entity\Pictures;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PicturesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label')
            ->add('link', fileType::class, [
                'data_class'=> null, 
                'label' => 'Image :',
                'mapped' => false,
                'required' => false,              
            ])
            // ->add('tricks')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pictures::class,
        ]);
    }
}
