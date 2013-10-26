<?php

/*
 * Mikromongo
 */

namespace tests\unit;

use Trismegiste\Mikromongo\Serializer;

/**
 * Provider is a collection of provider
 */
trait DataProvider
{

    public function getInternalType()
    {
        $data = [
            false, true,
            123, 123.456,
            "illogical",
            null,
            [123], [123, 456],
            [[], 123, [456, [[[]]]], "w:;\":s:3:\"esh" => 789, "str" => "com\"bo", [[]]]
        ];
        $fixtures = [];
        foreach ($data as $val) {
            $fixtures[] = [$val];
        }

        return $fixtures;
    }

    public function getObjectType()
    {
        $simple = new \stdClass();
        $simple->prop = 123;

        $cplx = clone $simple;
        $cplx->tab = array(1, true, "2\";2", array(3, new \stdClass()), 4);

        $ref = new \stdClass();
        $ref->p1 = new \stdClass();
        $ref->p2 = $ref->p1;
        $ref->p3 = 123;
        $ref->p4 = &$ref->p3;

        return [
            [$simple, [Serializer::META_CLASS => 'stdClass', 'prop' => 123]],
            [
                $cplx,
                [
                    Serializer::META_CLASS => 'stdClass',
                    'prop' => 123,
                    'tab' => [
                        1, true, "2\";2",
                        [3, [Serializer::META_CLASS => 'stdClass']],
                        4
                    ]
                ]
            ],
            [
                $ref,
                [
                    Serializer::META_CLASS => 'stdClass',
                    'p1' => [Serializer::META_CLASS => 'stdClass'],
                    'p2' => [Serializer::META_REFERENCE => ['r' => 2]],
                    'p3' => 123,
                    'p4' => [Serializer::META_REFERENCE => ['R' => 4]]
                ]
            ],
            [
                new \tests\fixtures\Access(),
                [
                    Serializer::META_CLASS => 'tests\fixtures\Access',
                    '-notInherited' => 111,
                    '#inherited' => 222,
                    'openbar' => 333
                ]
            ],
            [
                new \ArrayObject([1, 2, 3]),
                [
                    Serializer::META_CLASS => 'ArrayObject',
                    Serializer::META_CUSTOM => new \MongoBinData('x:i:0;a:3:{i:0;i:1;i:1;i:2;i:2;i:3;};m:a:0:{}', \MongoBinData::BYTE_ARRAY)
                ]
            ]
        ];
    }

    public function getSplType()
    {
        $spl2 = new \SplObjectStorage();
        $spl2[new \stdClass()] = 123;
        $spl2[new \stdClass()] = 456;
        $flt2 = [Serializer::META_CLASS => 'SplObjectStorage', Serializer::META_CUSTOM => new \MongoBinData('x:i:2;O:8:"stdClass":0:{},i:123;;O:8:"stdClass":0:{},i:456;;m:a:0:{}', 2)];

        return [
            [$spl2, $flt2]
        ];
    }

}