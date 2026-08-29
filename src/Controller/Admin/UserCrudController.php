<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class UserCrudController extends AbstractCrudController
{

    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle(Crud::PAGE_DETAIL, fn(User $user) => sprintf('%s %s', $user->getPrenom(), $user->getNom()))
            ->setPageTitle(Crud::PAGE_EDIT, fn(User $user) => sprintf('%s %s', $user->getPrenom(), $user->getNom()));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('uid')
            ->formatValue(fn($value) => $value ? (string) $value : null)
            ->hideOnForm()
            ->hideOnIndex();

        yield DateTimeField::new('createdAt')
            ->hideOnForm();

        yield EmailField::new('email');

        yield TextField::new('prenom');

        yield TextField::new('nom');

        yield TelephoneField::new('telephone');

        yield DateField::new('dateOfBirth');

        yield TextField::new('address', 'Adresse');

        yield ArrayField::new('roles')
            ->hideOnIndex();

        yield TextField::new('password')
            ->onlyOnForms()
            ->hideOnForm();

        yield TextField::new('promoMultipleCours', 'Promo multi-cours')
            ->hideOnForm();

        yield TextField::new('priceToPay', 'Prix à payer')
            ->hideOnForm();

        yield BooleanField::new('isPaid', 'Payé');

        // yield ArrayField::new('classesRegistrationID', 'ID des inscriptions')
        //     ->hideOnForm();
    }
}
