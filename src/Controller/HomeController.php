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





final class HomeController extends AbstractController
{
    public function __construct() {}

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

            foreach ($dto->classesRegistrations as $index => $classesRegistration) {
                $userRegistration->addClassesRegistration($classesRegistration);

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

                $em->persist($classesRegistration);
            }

            $em->persist($userRegistration);
            $em->flush();

            $this->addFlash('success', 'Votre inscription a été enregistrée avec succès !');

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
