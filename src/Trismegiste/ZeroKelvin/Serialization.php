<?php

/*
 * ZeroKelvin
 */

namespace Trismegiste\ZeroKelvin;

/**
 * Serialization is a contract for transformations
 * based on php serialization
 */
interface Serialization
{

    const META_CLASS = '@classname';
    const META_PRIVATE = '-';
    const META_PUBLIC = '+';
    const META_CUSTOM = '@content';
    const META_REF = '@ref';

}