<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType; // ajouté pour la date
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l’événement',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Budget estimé',
                'currency' => 'TND',
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('createdAt', DateType::class, [            // ajout du champ date
                'label' => 'Date de l\'événement',
                'widget' => 'single_text',
                'html5' => true,
                'required' => true,
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'label' => 'Image (jpg, png)',
                'allow_delete' => true,
                'download_uri' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
