<?php

/**
 * Persistence
 */

namespace Trismegiste\Mikromongo\Persistence;

/**
 * A contract for a repository
 */
interface RepositoryInterface
{

    /**
     * Transforms an object tree into a tree/array and persists it 
     * into the database layer
     * 
     * @param Persistable $doc
     */
    function persist(Persistable $doc);

    /**
     * Finds an object from the database for a given primary key and
     * maps it with a transformer into a real object.
     *
     * @param string $id the primary key
     * 
     * @return Persistable
     *
     * @throws NotFoundException When no object found for this pk
     */
    function findByPk($id);

    /**
     * Creates an instance and maps this object with data retrieved from 
     * database. Usefull when iterating over a MongoCursor
     * 
     * @param array $struc a raw structure coming from database
     * 
     * @return Persistable
     */
    function createFromDb(array $struc);
}
