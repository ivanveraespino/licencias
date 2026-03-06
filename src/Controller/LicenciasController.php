<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LicenciasController extends AbstractController
{
    #[Route('/licencias', name: 'app_licencias')]
    public function index(): Response
    {
        return $this->render('licencias/index.html.twig', [
            'controller_name' => 'LicenciasController',
        ]);
    }
   
    
}
