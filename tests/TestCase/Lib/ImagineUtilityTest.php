<?php

/**
 * Copyright 2011-2014, Florian Krämer
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * Copyright 2011-2014, Florian Krämer
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('ImagineUtility', 'Imagine.Lib');

class ImagineUtilityTest extends CakeTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [];

    /**
     * testOperationsToString
     *
     * @return void
     */
    public function testOperationsToString(): void
    {
        $operations = [
            'thumbnail' => [
                'width' => 200,
                'height' => 150]];
        $result = ImagineUtility::operationsToString($operations);
        $this->assertEquals($result, '.thumbnail+width-200+height-150');
    }

    /**
     * All operations are included, sorted by name
     *
     * @return void
     */
    public function testOperationsToStringMultipleOperations(): void
    {
        $operations = [
            'thumbnail' => [
                'width' => 200,
                'height' => 150,
            ],
            'crop' => [
                'width' => 100,
                'height' => 100,
            ],
        ];
        $result = ImagineUtility::operationsToString($operations);
        $this->assertSame('.crop+width-100+height-100.thumbnail+width-200+height-150', $result);

        $result = ImagineUtility::operationsToString($operations, ['operations' => '_', 'params' => '~', 'value' => '=']);
        $this->assertSame('_crop~width=100~height=100_thumbnail~width=200~height=150', $result);

        $result = ImagineUtility::operationsToString($operations, [], 'md5');
        $this->assertSame(md5('.crop+width-100+height-100.thumbnail+width-200+height-150'), $result);
    }

    /**
     * testHashImageOperations
     *
     * @return void
     */
    public function testHashImageOperations(): void
    {
        $operations = [
            'SomeModel' => [
                't200x150' => [
                    'thumbnail' => [
                        'width' => 200,
                        'height' => 150,
                    ],
                ],
            ],
        ];

        $result = ImagineUtility::hashImageOperations($operations);
        $this->assertEquals($result, [
            'SomeModel' => [
                't200x150' => '38b1868f',
            ],
        ]);
    }

    /**
     * The hash length argument is used
     *
     * @return void
     */
    public function testHashImageOperationsLength(): void
    {
        $operations = [
            'SomeModel' => [
                't200x150' => [
                    'thumbnail' => [
                        'width' => 200,
                        'height' => 150,
                    ],
                ],
            ],
        ];

        $result = ImagineUtility::hashImageOperations($operations, 12);
        $this->assertSame(12, strlen($result['SomeModel']['t200x150']));
        $this->assertStringStartsWith('38b1868f', $result['SomeModel']['t200x150']);

        $result = ImagineUtility::hashImageOperations($operations, 4);
        $this->assertSame('38b1', $result['SomeModel']['t200x150']);
    }
}
