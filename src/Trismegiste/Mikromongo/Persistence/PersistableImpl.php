<?php

/*
 * Dokudokibundle
 */

namespace Trismegiste\Mikromongo\Persistence;

/**
 * PersistableImpl is an implementation for interface Persistable
 */
trait PersistableImpl
{

    protected $id;

    public function setId(\MongoId $pk)
    {
        $this->id = $pk;
    }

    public function getId()
    {
        return $this->id;
    }

}