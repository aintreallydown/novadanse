<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\ClassesRegistration;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;
use App\Dto\RegistrationDto;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use App\Message\BrevoMailMessage;
use Symfony\Component\Messenger\MessageBusInterface;





final class HomeController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus
    ) {}

    #[Route('/', name: 'home')]
    public function index(Request $request, EntityManagerInterface $em, UserRepository $ur): Response
    {
        $dto = new RegistrationDto();
        $dto->user = new User();
        $dto->classesRegistrations = [new ClassesRegistration()];

        $form = $this->createForm(RegistrationType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = $dto->user->getEmail();
            $existingUser = $ur->findOneBy(['email' => $email]);

            if ($existingUser !== null) {
                $this->addFlash('error', 'Cet email est déjà utilisé, veuillez en choisir un autre.');

                return $this->render('home/index.html.twig', [
                    'form' => $form,
                ]);
            }

            $dto = $form->getData();

            $userRegistration = $dto->user;
            $userRegistration->setUid(Uuid::v4());
            $userRegistration->setRoles(['ROLE_USER']);

            $PromoAdress = $userRegistration->getAddress();
            $classesRegistrationID = $userRegistration->getClassesRegistrationID();

            $totalBrut = 0;
            $totalCoursCount = 0;
            $reductionEstrees = 0;

            foreach ($dto->classesRegistrations as $index => $classesRegistration) {

                $firstname = $classesRegistration->getPrenom();
                $lastname = $classesRegistration->getNom();

                $userRegistration->addClassesRegistration($classesRegistration);

                $lessThan16 = $classesRegistration->getDateOfBirth() && $classesRegistration->getDateOfBirth() > new \DateTimeImmutable('-16 years');

                $isEstreesSaintDenis = $PromoAdress && preg_match('/Estr[ée]es?[\s-]*Saint[\s-]*Denis/iu', $PromoAdress);

                $classesRegistration->setPromoEstresSaintDenis($isEstreesSaintDenis && $lessThan16);

                $numberOfCourses = count($classesRegistration->getProduit());

                $classesRegistered = $classesRegistration->getProduit();

                $totalCoursCount += count($classesRegistered);

                $passport = $classesRegistration->getPassPortCode() ?? null;

                foreach ($classesRegistered as $registered) {
                    $totalBrut += ClassesRegistration::getPrixCours($registered) ?? 0;
                }

                if ($classesRegistration->isPromoEstresSaintDenis()) {
                    $reductionEstrees += 20;
                }



                $classesRegistrationID['dossier'][] = $classesRegistration->setUid(Uuid::v4())->getUid();


                // Réponses QS-Sport spécifiques à cet adhérent (champs nommés qs1_0, qs2_0... ou similaire selon le JS)
                $qsAnswers = [];
                for ($i = 1; $i <= 9; $i++) {
                    $qsAnswers[] = $request->request->get("qs{$i}_{$index}");
                }
                $needsCertificate = in_array('oui', $qsAnswers, true);
                $classesRegistration->setNeedMedicalCertificate($needsCertificate);

                $certificateFile = $form->get('classesRegistrations')->get($index)->get('medicalCertificateFile')->getData();

                if ($certificateFile instanceof UploadedFile) {
                    $newFilename = 'certificatMedicale_de_' . $classesRegistration->getPrenom() . '_' . $classesRegistration->getNom() . '.' . $certificateFile->guessExtension();

                    $certificateFile->move(
                        $this->getParameter('certificates_directory'),
                        $newFilename
                    );

                    $classesRegistration->setMedicalCertificateFile($newFilename);
                }


                $classesRegistration->setCreatedAt(new \DateTimeImmutable());

                $em->persist($classesRegistration);
            }


            if ($numberOfCourses && $numberOfCourses == 2) {

                $userRegistration->setPromoMultipleCours('10%');
            } elseif ($numberOfCourses && $numberOfCourses > 2) {

                $userRegistration->setPromoMultipleCours('20%');
            }

            $tauxRemise = 0;

            if ($totalCoursCount === 2) {
                $tauxRemise = 0.10;
                $userRegistration->setPromoMultipleCours('10%');
            } elseif ($totalCoursCount > 2) {
                $tauxRemise = 0.20;
                $userRegistration->setPromoMultipleCours('20%');
            }

            $remise = $totalBrut * $tauxRemise;
            $totalFinal = max($totalBrut - $remise - $reductionEstrees - ($passport ? 15 : 0), 0);

            $userRegistration->setPriceToPay((string) $totalFinal);
            $userRegistration->setClassesRegistrationID($classesRegistrationID);
            $userRegistration->setIsPaid(false);


            $em->persist($userRegistration);
            $em->flush();

            $this->addFlash('success', 'Votre inscription a été enregistrée avec succès !');

            $token = $this->getParameter('brevo_api_key');
            $sender = 'contact@novadanse.fr';

            $this->bus->dispatch(new BrevoMailMessage(
                $token,
                1,
                $sender,
                [[
                    'nom' => $lastname,
                    'email' => $userRegistration->getEmail(),
                ]],
                [
                    'prenom' => $lastname,
                    'nom' => $firstname,
                    'classes' => array_map(fn($cr) => $cr->getProduit(), $dto->classesRegistrations),
                    'PromoCity' => $reductionEstrees ?? '0',
                    'PromoMulti' => $userRegistration->getPromoMultipleCours() ?? '0',
                    'totalFinal' => $totalFinal,
                    'passport' => $passport ?? 'Non fourni',
                ]
            ));



            return $this->redirectToRoute('home');
        }



        return $this->render('home/index.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/notre-vision', name: 'vision')]
    public function vision(): Response
    {
        return $this->render('home/vision.html.twig');
    }

    #[Route('/adhesion/reglement-interieur', name: 'reglement')]
    public function reglement(): Response
    {
        return $this->render('home/nova-internal-rules.html.twig');
    }

    #[Route('/adhesion/conditions-generales-de-vente', name: 'conditions_generales_vente')]
    public function conditionsGeneralesVente(): Response
    {
        return $this->render('home/cgv.html.twig');
    }
}
