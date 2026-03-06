<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sede')]
class Sede
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 250)]
    private string $nombrenegocio;

    #[ORM\Column(type: 'string', length: 100)]
    private string $tipovia;

    #[ORM\Column(type: 'string', length: 250)]
    private string $direccion;

    #[ORM\Column(type: 'string', length: 12)]
    private string $celular;

    #[ORM\Column(type: 'integer')]
    private int $tipodomicilio;

    #[ORM\Column(type: 'string', length: 250)]
    private string $urldefensacivil;

    #[ORM\Column(type: 'string', length: 250)]
    private string $urlcompatibilidadsuelos;
    
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $horario='';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 3)]
    private ?string $area=null;

    #[ORM\Column(type: 'integer')]
    private int $estado;

    #[ORM\ManyToOne(targetEntity: Tipo::class)]
    #[ORM\JoinColumn(name: 'idtipo', referencedColumnName: 'id')]
    private Tipo $tipo;

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
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNombrenegocio(): string
    {
        return $this->nombrenegocio;
    }
    public function setNombrenegocio(string $nombrenegocio): self
    {
        $this->nombrenegocio = $nombrenegocio;
        return $this;
    }
    public function getTipovia(): string
    {
        return $this->tipovia;
    }
    public function setTipovia(string $tipovia): self
    {
        $this->tipovia = $tipovia;
        return $this;
    }
    public function getDireccion(): string
    {
        return $this->direccion;
    }
    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;
        return $this;
    }
    public function getCelular(): string
    {
        return $this->celular;
    }
    public function setCelular(string $celular): self
    {
        $this->celular = $celular;
        return $this;
    }

    public function getTipodomicilio(): int
    {
        return $this->tipodomicilio;
    }
    public function setTipodomicilio(int $tipodomicilio): self
    {
        $this->tipodomicilio = $tipodomicilio;
        return $this;
    }
    public function getUrldefensacivil(): string
    {
        return $this->urldefensacivil;
    }
    public function setUrldefensacivil(string $urldefensacivil): self
    {
        $this->urldefensacivil = $urldefensacivil;
        return $this;
    }
    public function getUrlcompatibilidadsuelos(): string
    {
        return $this->urlcompatibilidadsuelos;
    }
    public function setUrlcompatibilidadsuelos(string $urlcompatibilidadsuelos): self
    {
        $this->urlcompatibilidadsuelos = $urlcompatibilidadsuelos;
        return $this;
    }
    public function getHorario(): ?string
    {
        return $this->horario;
    }
    public function setHorario(?string $horario): self
    {
        $this->horario = $horario;
        return $this;
    }
    public function getEstado(): int
    {
        return $this->estado;
    }
    public function setEstado(int $estado): self
    {
        $this->estado = $estado;
        return $this;
    }
    public function getTipo(): Tipo
    {
        return $this->tipo;
    }
    public function setTipo(Tipo $tipo): self
    {
        $this->tipo = $tipo;
        return $this;
    }
    public function getDistrito(): Distrito
    {
        return $this->distrito;
    }
    public function setDistrito(Distrito $distrito): self
    {
        $this->distrito = $distrito;
        return $this;
    }
    public function getProvincia(): Provincia
    {
        return $this->provincia;
    }
    public function setProvincia(Provincia $provincia): self
    {
        $this->provincia = $provincia;
        return $this;
    }
    public function getDepartamento(): Departamento
    {
        return $this->departamento;
    }
    public function setDepartamento(Departamento $departamento): self
    {
        $this->departamento = $departamento;
        return $this;
    }
    public function getNegocio(): Negocio
    {
        return $this->negocio;
    }
    public function setNegocio(Negocio $negocio): self
    {
        $this->negocio = $negocio;
        return $this;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(?string $area): self
    {
        $this->area = $area;
        return $this;
    }
}
