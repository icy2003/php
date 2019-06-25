<?php

namespace icy2003\php_tests\ihelpers;

use icy2003\php\ihelpers\Arrays;

class ArraysTest extends \Codeception\Test\Unit
{

    public function testIndexBy()
    {
        parent::assertEquals(Arrays::indexBy(
            [
                ['id' => 1, 'name' => 'tom'],
                ['id' => 2, 'name' => 'jerry'],
            ],
            'id'
        ),
            [
                1 => ['id' => 1, 'name' => 'tom'],
                2 => ['id' => 2, 'name' => 'jerry'],
            ]
        );
        parent::assertEquals(Arrays::indexBy(
            [
                ['id' => 1, 'name' => 'tom'],
                ['id' => 2, 'name' => 'jerry'],
                ['id' => 2, 'name' => 'jerry2'],
            ],
            'id',
            true
        ),
            [
                1 => [
                    ['id' => 1, 'name' => 'tom'],
                ],
                2 => [
                    ['id' => 2, 'name' => 'jerry'],
                    ['id' => 2, 'name' => 'jerry2'],
                ],
            ]
        );
    }

    public function testColumns()
    {
        parent::assertEquals(Arrays::columns(
            [
                ['id' => 1, 'name' => 'tom', 'type' => 'cat'],
                ['id' => 2, 'name' => 'jerry', 'type' => 'rat'],
            ],
            ['id', 'name']
        ),
            [
                ['id' => 1, 'name' => 'tom'],
                ['id' => 2, 'name' => 'jerry'],
            ]
        );
    }

    public function testColumn()
    {
        parent::assertEquals(Arrays::column(
            [
                ['id' => 1, 'name' => 'tom'],
                ['id' => 2, 'name' => 'jerry'],
            ],
            'name'
        ),
            ['tom', 'jerry']
        );
        parent::assertEquals(Arrays::column(
            [
                ['id' => 1, 'name' => 'tom'],
                ['id' => 2, 'name' => 'jerry'],
            ],
            'name',
            'id'
        ),
            [1 => 'tom', 2 => 'jerry']
        );
    }

    public function testKeyExistsAll()
    {
        parent::assertTrue(Arrays::keyExistsAll(
            ['id', 'name'],
            ['id' => 1, 'name' => 'tom', 'type' => 'cat']
        ));
        parent::assertFalse(Arrays::keyExistsAll(
            ['image'],
            ['id' => 1, 'name' => 'tom', 'type' => 'cat']
        ));
    }

    public function testKeyExistsSome()
    {
        parent::assertTrue(Arrays::keyExistsSome(
            ['id', 'name', 'name2'],
            ['id' => 1, 'name' => 'tom', 'type' => 'cat']
        ));
        parent::assertFalse(Arrays::keyExistsSome(
            ['image'],
            ['id' => 1, 'name' => 'tom', 'type' => 'cat']
        ));
    }

    public function testCombines()
    {
        parent::assertEquals(Arrays::combines(
            ['id', 'name'],
            [
                [1, 'tom'],
                [2, 'jerry'],
            ]
        ),
            [
                ['id' => 1, 'name' => 'tom'],
                ['id' => 2, 'name' => 'jerry'],
            ]
        );
    }

    public function testMerge()
    {
        parent::assertEquals(Arrays::merge(
            ['tom'],
            ['jerry']
        ),
            ['tom', 'jerry']
        );
        parent::assertEquals(Arrays::merge(
            ['tom'],
            ['jerry'],
            ['speike']
        ),
            ['tom', 'jerry', 'speike']
        );
        parent::assertEquals(Arrays::merge(
            ['name' => 'tom'],
            ['name' => 'jerry']
        ),
            ['name' => 'jerry']
        );
    }

    public function testRangeGenerator()
    {
        $array1 = [];
        foreach (Arrays::rangeGenerator(1, 3) as $value) {
            $array1[] = $value;
        }
        $array2 = [];
        foreach (Arrays::rangeGenerator(1, 5, 2) as $value) {
            $array2[] = $value;
        }
        parent::assertEquals($array1, [1, 2, 3]);
        parent::assertEquals($array2, [1, 3, 5]);
    }

    public function testTransposed()
    {
        parent::assertEquals(Arrays::transposed(
            [
                [1, 2, 3],
                [4, 5, 6],
            ]
        ),
            [
                [1, 4],
                [2, 5],
                [3, 6],
            ]
        );
    }

    public function testDetectFirst()
    {
        parent::assertEquals(Arrays::detectFirst(
            [1, 2, 3],
            function ($v) {
                return $v > 1;
            }
        ),
            2
        );
    }

    public function testDetectAll()
    {
        parent::assertEquals(Arrays::detectAll(
            [1, 2, 3],
            function ($v) {
                return $v > 1;
            }
        ),
            [1 => 2, 2 => 3]
        );
        parent::assertEquals(Arrays::detectAll(
            [1, 2, 3],
            function ($v) {
                return $v > 1;
            },
            function ($v) {
                return $v + 1;
            }
        ),
            [1 => 3, 2 => 4]
        );
    }

    public function testKeyLast()
    {
        parent::assertEquals(Arrays::keyLast(
            ['id' => 1, 'name' => 'tom']
        ),
            'name'
        );
    }

    public function testKeyFirst()
    {
        parent::assertEquals(Arrays::keyFirst(
            [1, 2, 3]
        ),
            0
        );
    }

    public function testToPart()
    {
        parent::assertEquals(Arrays::toPart(
            ['1,,4', '3', 2, '4']
        ),
            ['1', '4', '3', '2']
        );
    }

    public function testToCellArray()
    {
        parent::assertEquals(Arrays::toCellArray(
            [
                [1, 2, 3],
                [4, 5, 6],
            ]
        ),
            [
                1 => ['A' => 1, 'B' => 2, 'C' => 3],
                2 => ['A' => 4, 'B' => 5, 'C' => 6],
            ]
        );
    }

    public function testDimension()
    {
        parent::assertEquals(Arrays::dimension(
            [1, 2]
        ),
            1
        );
        parent::assertEquals(Arrays::dimension(
            [
                [
                    'name' => 'tom',
                    'ability' => [
                        'fly' => true,
                    ],
                ],
                [
                    'name' => 'jerry',
                ],
            ]
        ),
            3
        );
    }

    public function testIsAssoc()
    {
        parent::assertTrue(Arrays::isAssoc(
            ['name' => 'tom']
        ));
        parent::assertFalse(Arrays::isAssoc(
            [1, 2]
        ));
    }

    public function testIsIndexed()
    {
        parent::assertFalse(Arrays::isIndexed(
            ['name' => 'tom']
        ));
        parent::assertTrue(Arrays::isIndexed(
            [1, 2]
        ));
    }

    public function testFirst()
    {
        parent::assertEquals(Arrays::first(
            [1, 2, 3]
        ),
            1
        );
        parent::assertEquals(Arrays::first(
            [1, 2, 3],
            3
        ),
            3
        );
        parent::assertEquals(Arrays::first(
            []
        ),
            null
        );
        parent::assertEquals(Arrays::first(
            [
                ['name' => 'tom'],
                ['name' => 'jerry'],
            ],
            2
        ),
            ['name' => 'jerry']
        );
    }

    public function testLast()
    {
        parent::assertEquals(Arrays::last(
            [1, 2, 3]
        ),
            3
        );
        parent::assertEquals(Arrays::last(
            [1, 2, 3],
            3
        ),
            1
        );
        parent::assertEquals(Arrays::last(
            []
        ),
            null
        );
        parent::assertEquals(Arrays::last(
            [
                ['name' => 'tom'],
                ['name' => 'jerry'],
            ],
            2
        ),
            ['name' => 'tom']
        );
    }
}
