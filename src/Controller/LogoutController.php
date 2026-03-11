<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LogoutController extends AbstractController{
    #[Route('/logout', name:'app_logout', methods:['GET'])]
    public function logout():void{
        throw new \LogicException('Este método puede estar vacío; será interceptado por la llave de logout del firewall.');
    }
}