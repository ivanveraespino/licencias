<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'distritos')]
class Distrito
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 6)]
    private string $id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $nombre;

    #[ORM\ManyToOne(targetEntity: Provincia::class)]
    #[ORM\JoinColumn(name: 'idprov', referencedColumnName: 'id')]
    private Provincia $provincia;

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

    public function getProvincia(): Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(Provincia $provincia): self
    {
        $this->provincia = $provincia;
        return $this;
    }
}
