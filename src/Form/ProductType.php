<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('prix', MoneyType::class, [
                'label' => 'Prix (€)',
                'divisor' => 100,
                'currency' => 'EUR',
                'scale' => 2,
                'attr' => ['class' => 'border border-pink-300 rounded w-full p-2'],
            ])
             ->add('imageFile', FileType::class, [
                'label' => 'Image du produit (JPEG, PNG, GIF)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG, GIF)',
                    ])
                ],
                'attr' => ['class' => 'border border-pink-300 rounded w-full p-2'],
            ])
            ->add('urlvideo')
              ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'category', // <--- champ en BDD
                'label' => 'Catégorie',
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
