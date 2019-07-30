<?php

namespace icy2003\php_tests\ihelpers;

use icy2003\php\I;
use icy2003\php\ihelpers\Arrays;

class ArraysTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testIndexBy()
    {
        $this->tester->assertEquals(Arrays::indexBy(
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
        $this->tester->assertEquals(Arrays::indexBy(
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
        $this->tester->assertEquals(Arrays::indexBy([
            ['id' => 1, 'name' => 'tom'],
        ], 'type'), []);
    }

    public function testColumns()
    {
        $this->tester->assertEquals(Arrays::columns(
            [
                ['id' => 1, 'name' => 'tom', 'type' => 'cat'],
                ['id' => 2, 'name' => 'jerry', 'type' => 'rat'],
            ],
            ['id', 'name'],
            2
        ),
            [
                ['id' => 1, 'name' => 'tom'],
                ['id' => 2, 'name' => 'jerry'],
            ]
        );
        $this->tester->assertEquals(Arrays::columns(
            ['id' => 1, 'name' => 'tom', 'type' => 'cat'],
            ['id', 'name'],
            1
        ), ['id' => 1, 'name' => 'tom', 'type' => null]);
        $this->tester->assertEquals(Arrays::columns(['a', 'b']), ['a', 'b']);
        $this->tester->assertEquals(Arrays::columns(
            [
                ['id' => 1, 'name' => 'tom', 'type' => 'cat'],
                ['id' => 2, 'name' => 'jerry', 'type' => 'rat'],
            ],
            ['id1'],
            2
        ),
            [
                ['id1' => null],
                ['id1' => null],
            ]
        );
    }

    public function testColumn()
    {
        I::ini('USE_CUSTOM', false);
        $this->tester->assertEquals(Arrays::column(
            [
                ['id' => 1, 'name' => 'tom'],
                ['id' => 2, 'name' => 'jerry'],
            ],
            'name'
        ),
            ['tom', 'jerry']
        );
        $this->tester->assertEquals(Arrays::column(
            [
                ['id' => 1, 'name' => 'tom'],
                ['id' => 2, 'name' => 'jerry'],
            ],
            'name',
            'id'
        ),
            [1 => 'tom', 2 => 'jerry']
        );
        I::ini('USE_CUSTOM', true);
        $this->tester->assertEquals(Arrays::column(
            [
                ['id' => 1, 'name' => 'tom'],
                ['id' => 2, 'name' => 'jerry'],
            ],
            'name',
            'id'
        ),
            [1 => 'tom', 2 => 'jerry']
        );
        $this->tester->assertEquals(Arrays::column(
            [
                ['id' => 1, 'name' => 'tom'],
                ['id' => 2, 'name' => 'jerry'],
            ],
            'name',
            null
        ),
            [0 => 'tom', 1 => 'jerry']
        );
        I::ini('USE_CUSTOM', false);
    }

    public function testCombine()
    {
        $this->tester->assertEquals(Arrays::combine(
            ['id', 'name'],
            [2, 'tom']
        ),
            ['id' => 2, 'name' => 'tom']);
        $this->tester->assertEquals(Arrays::combine(
            ['id'],
            [2, 'tom']
        ),
            ['id' => 2]);
    }

    public function testMerge()
    {
        $this->tester->assertEquals(Arrays::merge(
            ['tom'],
            ['jerry']
        ),
            ['tom', 'jerry']
        );
        $this->tester->assertEquals(Arrays::merge(
            ['tom'],
            ['jerry'],
            ['speike']
        ),
            ['tom', 'jerry', 'speike']
        );
        $this->tester->assertEquals(Arrays::merge(
            ['name' => 'tom'],
            ['name' => 'jerry']
        ),
            ['name' => 'jerry']
        );
        $this->tester->assertEquals(Arrays::merge(
            ['name' => 'tom', 'attr' => [
                'height' => '1m',
            ]],
            ['name' => 'jerry', 'attr' => [
                'color' => 'blue',
            ]]
        ),
            ['name' => 'jerry', 'attr' => [
                'height' => '1m',
                'color' => 'blue',
            ]]
        );
    }

    public function testRangeGenerator()
    {
        $array1 = [];
        foreach (Arrays::rangeGenerator(1, 3) as $value) {
            $array1[] = $value;
        }
        $array2 = [];
        foreach (Arrays::rangeGenerator(5, 1, -2) as $value) {
            $array2[] = $value;
        }
        $this->tester->assertEquals($array1, [1, 2, 3]);
        $this->tester->assertEquals($array2, [5, 3, 1]);
        try {
            foreach (Arrays::rangeGenerator(1, 5, -1) as $value) {
            }
        } catch (\LogicException $e) {
            $this->tester->assertTrue(true);
        }
        try {
            foreach (Arrays::rangeGenerator(5, 1, 1) as $value) {
            }
        } catch (\LogicException $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testDetectFirst()
    {
        $this->tester->assertEquals(Arrays::detectFirst(
            [1, 2, 3],
            function ($v) {
                return $v > 1;
            }
        ),
            2
        );
        $this->tester->assertEquals(Arrays::detectFirst(
            [1, 2, 3],
            function ($v) {
                return $v > 5;
            }
        ),
            null
        );
    }

    public function testDetectAll()
    {
        $this->tester->assertEquals(Arrays::detectAll(
            [1, 2, 3],
            function ($v) {
                return $v > 1;
            }
        ),
            [1 => 2, 2 => 3]
        );
        $this->tester->assertEquals(Arrays::detectAll(
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
        I::ini('USE_CUSTOM', false);
        $this->tester->assertEquals(Arrays::keyLast(['id' => 1, 'name' => 'tom']), 'name');
        $this->tester->assertEquals(Arrays::keyLast(1), null);
        I::ini('USE_CUSTOM', true);
        $this->tester->assertEquals(Arrays::keyLast(['id' => 1, 'name' => 'tom']), 'name');
        I::ini('USE_CUSTOM', false);
    }

    public function testKeyFirst()
    {
        I::ini('USE_CUSTOM', false);
        $this->tester->assertEquals(Arrays::keyFirst([1, 2, 3]), 0);
        $this->tester->assertEquals(Arrays::keyFirst(1), null);
        I::ini('USE_CUSTOM', true);
        $this->tester->assertEquals(Arrays::keyFirst([1, 2, 3]), 0);
        I::ini('USE_CUSTOM', false);
    }

    public function testDimension()
    {
        $this->tester->assertEquals(Arrays::dimension([1, 2]), 1);
        $this->tester->assertEquals(Arrays::dimension(
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
        $this->tester->assertEquals(Arrays::dimension(1), 0);
    }

    public function testIsAssoc()
    {
        $this->tester->assertTrue(Arrays::isAssoc(['name' => 'tom']));
        $this->tester->assertFalse(Arrays::isAssoc([1, 2]));
        $this->tester->assertFalse(Arrays::isAssoc(1));
    }

    public function testIsIndexed()
    {
        $this->tester->assertFalse(Arrays::isIndexed(['name' => 'tom']));
        $this->tester->assertTrue(Arrays::isIndexed([1, 2]));
        $this->tester->assertFalse(Arrays::isIndexed(1));
    }

    public function testFirst()
    {
        $this->tester->assertEquals(Arrays::first([1, 2, 3]), 1);
        $this->tester->assertEquals(Arrays::first([1, 2, 3], 3), 3);
        $this->tester->assertEquals(Arrays::first([]), null);
        $this->tester->assertEquals(Arrays::first(
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
        $this->tester->assertEquals(Arrays::last([1, 2, 3]), 3);
        $this->tester->assertEquals(Arrays::last([1, 2, 3], 3), 1);
        $this->tester->assertEquals(Arrays::last([]), null);
        $this->tester->assertEquals(Arrays::last(
            [
                ['name' => 'tom'],
                ['name' => 'jerry'],
            ],
            2
        ),
            ['name' => 'tom']
        );
    }

    public function testCount()
    {
        $this->tester->assertEquals(Arrays::count(0), 0);
        $this->tester->assertEquals(Arrays::count([1, 2]), 2);
        $this->tester->assertEquals(Arrays::count([1, 2, 3, 4], 2), 1);
        $this->tester->assertEquals(Arrays::count([1, 2, 3, 4], '2', false), 1);
    }

    public function testLists()
    {
        $this->tester->assertEquals(Arrays::lists([1, 2, 3], 4), [1, 2, 3, null]);
        $this->tester->assertEquals(Arrays::lists([1, 2, 3], 2), [1, 2, 3]);
        $this->tester->assertEquals(Arrays::lists(['a' => '2aa', 'b' => 'b4b'], 2, 'trim'), ['a' => 2, 'b' => 4]);
    }

    public function testValues()
    {
        $this->tester->assertEquals(Arrays::values(
            [
                'id' => 1,
                'name' => 'tom',
                'type' => 'cat',
            ],
            'id,type'
        ), [1, 'cat']);
        $this->tester->assertEquals(Arrays::values(
            [
                'id' => 1,
                'name' => 'tom',
                'type' => 'cat',
            ],
            ['id', 'type']
        ), [1, 'cat']);
    }

    public function testSome()
    {
        $this->tester->assertEquals(Arrays::some(
            [
                'id' => 1,
                'name' => 'tom',
                'type' => 'cat',
            ],
            'id,type'
        ), ['id' => 1, 'type' => 'cat']);
        $this->tester->assertEquals(Arrays::some(
            [
                'id' => 1,
                'name' => 'tom',
                'type' => 'cat',
            ],
            ['id', 'type']
        ), ['id' => 1, 'type' => 'cat']);
        $this->tester->assertEquals(Arrays::some(['a']), ['a']);
    }

    public function testExceptedKeys()
    {
        $this->tester->assertEquals(Arrays::exceptedKeys(
            [
                'id' => 1,
                'name' => 'tom',
                'type' => 'cat',
            ],
            'id,name'
        ), ['type' => 'cat']);
        $this->tester->assertEquals(Arrays::exceptedKeys(
            [
                'id' => 1,
                'name' => 'tom',
                'type' => 'cat',
            ],
            ['id', 'name']
        ), ['type' => 'cat']);
    }

    public function testKeyExistsAll()
    {
        $this->tester->assertTrue(Arrays::keyExistsAll(
            ['id', 'name'],
            ['id' => 1, 'name' => 'tom', 'type' => 'cat']
        ));
        $this->tester->assertFalse(Arrays::keyExistsAll(
            ['image'],
            ['id' => 1, 'name' => 'tom', 'type' => 'cat']
        ));
    }

    public function testKeyExistsSome()
    {
        $this->tester->assertTrue(Arrays::keyExistsSome(
            ['id', 'name', 'name2'],
            ['id' => 1, 'name' => 'tom', 'type' => 'cat']
        ));
        $this->tester->assertFalse(Arrays::keyExistsSome(
            ['image'],
            ['id' => 1, 'name' => 'tom', 'type' => 'cat']
        ));
    }

    public function testValueExistsAll()
    {
        $this->tester->assertTrue(Arrays::valueExistsAll(
            ['name'],
            ['name', 'type']
        ));
        $this->tester->assertFalse(Arrays::valueExistsAll(
            ['name', 'name2'],
            ['name', 'type'],
            $diff
        ));
        $this->tester->assertEquals($diff, [1 => 'name2']);
    }

    public function testValueExistsSome()
    {
        $this->tester->assertTrue(Arrays::valueExistsSome(
            ['name', 'name2'],
            ['name', 'type'],
            $find
        ));
        $this->tester->assertEquals($find, ['name']);
    }

    public function testCombines()
    {
        $this->tester->assertEquals(Arrays::combines(
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

    public function testToPart()
    {
        $this->tester->assertEquals(Arrays::toPart(
            ['1,,4', '3', 2, '4']
        ),
            ['1', '4', '3', '2']
        );
    }

    public function testTransposed()
    {
        $this->tester->assertEquals(Arrays::transposed(
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

    public function testToCellArray()
    {
        $this->tester->assertEquals(Arrays::toCellArray(
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

    public function testColRowCount()
    {
        $this->tester->assertEquals(Arrays::colRowCount([
            [1, 2, 3],
            [4, 5, 6],
        ]), [3, 2]);
    }

    public function testFill()
    {
        $this->tester->assertEquals(Arrays::fill(0, 3, 'a'), ['a', 'a', 'a']);
        $this->tester->assertEquals(Arrays::fill(-2, 3, 'a'), [
            '-2' => 'a', '-1' => 'a', '0' => 'a',
        ]);
        $this->tester->assertEquals(Arrays::fill(0, -1, 'a'), []);
    }

    public function testExport()
    {
        $array = Arrays::export(['a']);
        ob_start();
        Arrays::export(['a'], false);
        $temp = ob_get_contents();
        $this->tester->assertEquals($temp, $array);
        ob_end_clean();
    }

    public function testFromCsv()
    {
        $this->tester->assertEquals(Arrays::fromCsv('1,2,3' . PHP_EOL . '4,5,6'), [
            [1, 2, 3],
            [4, 5, 6],
        ]);
    }

    public function testSearch()
    {
        $this->tester->assertEquals(Arrays::search(1, ['a' => 1]), 'a');
        $this->tester->assertEquals(Arrays::search(function ($x) {
            return $x == 1;
        },
            ['a' => '1a']
        ), 'a');
        $this->tester->assertFalse(Arrays::search(function ($x) {
            return $x == 1;
        }, ['a' => 2]));
    }

    public function testIncrement()
    {
        $array = [
            'count' => 1,
        ];
        $this->tester->assertEquals(Arrays::increment($array, 'count', 2), 3);
        $this->tester->assertEquals($array, [
            'count' => 3,
        ]);
    }

    public function testDecrement()
    {
        $array = [
            'count' => 3,
        ];
        $this->tester->assertEquals(Arrays::decrement($array, 'count', 2), 1);
        $this->tester->assertEquals($array, [
            'count' => 1,
        ]);
    }

}
