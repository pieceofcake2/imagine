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
}
