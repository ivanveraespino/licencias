<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'representante')]
class Representante
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 8)]
    private string $dni;

    #[ORM\Column(type: 'string', length: 100)]
    private string $nombres;

    #[ORM\Column(type: 'string', length: 100)]
    private string $paterno;

    #[ORM\Column(type: 'string', length: 100)]
    private string $materno;

    #[ORM\Column(type: 'integer')]
    private int $estado;

    #[ORM\Column(type: 'string', length: 100)]
    private string $tipovia;

    #[ORM\Column(type: 'string', length: 250)]
    private string $direccion;


    #[ORM\Column(type: 'string', length: 12)]
    private ?string $celular;

    #[ORM\ManyToOne(targetEntity: Distrito::class)]
    #[ORM\JoinColumn(name: 'iddis', referencedColumnName: 'id')]
    private Distrito $distrito;

    #[ORM\ManyToOne(targetEntity: Provincia::class)]
    #[ORM\JoinColumn(name: 'idprov', referencedColumnName: 'id')]
    private Provincia $provincia;

    #[ORM\ManyToOne(targetEntity: Departamento::class)]
    #[ORM\JoinColumn(name: 'iddep', referencedColumnName: 'id')]
    private Departamento $departamento;

    #[ORM\ManyToOne(targetEntity: Negocio::class)]
    #[ORM\JoinColumn(name: 'idnegocio', referencedColumnName: 'id')]
    private Negocio $negocio;

    // --- Getters y Setters ---
    public function getId(): ?int { return $this->id; }
    public function getDni(): string { return $this->dni; }
    public function setDni(string $dni): self { $this->dni = $dni; return $this; }
    public function getNombres(): string { return $this->nombres; }
    public function setNombres(string $nombres): self { $this->nombres = $nombres; return $this; }
    public function getPaterno(): string { return $this->paterno; }
    public function setPaterno(string $paterno): self { $this->paterno = $paterno; return $this; }
    public function getMaterno(): string { return $this->materno; }
    public function setMaterno(string $materno): self { $this->materno = $materno; return $this; }
    public function getEstado(): int { return $this->estado; }
    public function setEstado(int $estado): self { $this->estado = $estado; return $this; }
    public function getTipovia(): string { return $this->tipovia; }
    public function setTipovia(string $tipovia): self { $this->tipovia = $tipovia; return $this; }
    public function getDireccion(): string { return $this->direccion; }
    public function setDireccion(string $direccion): self { $this->direccion = $direccion; return $this; }

    public function getCelular(): ?string { return $this->celular; }
    public function setCelular(?string $celular): self { $this->celular = $celular; return $this; }

    public function getDistrito(): Distrito { return $this->distrito; }
    public function setDistrito(Distrito $distrito): self { $this->distrito = $distrito; return $this; }
    public function getProvincia(): Provincia { return $this->provincia; }
    public function setProvincia(Provincia $provincia): self { $this->provincia = $provincia; return $this; }
    public function getDepartamento(): Departamento { return $this->departamento; }
    public function setDepartamento(Departamento $departamento): self { $this->departamento = $departamento; return $this; }
    public function getNegocio(): Negocio { return $this->negocio; }
    public function setNegocio(Negocio $negocio): self { $this->negocio = $negocio; return $this; }
}
