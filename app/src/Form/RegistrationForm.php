<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class RegistrationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       $builder
    ->add('email')
    ->add('agreeTerms', CheckboxType::class, [
        'mapped' => false,
        'required' => true,
        'label' => 'I agree to terms',
        'constraints' => [
            new IsTrue(message: 'You should agree to our terms.'),
        ],
    ])
    ->add('plainPassword', PasswordType::class, [
        'mapped' => false,
        'attr' => ['autocomplete' => 'new-password'],
        'constraints' => [
            new NotBlank(message: 'Please enter a password'),
            new Length(min: 6, minMessage: 'Your password should be at least {{ limit }} characters', max: 4096),
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
