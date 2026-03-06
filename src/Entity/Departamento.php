<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'departamentos')]
class Departamento
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 2)]
    private string $id;

    #[ORM\Column(type: 'string', length: 50)]
    private string $nombre;

    // --- Getters y Setters ---
    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }
}
