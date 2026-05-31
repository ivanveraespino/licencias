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
        // Captura 'documento' (POST) o 'id' (GET)
        $documento = $request->request->get('documento') ?? $request->query->get('id', '');
        $documento = trim($documento); // Limpiar espacios
        $longitud = strlen($documento);

        $conn = $em->getConnection();
        $resultados = null;
        $sql = null;
        $params = [$documento];

        // LÓGICA DE SELECCIÓN ACTUALIZADA
        if ($longitud === 11 && is_numeric($documento)) {
            // RUC (11 dígitos numéricos)
            $sql = 'SELECT n.ruc, n.razonsocial, s.nombrenegocio, s.direccion, s.tipovia, s.celular, l.tipo, l.resolucion, l.inicio, l.fin, l.numero 
                FROM negocio n 
                INNER JOIN sede s ON n.id = s.idnegocio 
                INNER JOIN licencia l ON s.id = l.idsede 
                WHERE n.ruc = ?';
        } elseif ($longitud > 0 && $longitud <= 6 && is_numeric($documento)) {
            // CÓDIGO LICENCIA (Numérico corto)
            $sql = 'SELECT n.ruc, n.razonsocial, s.nombrenegocio, s.direccion, s.tipovia, s.celular, l.tipo, l.resolucion, l.inicio, l.fin, l.numero 
                FROM licencia l 
                INNER JOIN sede s ON l.idsede = s.id 
                INNER JOIN negocio n ON s.idnegocio = n.id 
                WHERE l.numero = ?';
        } elseif ($longitud > 0) {
            // RAZÓN SOCIAL O NEGOCIO (Texto o cualquier otra longitud)
            // Usamos LIKE para que la búsqueda sea flexible
            $sql = 'SELECT n.ruc, n.razonsocial, s.nombrenegocio, s.direccion, s.tipovia, s.celular, l.tipo, l.resolucion, l.inicio, l.fin, l.numero 
                FROM negocio n 
                INNER JOIN sede s ON n.id = s.idnegocio 
                INNER JOIN licencia l ON s.id = l.idsede 
                WHERE LOWER(n.razonsocial) LIKE ? OR LOWER(s.nombrenegocio) LIKE ?';

            $params = ['%' . $documento . '%', '%' . $documento . '%'];
        }

        if ($sql) {
            $resultSet = $conn->executeQuery($sql, $params);
            $resultados = $resultSet->fetchAllAssociative();
        }

        // Respuesta JSON para tu formulario externo
        if ($request->isXmlHttpRequest() || $request->headers->get('Accept') === 'application/json' || $request->request->has('documento')) {
            if (empty($resultados)) {
                return $this->json(['mensaje' => 'No se encontraron resultados para: ' . $documento]);
            }
            return $this->json(['mensaje' => 'Éxito', 'datos' => $resultados]);
        }

        return $this->render('licencias/index.html.twig', ['datos' => $resultados]);
    }
}
