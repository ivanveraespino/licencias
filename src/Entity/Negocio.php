<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'negocio')]
class Negocio {
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 11)]
    private string $ruc;

    #[ORM\Column(type: 'string', length: 250)]
    private string $razonsocial;

    #[ORM\Column(type: 'string', length: 250)]
    private string $urlficha;

    #[ORM\ManyToOne(targetEntity: Giro::class)]
    #[ORM\JoinColumn(name: 'idgiro', referencedColumnName: 'id')]
    private Giro $giro;

    #[ORM\Column(type: 'integer')]
    private int $estado;

    public function getId(): ?int { return $this->id; }
    public function getRuc(): string { return $this->ruc; }
    public function setRuc(string $ruc): self { $this->ruc = $ruc; return $this; }
    public function getRazonsocial(): string { return $this->razonsocial; }
    public function setRazonsocial(string $razonsocial): self { $this->razonsocial = $razonsocial; return $this; }
    
    public function getUrlficha(): string { return $this->urlficha; }
    public function setUrlficha(string $urlficha): self { $this->urlficha = $urlficha; return $this; }
    public function getGiro(): Giro { return $this->giro; }
    public function setGiro(Giro $giro): self { $this->giro = $giro; return $this; }
    public function getEstado(): int { return $this->estado; }
    public function setEstado(int $estado): self { $this->estado = $estado; return $this; }
}
