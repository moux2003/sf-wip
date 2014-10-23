<?php

namespace Lpo\InfraBundle\Entity;

use Lpo\Domain\Exemple as BaseExemple;

class Exemple extends BaseExemple
{
    protected $id;

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
