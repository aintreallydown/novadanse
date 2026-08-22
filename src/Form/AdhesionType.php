<?php

namespace App\Form;

use App\Entity\DossierAdhesion;
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

class AdhesionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('produit', ChoiceType::class, [
                'label' => 'Cours choisis',
                'choices' => DossierAdhesion::getCoursChoices(),
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('contactUrgence', TextType::class, [
                'label' => "Nom du contact d'urgence",
            ])
            ->add('telephoneContactUrgence', TelType::class, [
                'label' => "Téléphone du contact d'urgence",
            ])
            ->add('needMedicalCertificate', CheckboxType::class, [
                'label' => "Certificat médical requis",
                'required' => false,
            ])
            ->add('medicalCertificateFile', FileType::class, [
                'label' => 'Certificat médical (PDF/image)',
                'mapped' => false, // upload géré à part dans le contrôleur
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
                'label' => 'Ancien adhérent',
                'required' => false,
            ])
            ->add('passPortCode', TextType::class, [
                'label' => "Code Pass'Sport",
                'required' => false,
            ])
            ->add('autorisationParentale', CheckboxType::class, [
                'label' => 'Autorisation parentale (mineur)',
                'required' => false,
            ])
            ->add('droitAImage', CheckboxType::class, [
                'label' => "Droit à l'image",
                'required' => false,
            ])
            ->add('cgv', CheckboxType::class, [
                'label' => 'J\'accepte les CGV',
                'constraints' => [
                    new IsTrue(message: 'Vous devez accepter les CGV pour continuer.'),
                ],
            ])
            ->add('reglementInterieur', CheckboxType::class, [
                'label' => "J'accepte le règlement intérieur",
                'constraints' => [
                    new IsTrue(message: 'Vous devez accepter le règlement intérieur pour continuer.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DossierAdhesion::class,
        ]);
    }
}
