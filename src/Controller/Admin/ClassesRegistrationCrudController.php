<?php

namespace App\Controller\Admin;


use App\Entity\ClassesRegistration;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ClassesRegistrationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ClassesRegistration::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Inscription')
            ->setEntityLabelInPlural('Inscriptions')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle(Crud::PAGE_DETAIL, fn(ClassesRegistration $c) => sprintf('%s %s', $c->getPrenom(), $c->getNom()))
            ->setPageTitle(Crud::PAGE_EDIT, fn(ClassesRegistration $c) => sprintf('%s %s', $c->getPrenom(), $c->getNom()));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('uid')
            ->formatValue(fn($value) => $value ? (string) $value : null)
            ->hideOnForm()
            ->hideOnIndex();

        yield AssociationField::new('user', 'Titulaire du dossier');

        yield TextField::new('prenom');

        yield TextField::new('nom');

        yield DateField::new('dateOfBirth', 'Date de naissance')
            ->hideOnIndex();

        yield DateTimeField::new('createdAt')
            ->hideOnForm();

        yield ArrayField::new('produit', 'Cours choisis')
            ->formatValue(fn($value, $entity) => implode(', ', $entity->getProduitLabels()));

        yield TextField::new('contactUrgence', "Contact d'urgence")
            ->hideOnIndex();

        yield TelephoneField::new('telephoneContactUrgence', "Téléphone d'urgence")
            ->hideOnIndex();

        yield BooleanField::new('needMedicalCertificate', 'Certificat médical requis')
            ->hideOnIndex();

        yield TextField::new('medicalCertificateFile', 'Fichier certificat')
            ->hideOnForm()
            ->hideOnIndex();

        yield BooleanField::new('ancienAdherent', 'Ancien(ne) adhérent(e)')
            ->hideOnIndex();

        yield TextField::new('passPortCode', "Code Pass'Sport")
            ->hideOnIndex();

        yield BooleanField::new('autorisationParentale', 'Autorisation parentale')
            ->hideOnIndex();

        yield BooleanField::new('droitImage', "Droit à l'image")
            ->hideOnIndex();

        yield BooleanField::new('cgv', 'CGV acceptées')
            ->hideOnIndex();

        yield BooleanField::new('reglementInterieur', 'Règlement intérieur accepté')
            ->hideOnIndex();

        yield BooleanField::new('promoEstresSaintDenis', 'Promo Estrées-Saint-Denis')
            ->hideOnForm()
            ->hideOnIndex();
    }
}
