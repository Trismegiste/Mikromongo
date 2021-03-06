<?php

/*
 * ZeroKelvin
 */

namespace tests\unit;

use tests\fixtures\Vertex;
use Trismegiste\ZeroKelvin\Unserializer;
use Trismegiste\ZeroKelvin\Serializer;
use Trismegiste\ZeroKelvin\UuidFactory;

/**
 * ReferenceTest tests the unserialization of reference
 */
class ReferenceTest extends \PHPUnit_Framework_TestCase
{

    protected $unserial;
    protected $serial;

    protected function setUp()
    {
        $fac = new UuidFactory();
        $this->unserial = new Unserializer($fac);
        $this->serial = new Serializer($fac);
    }

    public function testCycle()
    {
        $vertices = array_map(function($v) {
                    return new Vertex($v);
                }, range(0, 3));

        $vertices[0]->add($vertices[1]);
        $vertices[0]->add($vertices[2]);
        $vertices[0]->add($vertices[3]);
        $vertices[1]->add($vertices[3]);
        $vertices[2]->add($vertices[3]);
        $vertices[3]->add($vertices[0]);

        $str = serialize($vertices[0]);
        $dump = $this->unserial->toArray($str);
        //print_r($dump);

        $restore = $this->serial->fromArray($dump);
        //var_dump($str, $restore);
        $this->assertEquals($str, $restore);
        $this->assertEquals($vertices[0], unserialize($restore));
    }

}