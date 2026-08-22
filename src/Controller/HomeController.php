<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\DossierAdhesion;
use App\Entity\User;
use App\Form\DossierAdhesionType;
use App\Dto\AdhesionDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;





final class HomeController extends AbstractController
{
    public function __construct() {}

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $dto = new AdhesionDto();
        $dto->user = new User();
        $dto->dossierAdhesion = new DossierAdhesion();

        // UID généré à l'affichage, utilisé comme identifiant du dossier lors du submit
        $uid = Uuid::v4();

        $form = $this->createForm(DossierAdhesionType::class, $dto, [
            'action' => $this->generateUrl('adhesion_submit', ['uid' => $uid]),
            'method' => 'POST',
        ]);

        return $this->render('home/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/adhesion/{uid}/file', name: 'adhesion_submit', methods: ['POST'])]
    public function submit(string $uid, Request $request, EntityManagerInterface $em): Response
    {
        $dto = new AdhesionDto();
        $dto->user = new User();
        $dto->dossierAdhesion = new DossierAdhesion();

        $form = $this->createForm(DossierAdhesionType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $dto->user;
            $dossier = $dto->dossierAdhesion;

            $user->setUid(Uuid::fromString($uid));
            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new \DateTimeImmutable());
            // pas de setPassword() — password reste null volontairement

            $user->setDossierAdhesion($dossier);

            $em->persist($user);
            $em->persist($dossier);
            $em->flush();

            return $this->redirectToRoute('adhesion_success');
        }

        // formulaire invalide : on réaffiche avec les erreurs
        return $this->render('adhesion/form.html.twig', [
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
