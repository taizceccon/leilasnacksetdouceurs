<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Adresse e-mail',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une adresse e-mail']),
                    new Email(['message' => 'L\'adresse e-mail n\'est pas valide']),
                ],
                'attr' => ['placeholder' => 'exemple@domaine.com'],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom complet',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire']),
                    new Length(['max' => 100, 'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères']),
                ],
                'attr' => ['placeholder' => 'Votre nom'],
            ])
            ->add('adresse', TextType::class, [
                'required' => false,
                'label' => 'Adresse',
                'attr' => ['placeholder' => 'Adresse postale'],
                'constraints' => [
                    new Length(['max' => 255, 'maxMessage' => 'L\'adresse ne peut pas dépasser {{ limit }} caractères']),
                ],
            ])
            ->add('telephone', TextType::class, [
                'required' => false,
                'label' => 'Téléphone',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^0[1-9](\s?\d{2}){4}$/',
                        'message' => 'Le numéro de téléphone est invalide.',
                    ]),
                ],
                'attr' => ['placeholder' => 'Ex: 01 23 45 67 89'],
            ])          
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Mot de passe'],
                'label' => 'Mot de passe', 
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un mot de passe']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ])
              ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les conditions générales',
                'constraints' => [
                    new IsTrue(['message' => 'Vous devez accepter nos conditions.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
