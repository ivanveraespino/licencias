<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LicenciasController extends AbstractController
{
    #[Route('/busqueda', name: 'app_licencias')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $forma = $request->query->get('forma', 0); // devuelve 'dni'
        $id = $request->query->get('id', '');
        $con = $em->getConnection();
        if ($forma == 1) {
            //si es con dni
            $conn = $em->getConnection();

            $sql = 'SELECT n.ruc, n.razonsocial, s.nombrenegocio, s.direccion, s.tipovia, s.celular, 
               l.tipo, l.resolucion, l.inicio, l.fin, l.numero 
        FROM representante r 
        INNER JOIN negocio n ON r.idnegocio = n.id 
        INNER JOIN sede s ON n.id = s.idnegocio 
        INNER JOIN licencia l ON s.id = l.idsede 
        WHERE r.dni = ?'; // Cambiado :dni por ?

            // Pasamos el valor en un array indexado (el primer ? es el índice 0)
            $resultSet = $conn->executeQuery($sql, ['46301964']);

            $resultados = $resultSet->fetchAllAssociative();
        } else if ($forma == 2) {
            //si es con ruc
            $conn = $em->getConnection();

            $sql = 'SELECT n.ruc, n.razonsocial, s.nombrenegocio, s.direccion, s.tipovia, s.celular, 
                           l.tipo, l.resolucion, l.inicio, l.fin, l.numero 
                    FROM negocio n 
                    INNER JOIN sede s ON n.id = s.idnegocio 
                    INNER JOIN licencia l ON s.id = l.idsede 
                    WHERE n.ruc = ?'; // Cambiado :ruc por ?

            $resultSet = $conn->executeQuery($sql, ['10463019644']);

            $resultados = $resultSet->fetchAllAssociative();
        } else {
            $resultados = null;
        }
        return $this->render('licencias/index.html.twig', [
            'datos' => $resultados,
        ]);
    }
}
