<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
              ->add('file', FileType::class, [
                'label_format' => 'Imágen de la veterinaria',
                'required' => true,
                'label_attr' => [
                    'class' => 'form-control js-dropzone',
                ],
                'attr' => [
                    'accept' => '.png,.jpg,.jpeg',
                ],
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
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
