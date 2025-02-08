<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
            ])
            ->add('price', NumberType::class, [
                'required' => false,
                'scale'    => 2,
                'attr'     => ['min' => 0, 'max' => 200],
            ])
            ->add('photo', TextType::class, [
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('alzaUrl', UrlType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'URL товара с alza.cz',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
