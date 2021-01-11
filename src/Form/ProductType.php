<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('price')
            ->add('description')
            //->add('images', FileType::class, [
                //'mapped' => false,
                /*'constraints' => [
                    new File([
                        'maxSize' => '10240k',
                        'binaryFormat' => false,
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'jpeg',
                        ],
                        'mimeTypesMessage' => 'Por favor ingrese una imágen png o jpg',
                    ]),
                ],*/
            //])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
