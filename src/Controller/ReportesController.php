<?php

namespace App\Controller;

use App\Entity\Giro;
use App\Entity\Tipo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

final class ReportesController extends AbstractController
{
    #[Route('/reportes', name: 'app_reportes')]
    public function index(EntityManagerInterface $em): Response
    {
        $giros = $em->getRepository(Giro::class)->findAll();
        $tipos = $em->getRepository(Tipo::class)->findAll();
        return $this->render('reportes/index.html.twig', [
            'giros' => $giros,
            'tipos' => $tipos,
        ]);
    }

    #[Route('/consulta', name: 'reporte_consulta')]
    public function consultar(EntityManagerInterface $em, Request $request)
    {
        try {
            // 1. Capturar los strings de las fechas desde el Request
            $inicioStr = $request->query->get('fecha-ini');
            $finStr    = $request->query->get('fecha-fin');
        
            // 2. Convertir a strings limpios en formato ISO (YYYY-MM-DD) si existen
            // SQL Server acepta este formato universal sin importar el idioma de la BD
            $fechaIniFormateada = $inicioStr ? (new \DateTime($inicioStr))->format('Y-m-d\T00:00:00') : null;
            $fechaFinFormateada = $finStr ? (new \DateTime($finStr))->format('Y-m-d\T00:00:00') : null;

            
        
            $params = [
                'fecha_ini' => $fechaIniFormateada, // Ahora es un String 'YYYY-MM-DD' o null
                'fecha_fin' => $fechaFinFormateada, // Ahora es un String 'YYYY-MM-DD' o null
                'giro'      => $request->query->get('giro') ?: null,
                'tipo'      => $request->query->get('tipo') ?: null,
                'licencia'  => $request->query->get('licencia') ?: null,
            ];
            
    
            
            $conn = $em->getConnection();
            $sql = 'EXEC sp_ConsultarLicenciasNegocio 
                    @fecha_ini = :fecha_ini, 
                    @fecha_fin = :fecha_fin, 
                    @giro = :giro, 
                    @tipo = :tipo, 
                    @licencia = :licencia';
            
            $stmt = $conn->executeQuery($sql, $params);
            $resultados = $stmt->fetchAllAssociative();
        
            return $this->render('reportes/reporte.html.twig', [
                'datos' => $resultados
            ]);
        } catch (\Exception $e) {
            return new Response("Error: " . $e->getMessage(), 500);
        }
        
    }
    
    /**
     * @Route("/reportes/logo-excel", name="reporte_logo_excel")
     */
    #[Route('/logo-excel', name: 'logo-excel')]
    public function getLogoExcel(): BinaryFileResponse
    {
        // 1. Apunta a la ubicación física de tu imagen dentro del proyecto
        // Si está en public/img/logo-informe.png:
        $rutaImagen = $this->getParameter('kernel.project_dir') . '/public/img/logox100.png';

        if (!file_exists($rutaImagen)) {
            throw $this->createNotFoundException('El logotipo no existe.');
        }

        // 2. Retorna una respuesta binaria nativa de Symfony
        $response = new BinaryFileResponse($rutaImagen);
        
        // 3. Forzar el Content-Type correcto para que Excel lo interprete al instante
        $response->headers->set('Content-Type', 'image/png');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

        return $response;
    }
}
