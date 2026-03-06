<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tipo')]
class Tipo {
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 250)]
    private string $denominacion;

    #[ORM\ManyToOne(targetEntity: Giro::class)]
    #[ORM\JoinColumn(name: 'idgiro', referencedColumnName: 'id')]
    private Giro $giro;

    public function getId(): ?int { return $this->id; }
    public function getDenominacion(): string { return $this->denominacion; }
    public function setDenominacion(string $denominacion): self { $this->denominacion = $denominacion; return $this; }
    public function getGiro(): Giro { return $this->giro; }
    public function setGiro(Giro $giro): self { $this->giro = $giro; return $this; }
}
