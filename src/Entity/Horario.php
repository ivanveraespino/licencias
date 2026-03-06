<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'horario')]
class Horario
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $inicio;

    #[ORM\Column(type: 'integer')]
    private int $fin;

    #[ORM\ManyToOne(targetEntity: Sede::class)]
    #[ORM\JoinColumn(name: 'idsede', referencedColumnName: 'id')]
    private Sede $sede;

    // --- Getters y Setters ---
    public function getId(): ?int { return $this->id; }
    public function getInicio(): int { return $this->inicio; }
    public function setInicio(int $inicio): self { $this->inicio = $inicio; return $this; }
    public function getFin(): int { return $this->fin; }
    public function setFin(int $fin): self { $this->fin = $fin; return $this; }
    public function getSede(): Sede { return $this->sede; }
    public function setSede(Sede $sede): self { $this->sede = $sede; return $this; }
}
