<?php

namespace App\Controller\Admin;


use App\Entity\ClassesRegistration;
use App\Repository\ClassesRegistrationRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClassesRegistrationCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly ClassesRegistrationRepository $classesRegistrationRepository,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        #[Autowire('%certificates_directory%')]
        private readonly string $certificatesDirectory,
    ) {}

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
            ->setDefaultRowAction(Action::DETAIL)
            ->setPageTitle(Crud::PAGE_DETAIL, fn(ClassesRegistration $c) => sprintf('%s %s', $c->getPrenom(), $c->getNom()))
            ->setPageTitle(Crud::PAGE_EDIT, fn(ClassesRegistration $c) => sprintf('%s %s', $c->getPrenom(), $c->getNom()));
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
        $registrations = $this->classesRegistrationRepository->findAll();

        $response = new StreamedResponse(function () use ($registrations) {
            $handle = fopen('php://output', 'w+');

            // BOM UTF-8 pour qu'Excel reconnaisse l'encodage correctement
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID',
                'UID',
                'Prénom',
                'Nom',
                'Date de naissance',
                'Cours choisis',
                "Contact d'urgence",
                'Téléphone urgence',
                'Certificat médical requis',
                'Ancien adhérent',
                "Pass'Sport",
                'Autorisation parentale',
                "Droit à l'image",
                'CGV',
                'Règlement intérieur',
                'Promo Estrées-Saint-Denis',
                'Créé le',
            ], ';');

            foreach ($registrations as $registration) {
                fputcsv($handle, [
                    $registration->getId(),
                    (string) $registration->getUid(),
                    $registration->getPrenom(),
                    $registration->getNom(),
                    $registration->getDateOfBirth()?->format('Y-m-d'),
                    implode(', ', $registration->getProduitLabels()),
                    $registration->getContactUrgence(),
                    $registration->getTelephoneContactUrgence(),
                    $registration->isNeedMedicalCertificate() ? 'Oui' : 'Non',
                    $registration->isAncienAdherent() ? 'Oui' : 'Non',
                    $registration->getPassPortCode(),
                    $registration->isAutorisationParentale() ? 'Oui' : 'Non',
                    $registration->isDroitImage() ? 'Oui' : 'Non',
                    $registration->isCgv() ? 'Oui' : 'Non',
                    $registration->isReglementInterieur() ? 'Oui' : 'Non',
                    $registration->isPromoEstresSaintDenis() ? 'Oui' : 'Non',
                    $registration->getCreatedAt()?->format('Y-m-d H:i:s'),
                ], ';');
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="classes_registrations.csv"');

        return $response;
    }

    #[AdminRoute(path: '/{entityId}/download-certificate', name: 'download_certificate')]
    public function downloadCertificate(AdminContext $context): BinaryFileResponse
    {
        /** @var ClassesRegistration $registration */
        $registration = $context->getEntity()->getInstance();

        $filename = $registration->getMedicalCertificateFile();

        if (!$filename) {
            throw $this->createNotFoundException('Aucun certificat médical pour ce dossier.');
        }

        $filePath = $this->certificatesDirectory . '/' . $filename;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier du certificat est introuvable.');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

        return $response;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('uid')
            ->formatValue(function ($value, ClassesRegistration $entity) {
                if (!$value) {
                    return null;
                }

                $fullUid = (string) $value;
                $shortUid = substr($fullUid, 0, 8) . '…';

                $url = $this->adminUrlGenerator
                    ->setController(self::class)
                    ->setAction(Action::DETAIL)
                    ->setEntityId($entity->getId())
                    ->generateUrl();

                return sprintf('<a href="%s" title="%s">%s</a>', $url, htmlspecialchars($fullUid), $shortUid);
            })
            ->renderAsHtml()
            ->hideOnForm();

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

        yield BooleanField::new('needMedicalCertificate', 'Certificat médical requis');


        yield TextField::new('medicalCertificateFile', 'Certificat médical')
            ->hideOnForm()
            ->hideOnIndex()
            ->renderAsHtml()
            ->formatValue(function ($value, ClassesRegistration $entity) {
                if (!$entity->getMedicalCertificateFile()) {
                    return 'Aucun certificat';
                }

                $url = $this->adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('downloadCertificate')
                    ->setEntityId($entity->getId())
                    ->generateUrl();

                return sprintf('<a href="%s">Télécharger le certificat</a>', $url);
            });

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
