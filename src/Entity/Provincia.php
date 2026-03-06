<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'provincias')]
class Provincia
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 4)]
    private string $id;

    #[ORM\Column(type: 'string', length: 50)]
    private string $nombre;

    #[ORM\ManyToOne(targetEntity: Departamento::class)]
    #[ORM\JoinColumn(name: 'iddep', referencedColumnName: 'id')]
    private Departamento $departamento;

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

    public function getDepartamento(): Departamento
    {
        return $this->departamento;
    }

    public function setDepartamento(Departamento $departamento): self
    {
        $this->departamento = $departamento;
        return $this;
    }
}
