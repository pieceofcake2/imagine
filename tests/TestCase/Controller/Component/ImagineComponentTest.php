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

App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('ImagineComponent', 'Imagine.Controller/Component');

if (!class_exists('ImagineImagesTestController')) {
    /**
     * @property Imagine $Imagine
     */
    class ImagineImagesTestController extends Controller
    {
        /**
         * @var string
         */
        public $name = 'Images';

        /**
         * @var array
         */
        public $uses = ['Images'];

        /**
         * @var array
         */
        public $components = [
            'Session',
            'Imagine.Imagine',
        ];

        /**
         * Redirect url
         *
         * @var mixed
         */
        public $redirectUrl = null;

        public function beforeFilter(): void
        {
            parent::beforeFilter();
            $this->Imagine->userModel = 'UserModel';
        }

        public function redirect($url, $status = null, $exit = true): void
        {
            $this->redirectUrl = $url;
        }
    }
}

/**
 * Imagine Component Test
 *
 * @package Imagine
 * @subpackage Imagine.tests.cases.components
 */
class ImagineComponentTest extends CakeTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.Imagine.Image',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Configure::write('Imagine.salt', 'this-is-a-nice-salt');
        $request = new CakeRequest(null, false);
        $this->Controller = new ImagineImagesTestController($request, $this->getMock('CakeResponse'));
        $this->Controller->constructClasses();
        $this->Controller->Components->init($this->Controller);
        $this->Controller->Imagine->Controller = $this->Controller;
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->Controller);
        ClassRegistry::flush();
    }

    /**
     * testGetHash method
     *
     * @return void
     */
    public function testGetHash(): void
    {
        $this->Controller->request->params['named'] = [
            'thumbnail' => 'width|200;height|150'];
        $hash = $this->Controller->Imagine->getHash();
        $this->assertTrue(is_string($hash));
    }

    /**
     * testCheckHash method
     *
     * @return void
     */
    public function testCheckHash(): void
    {
        $this->Controller->request->params['named'] = [
            'thumbnail' => 'width|200;height|150',
            'hash' => '69aa9f46cdc5a200dc7539fc10eec00f2ba89023',
        ];
        $result = $this->Controller->Imagine->checkHash();
        $this->assertTrue($result);
    }

    /**
     * testInvalidHash
     */
    public function testInvalidHash(): void
    {
        $this->expectException(NotFoundException::class);
        $this->Controller->request->params['named'] = [
            'thumbnail' => 'width|200;height|150',
            'hash' => 'wrong-hash-value',
        ];
        $this->Controller->Imagine->checkHash();
    }

    /**
     * testMissingHash
     */
    public function testMissingHash(): void
    {
        $this->expectException(NotFoundException::class);
        $this->Controller->request->params['named'] = [
            'thumbnail' => 'width|200;height|150'];
        $this->Controller->Imagine->checkHash();
    }

    /**
     * testCheckHash method
     *
     * @return void
     */
    public function testUnpackParams(): void
    {
        $this->assertEquals($this->Controller->Imagine->operations, []);
        $this->Controller->request->params['named']['thumbnail'] = 'width|200;height|150';
        $this->Controller->Imagine->unpackParams();
        $this->assertEquals($this->Controller->Imagine->operations, [
            'thumbnail' => [
                'width' => 200,
                'height' => 150,
            ],
        ]);
    }
}
