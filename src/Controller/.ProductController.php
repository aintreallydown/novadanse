<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends AbstractController
{
    public function __construct() {}

    #[Route('/adhesion/{uid}/file', name: 'products')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig');
    }
}
