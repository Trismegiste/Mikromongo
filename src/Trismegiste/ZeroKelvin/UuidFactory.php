<?php

/*
 * ZeroKelvin
 */

namespace Trismegiste\ZeroKelvin;

/**
 * UuidFactory creates UUID unique id
 * Copy pasted from php.net
 */
class UuidFactory implements UniqueGenerator
{

    public function create()
    {
        return sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', 
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), 
                mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, 
                mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), 
                mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function getFieldName()
    {
        return '@uuid';
    }

}