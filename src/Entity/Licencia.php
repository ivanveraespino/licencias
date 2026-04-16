<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'licencia')]
class Licencia
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $tipo;

    #[ORM\Column(type: 'integer')]
    private int $dias;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $inicio;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $fin=null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $resolucion;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $expediente;

    #[ORM\Column(type: 'string', length: 10 , nullable:true)]
    private ?string $numero;

    
    #[ORM\Column(type: 'string', length: 250)]
    private ?string $urlresolucion;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $horario;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $acervo;

    #[ORM\Column(type: 'string', length: 250)]
    private ?string $urllicencia;

    #[ORM\Column(type: 'integer')]
    private int $estado;

    #[ORM\ManyToOne(targetEntity: Sede::class)]
    #[ORM\JoinColumn(name: 'idsede', referencedColumnName: 'id')]
    private Sede $sede;

    // --- Getters y Setters ---
    public function getId(): ?int { return $this->id; }
    public function getTipo(): int { return $this->tipo; }
    public function setTipo(int $tipo): self { $this->tipo = $tipo; return $this; }
    public function getDias(): int { return $this->dias; }
    public function setDias(int $dias): self { $this->dias = $dias; return $this; }
    public function getInicio(): \DateTimeInterface { return $this->inicio; }
    public function setInicio(\DateTimeInterface $inicio): self { $this->inicio = $inicio; return $this; }
    public function getFin(): ?\DateTimeInterface { return $this->fin; }
    public function setFin(\DateTimeInterface $fin): self { $this->fin = $fin; return $this; }
    
    public function getUrllicencia(): ?string { return $this->urllicencia; }
    public function setUrllicencia(?string $urllicencia): self { $this->urllicencia = $urllicencia; return $this; }

    public function getAcervo(): ?string { return $this->acervo; }
    public function setAcervo(?string $acervo): self { $this->acervo = $acervo; return $this; }

    public function getHorario(): ?string { return $this->horario; }
    public function setHorario(?string $horario): self { $this->horario = $horario; return $this; }

    public function getUrlresolucion(): ?string { return $this->urlresolucion; }
    public function setUrlresolucion(?string $urlresolucion): self { $this->urlresolucion = $urlresolucion; return $this; }
    
    public function getResolucion(): string { return $this->resolucion; }
    public function setResolucion(string $resolucion): self { $this->resolucion = $resolucion; return $this; }
    public function getExpediente(): ?string { return $this->expediente; }
    public function setExpediente(?string $expediente): self { $this->expediente = $expediente; return $this; }
    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(?string $numero): self { $this->numero = $numero; return $this; }
    public function getEstado(): int { return $this->estado; }
    public function setEstado(int $estado): self { $this->estado = $estado; return $this; }
    public function getSede(): Sede { return $this->sede; }
    public function setSede(Sede $sede): self { $this->sede = $sede; return $this; }
}
