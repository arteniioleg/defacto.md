<?php

namespace App\Form;

use App\Consts;
use App\Entity\Politician;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PoliticianType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'label.first_name',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'label.last_name',
            ])
            ->add('slug', TextType::class, [
                'label' => 'label.slug',
                'attr' => ['data-slug-from' => 'politician[firstName],politician[lastName]'],
            ])
            ->add('photo', FileType::class, [
                'label' => 'label.photo',
                'required' => false,
            ])
            ->add('birthDate', DateType::class, [
                'label' => 'label.birth_date',
                'widget' => 'single_text',
                'format' => Consts::DATE_FORMAT_INTL,
                'required' => false,
            ])
            ->add('studies', TextareaType::class, [
                'label' => 'label.studies',
                'required' => false,
                'attr' => ['class' => 'wysiwyg'],
            ])
            ->add('profession', TextType::class, [
                'label' => 'label.profession',
            ])
            ->add('website', TextType::class, [
                'label' => 'label.website',
            ])
            ->add('facebook', TextType::class, [
                'label' => 'label.facebook',
            ])
            ->add('email', TextType::class, [
                'label' => 'label.email',
            ])
            ->add('phone', TextType::class, [
                'label' => 'label.phone',
            ])
            ->add('previousTitles', TextareaType::class, [
                'label' => 'label.previous_titles',
                'required' => false,
                'attr' => ['class' => 'wysiwyg'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Politician::class,
        ]);
    }
}
