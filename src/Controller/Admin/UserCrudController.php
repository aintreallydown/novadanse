<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly UserRepository $userRepository,
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

    public function configureActions(Actions $actions): Actions
    {
        $exportCsv = Action::new('exportCsv', 'Exporter en CSV', 'fas fa-file-csv')
            ->createAsGlobalAction()
            ->linkToCrudAction('exportCsv');

        return $actions
            ->add(Crud::PAGE_INDEX, $exportCsv);
    }

    #[AdminRoute(path: '/export-csv', name: 'export_csv')]
    public function exportCsv(): StreamedResponse
    {
        $users = $this->userRepository->findAll();

        $response = new StreamedResponse(function () use ($users) {
            $handle = fopen('php://output', 'w+');

            // BOM UTF-8 pour qu'Excel reconnaisse l'encodage correctement
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID',
                'UID',
                'Email',
                'Prénom',
                'Nom',
                'Téléphone',
                'Date de naissance',
                'Adresse',
                'Payé',
                'Prix à payer',
                'Créé le',
            ], ';');

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->getId(),
                    (string) $user->getUid(),
                    $user->getEmail(),
                    $user->getPrenom(),
                    $user->getNom(),
                    $user->getTelephone(),
                    $user->getDateOfBirth()?->format('Y-m-d'),
                    $user->getAddress(),
                    $user->isPaid() ? 'Oui' : 'Non',
                    $user->getPriceToPay(),
                    $user->getCreatedAt()?->format('Y-m-d H:i:s'),
                ], ';');
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="users.csv"');

        return $response;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('uid')
            ->formatValue(fn($value) => $value ? (string) $value : null)
            ->hideOnForm()
            ->hideOnIndex();


        yield EmailField::new('email');

        yield TextField::new('prenom');

        yield TextField::new('nom');

        yield TelephoneField::new('telephone');

        yield DateField::new('dateOfBirth');

        yield TextField::new('address', 'Adresse');

        yield ArrayField::new('roles')
            ->hideOnIndex()
            ->hideOnForm();

        yield TextField::new('password')
            ->onlyOnForms()
            ->hideOnForm();

        yield TextField::new('promoMultipleCours', 'Promo multi-cours')
            ->hideOnForm();

        yield TextField::new('priceToPay', 'Prix à payer')
            ->hideOnForm();

        yield BooleanField::new('isPaid', 'Payé');

        yield DateTimeField::new('createdAt')
            ->hideOnForm()
            ->hideOnIndex();
    }
}
