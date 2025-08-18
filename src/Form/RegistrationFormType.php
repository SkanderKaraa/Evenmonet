<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Full Name *'],
                'constraints' => [
                    new NotBlank(['message' => 'Entrez votre nom complet']),
                    new Length(['min' => 3, 'max' => 50]),
                ],
            ])
            ->add('username', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Username *'],
                'constraints' => [
                    new NotBlank(['message' => 'Entrez un nom d\'utilisateur']),
                    new Length(['min' => 3, 'max' => 25]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Email *'],
                'constraints' => [
                    new NotBlank(['message' => 'Entrez une adresse email']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => false,
                'attr' => ['placeholder' => 'Password *'],
                'constraints' => [
                    new NotBlank(['message' => 'Entrez un mot de passe']),
                    new Length(['min' => 6]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'registration_form',
        ]);
    }
}
