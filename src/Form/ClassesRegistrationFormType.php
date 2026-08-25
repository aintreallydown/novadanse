<?php

namespace App\Form;

use App\Entity\ClassesRegistration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;

class ClassesRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('produit', ChoiceType::class, [
                'label' => 'Cours choisis',
                'choices' => ClassesRegistration::getCoursChoices(),
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('contactUrgence', TextType::class, [
                'label' => "Nom du contact d'urgence"
            ])
            ->add('telephoneContactUrgence', TelType::class, [
                'label' => "Téléphone du contact d'urgence"
            ])
            ->add('needMedicalCertificate', CheckboxType::class, [
                'required' => false
            ])
            ->add('medicalCertificateFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['application/pdf', 'image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Merci de déposer un PDF ou une image valide.',
                    ]),
                ],
            ])
            ->add('ancienAdherent', CheckboxType::class, [
                'required' => false
            ])
            ->add('passPortCode', TextType::class, [
                'required' => false
            ])
            ->add('autorisationParentale', CheckboxType::class, [
                'required' => false
            ])
            ->add('droitImage', CheckboxType::class, [
                'required' => false
            ])
            ->add('cgv', CheckboxType::class, [
                'constraints' => [new IsTrue(message: 'Vous devez accepter les CGV.')],
            ])
            ->add('reglementInterieur', CheckboxType::class, [
                'constraints' => [new IsTrue(message: 'Vous devez accepter le règlement intérieur.')],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClassesRegistration::class,
        ]);
    }
}
