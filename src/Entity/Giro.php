<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'giro')]
class Giro {
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 250)]
    private string $actividad;

    #[ORM\Column(type: 'string', length: 5)]
    private string $codigo;

    public function getId(): ?int { return $this->id; }
    public function getActividad(): string { return $this->actividad; }
    public function setActividad(string $actividad): self { $this->actividad = $actividad; return $this; }
    public function getCodigo(): string { return $this->codigo; }
    public function setCodigo(string $codigo): self { $this->codigo = $codigo; return $this; }
}
