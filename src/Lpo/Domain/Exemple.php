<?php

namespace Lpo\Domain;

class Exemple
{
    protected $nom;
    protected $description;

    public function __construct($nom)
    {
        $this->nom = $nom;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function changeNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
