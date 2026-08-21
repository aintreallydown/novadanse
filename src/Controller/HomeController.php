<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



final class HomeController extends AbstractController
{
    public function __construct() {}

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
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
