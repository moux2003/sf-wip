<?php

namespace Lpo\SecurityBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;

class User extends BaseUser
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
