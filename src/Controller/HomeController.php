<?php

namespace App\Controller;

use App\Entity\Departamento;
use App\Entity\Distrito;
use App\Entity\Giro;
use App\Entity\Licencia;
use App\Entity\Negocio;
use App\Entity\Provincia;
use App\Entity\Representante;
use App\Entity\Sede;
use App\Entity\Tipo;
use App\Entity\Tipovia;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use PharIo\Manifest\License;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Picqer\Barcode\BarcodeGeneratorPNG;

final class HomeController extends AbstractController
{
      #[Route('/', name: 'root_index')]
    public function inicio(): Response
    {
        // Redirige al nombre de la ruta de tu página de inicio
        return $this->redirectToRoute('app_home');
    }

    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {

        $sql = "SELECT s.id, n.ruc, n.razonsocial, g.actividad,
               s.nombrenegocio, s.tipovia, s.direccion,
               s.celular, s.estado
        FROM negocio n
        INNER JOIN giro g ON n.idgiro = g.id
        INNER JOIN sede s ON s.idnegocio = n.id";

        $conn = $em->getConnection();
        $stmt = $conn->prepare($sql);
        $emitidos = $stmt->executeQuery()->fetchAllAssociative();
        $sedes = $em->getRepository(Sede::class)->findAll();
        return $this->render('home/index.html.twig', [
            'emitidos' => $emitidos,
            'sedes' => $sedes
        ]);
    }

    #[Route('/sede/{id}', name: 'ver-sede')]
    public function ver(int $id, EntityManagerInterface $em)
    {
        $sede = $em->getRepository(Sede::class)->find($id);
        $licencias = $em->getRepository(Licencia::class)->findBy(['sede' => $id]);
        $representante = $em->getRepository(Representante::class)->findOneBy(['negocio' => $sede->getNegocio()]);
        return $this->render('home/detalleNegocio.html.twig', [
            'sede' => $sede,
            'licencias' => $licencias,
            'representante' => $representante
        ]);
    }


    #[Route('/licencia/{id}/subir-pdfs', name: 'subir_pdfs', methods: ['POST'])]
    public function subirPdfs(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $archivo = $request->files->get('archivo');
        $licencia = $em->getRepository(Licencia::class)->find($id);
        if ($archivo) {
            // Ruta fija hacia la carpeta pdfs dentro de public
            $directorio = $this->getParameter('kernel.project_dir') . '/public/pdfs';

            $nombreArchivo = 'licencia_' . $licencia->getId() . '.pdf';
            $archivo->move($directorio, $nombreArchivo);

            $licencia->setUrllicencia($nombreArchivo);
            $licencia->setEstado(1);
            $em->flush();
        }

        return $this->redirectToRoute('ver-sede', ['id' => $licencia->getSede()->getId()]);
    }


    #[Route('/nuevo-negocio', name: 'negocio_nuevo')]
    public function negocioNuevo(EntityManagerInterface $em): Response
    {
        $giros = $em->getRepository(Giro::class)->findAll();
        $tipos = $em->getRepository(Tipo::class)->findAll();
        $deps = $em->getRepository(Departamento::class)->findAll();
        $provs = $em->getRepository(Provincia::class)->findAll();
        $diss = $em->getRepository(Distrito::class)->findAll();
        $vias = $em->getRepository(Tipovia::class)->findAll();
        return $this->render('home/nuevoNegocio.html.twig', [
            'giros' => $giros,
            'tipos' => $tipos,
            'deps' => $deps,
            'provs' => $provs,
            'diss' => $diss,
            'vias' => $vias,
        ]);
    }

    #[Route('/get-provincias/{depId}', name: 'get_provincias', methods: ['GET'])]
    public function getProvincias(int $depId, EntityManagerInterface $em): JsonResponse
    {
        // Buscamos las provincias que pertenecen al departamento (suponiendo relación 'departamento')
        $departamento = $em->getRepository(Departamento::class)->find($depId);

        $provincias = $em->getRepository(Provincia::class)->findBy([
            'departamento' => $departamento
        ]);



        $data = [];
        foreach ($provincias as $provincia) {
            $data[] = [
                'id' => $provincia->getId(),
                'nombre' => $provincia->getNombre(),
            ];
        }

        // Retornamos la lista en formato JSON
        return $this->json($data);
    }

    #[Route('/get-distritos/{provId}', name: 'get_distritos', methods: ['GET'])]
    public function getDistritos(string $provId, EntityManagerInterface $em): JsonResponse
    {
        // Buscamos la provincia correspondiente
        $provincia = $em->getRepository(Provincia::class)->find($provId);

        // Obtenemos los distritos que pertenecen a esa provincia (relación 'provincia')
        $distritos = $em->getRepository(Distrito::class)->findBy([
            'provincia' => $provincia
        ]);

        $data = [];
        foreach ($distritos as $distrito) {
            $data[] = [
                'id' => $distrito->getId(),
                'nombre' => $distrito->getNombre(),
            ];
        }

        // Retornamos la lista en formato JSON
        return $this->json($data);
    }

    #[Route('/subir-pdf', name: 'subir_pdf', methods: ['POST'])]
    public function subirPdf(Request $request): JsonResponse
    {
        $file = $request->files->get('file'); // el input debe llamarse "file"

        if (!$file || $file->getClientOriginalExtension() !== 'pdf') {
            return $this->json(['error' => 'Debe subir un archivo PDF válido'], 400);
        }

        // Carpeta donde se guardará (ejemplo: public/uploads)
        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/pdfs';

        // Nombre único para evitar colisiones
        $filename = uniqid() . '.' . $file->guessExtension();

        // Mover archivo
        $file->move($uploadsDir, $filename);
        chmod($uploadsDir . '/' . $filename, 0644); // Da permiso de lectura pública

        // URL pública
        $url = '/pdfs/' . $filename;

        return $this->json(['url' => $url]);
    }


    #[Route('/guardar-negocio', name: 'guardar_negocio', methods: ['POST'])]
    public function guardarFormulario(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Capturar datos del formulario
        $ruc = $request->request->get('ruc');
        $razonSocial = $request->request->get('razon-social');
        $ficha = $request->request->get('url-ficha');
        $idgiro = $request->request->get('giro');
        $giro = $em->getRepository(Giro::class)->find($idgiro);
        $negocio = new Negocio();
        $negocio->setRuc($ruc);
        $negocio->setRazonsocial($razonSocial);
        $negocio->setUrlficha($ficha);
        $negocio->setGiro($giro);
        $em->persist($negocio);
        $em->flush();

        $dniRep = $request->request->get('dni-rep');
        $nombreRep = $request->request->get('nombre-rep');
        $paternoRep = $request->request->get('paterno-rep');
        $maternoRep = $request->request->get('materno-rep');
        $celRep = $request->request->get('cel-rep');
        $tipoviaRep = $request->request->get('tipo-via-rep');
        $direccionRep = $request->request->get('dir-rep');
        $iddepRep = $request->request->get('dep-rep');
        $idprovRep = $request->request->get('prov-rep');
        $iddisRep = $request->request->get('dis-rep');
        $depRep = $em->getRepository(Departamento::class)->find($iddepRep);
        $provRep = $em->getRepository(Provincia::class)->find($idprovRep);
        $disRep = $em->getRepository(Distrito::class)->find($iddisRep);

        $representante = new Representante();
        $representante->setDni($dniRep);
        $representante->setNombres($nombreRep);
        $representante->setPaterno($paternoRep);
        $representante->setMaterno($maternoRep);
        $representante->setEstado(1);
        $representante->setDireccion($direccionRep);
        $representante->setTipovia($tipoviaRep);
        $representante->setDepartamento($depRep);
        $representante->setProvincia($provRep);
        $representante->setDistrito($disRep);
        $representante->setNegocio($negocio);
        $em->persist($representante);
        $em->flush();



        $idtiposede = $request->request->get('tipo-sede');
        $tipoSede = $em->getRepository(Tipo::class)->find($idtiposede);
        $nombreSede = $request->request->get('nombre-sede');
        $areaSede = $request->request->get('area-sede');
        $telSede = $request->request->get('cel-sede');
        $tipoviaSede = $request->request->get('tipo-via-sede');
        $direccionSede = $request->request->get('direccion-sede');
        $iddepSede = $request->request->get('dep-sede');
        $depsede = $em->getRepository(Departamento::class)->find($iddepSede);
        $idprovSede = $request->request->get('prov-sede');
        $provSede = $em->getRepository(Provincia::class)->find($idprovSede);
        $iddisSede = $request->request->get('dis-sede');
        $disSede = $em->getRepository(Distrito::class)->find($iddisSede);

        $certCivilSede = $request->request->get('url-def-civil-sede');
        $certUsoSede = $request->request->get('url-uso-suelo-sede');
        $estadoSede = $request->request->get('estado-sede') ? 1 : 0;

        $sede = new Sede();
        $sede->setNombrenegocio($nombreSede);
        $sede->setDireccion($direccionSede);
        $sede->setUrldefensacivil($certCivilSede);
        $sede->setUrlcompatibilidadsuelos($certUsoSede);
        $sede->setEstado($estadoSede);
        $sede->setTipo($tipoSede);
        $sede->setCelular($telSede);
        $sede->setDistrito($disSede);
        $sede->setDepartamento($depsede);
        $sede->setProvincia($provSede);
        $sede->setTipovia($tipoviaSede);
        $sede->setNegocio($negocio);
        $sede->setArea($areaSede);
        $em->persist($sede);
        $em->flush();

        $tipoLic = $request->request->get('tipo-licencia');
        $diasLic = $request->request->get('dias-licencia');
        $inicioLic = $request->request->get('ini-licencia');
        $finLic = $request->request->get('fin-licencia');
        $resolucionLic = $request->request->get('resolucion-licencia');
        $expLic = $request->request->get('expediente-licencia');
        $numeroLic = $request->request->get('numero-licencia');
        $urlresol = $request->request->get('url-resolucion-sede');
        $horarioSede = $request->request->get('horario-sede');

        $licencia = new Licencia();
        $licencia->setTipo($tipoLic);
        $licencia->setDias((int)$diasLic);
        $licencia->setHorario($horarioSede);
        $licencia->setResolucion($resolucionLic);
        $licencia->setUrlresolucion($urlresol);
        if (!empty($inicioLic)) {
            // Trim para evitar espacios accidentales que rompan el formato
            $fechaInicio = \DateTime::createFromFormat('Y-m-d', trim($inicioLic));

            if ($fechaInicio) {
                // Opcional: Resetear la hora a 00:00:00 para evitar problemas de precisión en SQL Server
                $fechaInicio->setTime(0, 0, 0);
                $licencia->setInicio($fechaInicio);
            } else {
                // La fecha no es válida (ej: "2025-02-30" o "abc")
                throw new \Exception("El formato de fecha de inicio es inválido: " . $inicioLic);
            }
        }

        if (!empty($finLic)) {
            // Trim para evitar espacios accidentales que rompan el formato
            $fechaFin = \DateTime::createFromFormat('Y-m-d', trim($finLic));

            if ($fechaFin) {
                // Opcional: Resetear la hora a 00:00:00 para evitar problemas de precisión en SQL Server
                $fechaFin->setTime(0, 0, 0);
                $licencia->setFin($fechaFin);
            } else {
                // La fecha no es válida (ej: "2025-02-30" o "abc")
                throw new \Exception("El formato de fecha de inicio es inválido: " . $inicioLic);
            }
        }
        $licencia->setEstado(0);
        $licencia->setSede($sede);
        $licencia->setNumero($numeroLic);
        $em->persist($licencia);
        $em->flush();
        $dominio = $request->getSchemeAndHttpHost();
        $dominio = $dominio . '/licencia/' . $numeroLic;
        $qrCode = new QrCode($dominio);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $qrBase64 = base64_encode($result->getString());

        // --- Generar PDF ---

        $meses = [
            'January' => 'enero',
            'February' => 'febrero',
            'March' => 'marzo',
            'April' => 'abril',
            'May' => 'mayo',
            'June' => 'junio',
            'July' => 'julio',
            'August' => 'agosto',
            'September' => 'setiembre',
            'October' => 'octubre',
            'November' => 'noviembre',
            'December' => 'diciembre'
            // ...
        ];
        $mesActual = $meses[date('F')];

        $html = $this->renderView('licencias/licencia.html.twig', [

            'ruc' => $ruc,
            'razonSocial' => $razonSocial,
            'nombresede' => $nombreSede,
            'direccionsede' => $direccionSede,
            'area' => $areaSede,
            'giro' => $giro->getActividad(),
            'resolucion' => $resolucionLic,
            'horario' => $horarioSede,
            'expediente' => $expLic,
            'mes' => $mesActual,
            'qr' => $qrBase64,
            // ... demás datos
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Guardar PDF en carpeta pública
        $output = $dompdf->output();
        $filename = uniqid('formulario_') . '.pdf';
        $licencia->setUrllicencia($filename);
        $em->persist($licencia);
        $em->flush();
        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/pdfs/' . $filename;
        file_put_contents($pdfPath, $output);

        $urlPdf = '/pdfs/' . $filename;
        // Forzar descarga 
        $response = new BinaryFileResponse($pdfPath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);



        return $this->json([
            'success' => true,
            'message' => 'Formulario guardado y PDF generado',
            'pdfUrl' => $urlPdf
        ]);
    }

    #[Route('/guardar-negocio-2', name: 'guardar_negocio_2', methods: ['POST'])]
    public function guardarFormulario2(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Capturar datos del formulario
        $ruc = $request->request->get('ruc');
        $razonSocial = $request->request->get('razon-social');
        $ficha = $request->request->get('url-ficha');
        $idgiro = $request->request->get('giro');
        $giro = $em->getRepository(Giro::class)->find($idgiro);
        $negocio = new Negocio();
        $negocio->setRuc($ruc);
        $negocio->setRazonsocial($razonSocial);
        $negocio->setUrlficha($ficha);
        $negocio->setGiro($giro);
        $em->persist($negocio);
        $em->flush();

        $dniRep = $request->request->get('dni-rep');
        $nombreRep = $request->request->get('nombre-rep');
        $paternoRep = $request->request->get('paterno-rep');
        $maternoRep = $request->request->get('materno-rep');
        $celRep = $request->request->get('cel-rep');
        $tipoviaRep = $request->request->get('tipo-via-rep');
        $direccionRep = $request->request->get('dir-rep');
        $iddepRep = $request->request->get('dep-rep');
        $idprovRep = $request->request->get('prov-rep');
        $iddisRep = $request->request->get('dis-rep');
        $depRep = $em->getRepository(Departamento::class)->find($iddepRep);
        $provRep = $em->getRepository(Provincia::class)->find($idprovRep);
        $disRep = $em->getRepository(Distrito::class)->find($iddisRep);

        $representante = new Representante();
        $representante->setDni($dniRep);
        $representante->setNombres($nombreRep);
        $representante->setPaterno($paternoRep);
        $representante->setMaterno($maternoRep);
        $representante->setEstado(1);
        $representante->setDireccion($direccionRep);
        $representante->setTipovia($tipoviaRep);
        $representante->setDepartamento($depRep);
        $representante->setProvincia($provRep);
        $representante->setDistrito($disRep);
        $representante->setNegocio($negocio);
        $em->persist($representante);
        $em->flush();



        $idtiposede = $request->request->get('tipo-sede');
        $tipoSede = $em->getRepository(Tipo::class)->find($idtiposede);
        $nombreSede = $request->request->get('nombre-sede');
        $areaSede = $request->request->get('area-sede');
        $telSede = $request->request->get('cel-sede');
        $tipoviaSede = $request->request->get('tipo-via-sede');
        $direccionSede = $request->request->get('direccion-sede');
        $iddepSede = $request->request->get('dep-sede');
        $depsede = $em->getRepository(Departamento::class)->find($iddepSede);
        $idprovSede = $request->request->get('prov-sede');
        $provSede = $em->getRepository(Provincia::class)->find($idprovSede);
        $iddisSede = $request->request->get('dis-sede');
        $disSede = $em->getRepository(Distrito::class)->find($iddisSede);
        $certCivilSede = $request->request->get('url-def-civil-sede');
        $certUsoSede = $request->request->get('url-uso-suelo-sede');
        $estadoSede = $request->request->get('estado-sede') ? 1 : 0;

        $sede = new Sede();
        $sede->setNombrenegocio($nombreSede);
        $sede->setDireccion($direccionSede);
        $sede->setUrldefensacivil($certCivilSede);
        $sede->setUrlcompatibilidadsuelos($certUsoSede);
        $sede->setEstado($estadoSede);
        $sede->setTipo($tipoSede);
        $sede->setCelular($telSede);
        $sede->setDistrito($disSede);
        $sede->setDepartamento($depsede);
        $sede->setProvincia($provSede);
        $sede->setTipovia($tipoviaSede);
        $sede->setNegocio($negocio);
        $sede->setArea($areaSede);
        $em->persist($sede);
        $em->flush();

        $tipoLic = $request->request->get('tipo-licencia');
        $diasLic = $request->request->get('dias-licencia');
        $inicioLic = $request->request->get('ini-licencia');
        $finLic = $request->request->get('fin-licencia');
        $resolucionLic = $request->request->get('resolucion-licencia');
        $expLic = $request->request->get('expediente-licencia');
        $numeroLic = $request->request->get('numero-licencia');
        $urlresol = $request->request->get('url-resolucion-sede');
        $horarioSede = $request->request->get('horario-sede');



        $licencia = new Licencia();
        $licencia->setTipo($tipoLic);
        $licencia->setDias($diasLic);
        $licencia->setResolucion($resolucionLic);
        $licencia->setUrlresolucion($urlresol);
        $licencia->setHorario($horarioSede);
        if (!empty($inicioLic)) {
            // Trim para evitar espacios accidentales que rompan el formato
            $fechaInicio = \DateTime::createFromFormat('Y-m-d', trim($inicioLic));

            if ($fechaInicio) {
                // Opcional: Resetear la hora a 00:00:00 para evitar problemas de precisión en SQL Server
                $fechaInicio->setTime(0, 0, 0);
                $licencia->setInicio($fechaInicio);
            } else {
                // La fecha no es válida (ej: "2025-02-30" o "abc")
                throw new \Exception("El formato de fecha de inicio es inválido: " . $inicioLic);
            }
        }

        if (!empty($finLic)) {
            // Trim para evitar espacios accidentales que rompan el formato
            $fechaFin = \DateTime::createFromFormat('Y-m-d', trim($finLic));

            if ($fechaFin) {
                // Opcional: Resetear la hora a 00:00:00 para evitar problemas de precisión en SQL Server
                $fechaFin->setTime(0, 0, 0);
                $licencia->setFin($fechaFin);
            } else {
                // La fecha no es válida (ej: "2025-02-30" o "abc")
                throw new \Exception("El formato de fecha de inicio es inválido: " . $inicioLic);
            }
        }
        $licencia->setEstado(0);
        $licencia->setSede($sede);
        $licencia->setNumero($numeroLic);

        $dominio = $request->getSchemeAndHttpHost();
        $dominio = $dominio . '/licencia/' . $numeroLic;
        $qrCode = new QrCode($dominio);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $qrBase64 = base64_encode($result->getString());
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($numeroLic, $generator::TYPE_CODE_128));
        // --- Generar PDF ---
        $meses = [
            'January' => 'enero',
            'February' => 'febrero',
            'March' => 'marzo',
            'April' => 'abril',
            'May' => 'mayo',
            'June' => 'junio',
            'July' => 'julio',
            'August' => 'agosto',
            'September' => 'setiembre',
            'October' => 'octubre',
            'November' => 'noviembre',
            'December' => 'diciembre'
            // ...
        ];
        $mesActual = $meses[date('F')];

        $html = $this->renderView('licencias/licenciaDiseno.html.twig', [

            'ruc' => $ruc,
            'razonSocial' => $razonSocial,
            'nombresede' => $nombreSede,
            'direccionsede' => $direccionSede,
            'area' => $areaSede,
            'giro' => $giro->getActividad(),
            'resolucion' => $resolucionLic,
            'horario' => $horarioSede,
            'expediente' => $expLic,
            'mes' => $mesActual,
            'qr' => $qrBase64,
            'dominio' => $request->getSchemeAndHttpHost(),

            'barcode' => $barcode
            // ... demás datos
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Guardar PDF en carpeta pública
        $output = $dompdf->output();
        $filename = uniqid('formulario_') . '.pdf';
        $licencia->setUrllicencia($filename);
        $em->persist($licencia);
        $em->flush();
        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/pdfs/' . $filename;
        file_put_contents($pdfPath, $output);

        $urlPdf = '/pdfs/' . $filename;
        // Forzar descarga 
        $response = new BinaryFileResponse($pdfPath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);



        return $this->json([
            'success' => true,
            'message' => 'Formulario guardado y PDF generado',
            'pdfUrl' => $urlPdf
        ]);
    }

    #[Route('/guardar-giro', name: 'guardar_giro', methods: ['POST'])]
    public function guardarGiro(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $actividad = $data['actividad'] ?? null;

        if (!$actividad) {
            return $this->json(['success' => false, 'error' => 'Actividad requerida'], 400);
        }

        $giro = new Giro();
        $giro->setActividad($actividad);

        $em->persist($giro);
        $em->flush();

        return $this->json([
            'success' => true,
            'id' => $giro->getId(),
            'actividad' => $giro->getActividad()
        ]);
    }

    #[Route('/guardar-tipo', name: 'guardar_tipo', methods: ['POST'])]
    public function guardarTipo(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $denominacion = $data['denominacion'] ?? null;

        if (!$denominacion) {
            return $this->json(['success' => false, 'error' => 'Denominación requerida'], 400);
        }

        $tipo = new Tipo();
        $tipo->setDenominacion($denominacion);

        $em->persist($tipo);
        $em->flush();

        return $this->json([
            'success' => true,
            'id' => $tipo->getId(),
            'denominacion' => $tipo->getDenominacion()
        ]);
    }

    #[Route('/sede/{id}/agregar-licencia', name: 'agregar_licencia', methods: ['POST'])]
    public function agregarLicencia(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        // Buscar la sede
        $sede = $em->getRepository(Sede::class)->find($id);
        if (!$sede) {
            throw $this->createNotFoundException('Sede no encontrada');
        }

        // Crear nueva licencia
        $licencia = new Licencia();
        $licencia->setSede($sede);
        $licencia->setTipo((int) $request->request->get('tipo-licencia'));
        $licencia->setResolucion($request->request->get('resolucion-licencia'));
        $licencia->setExpediente($request->request->get('expediente-licencia'));
        $licencia->setNumero($request->request->get('numero-licencia'));
        $licencia->setDias((int) $request->request->get('dias-licencia'));
        $licencia->setInicio(new \DateTime($request->request->get('ini-licencia')));
        $licencia->setFin(new \DateTime($request->request->get('fin-licencia')));
        $licencia->setEstado(0); // por defecto "Sin Firmar"

        // Guardar en BD
        $em->persist($licencia);
        $em->flush();

        // Redirigir a la vista detalle de la sede
        return $this->redirectToRoute('ver-sede', ['id' => $sede->getId()]);
    }


}
