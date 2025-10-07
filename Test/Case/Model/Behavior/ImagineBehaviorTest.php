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

App::uses('Model', 'Model');
App::uses('Security', 'Utility');

class ImagineTestModel extends Model
{
    public $name = 'ImagineTestModel';
    public $useTable = false;
}

class ImagineBehaviorTest extends CakeTestCase
{
    /**
     * Holds the instance of the model
     *
     * @var mixed
     */
    public $Article = null;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [];

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->Model = ClassRegistry::init('ImagineTestModel');
        $this->Model->Behaviors->load('Imagine.Imagine');
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Model);
        ClassRegistry::flush();
    }

    /**
     * testImagineObject
     *
     * @return void
     */
    public function testImagineObject(): void
    {
        $result = $this->Model->imagineObject();
        $this->assertTrue(is_a($result, 'Imagine\Gd\Imagine'));
    }

    /**
     * testParamsAsFileString
     *
     * @return void
     */
    public function testOperationsToString(): void
    {
        $operations = [
            'thumbnail' => [
                'width' => 200,
                'height' => 150]];
        $result = $this->Model->operationsToString($operations);
        $this->assertEquals($result, '.thumbnail+width-200+height-150');
    }

    /**
     * getImageSize
     *
     * @return void
     */
    public function getImageSize(): void
    {
        $image = CakePlugin::path('Imagine') . 'Test' . DS . 'Fixture' . DS . 'cake.icon.png';
        $result = $this->Model->getImageSize($image);
        $this->assertEquals($result, [20, 20]);
    }

    /**
     * testCropInvalidArgumentException
     *
     * @return void
     */
    public function testCropInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $image = CakePlugin::path('Imagine') . 'Test' . DS . 'Fixture' . DS . 'titus.jpg';
        $this->Model->processImage($image, TMP . 'crop.jpg', [], [
            'crop' => []]);
    }

    /**
     * testCrop
     *
     * @return void
     */
    public function testCrop(): void
    {
        $image = CakePlugin::path('Imagine') . 'Test' . DS . 'Fixture' . DS . 'titus.jpg';
        $this->Model->processImage($image, TMP . 'crop.jpg', [], [
            'crop' => [
                'height' => 300,
                'width' => 300]]);

        $result = $this->Model->getImageSize(TMP . 'crop.jpg');
        $this->assertEquals(
            $result,
            [300, 300, 'x' => 300, 'y' => 300],
        );
    }

    /**
     * testThumbnail
     *
     * @return void
     */
    public function testThumbnail(): void
    {
        $image = CakePlugin::path('Imagine') . 'Test' . DS . 'Fixture' . DS . 'titus.jpg';
        $this->Model->processImage($image, TMP . 'thumbnail.jpg', [], [
            'thumbnail' => [
                'mode' => 'outbound',
                'height' => 300,
                'width' => 300]]);

        $result = $this->Model->getImageSize(TMP . 'thumbnail.jpg');
        $this->assertEquals(
            $result,
            [300, 300, 'x' => 300, 'y' => 300],
        );

        $this->Model->processImage($image, TMP . 'thumbnail2.jpg', [], [
            'thumbnail' => [
                'mode' => 'inset',
                'height' => 300,
                'width' => 300]]);

        $result = $this->Model->getImageSize(TMP . 'thumbnail2.jpg');
        $this->assertEquals(
            $result,
            [226, 300, 'x' => 226, 'y' => 300],
        );
    }

    public function testSquareCenterCrop(): void
    {
        $image = CakePlugin::path('Imagine') . 'Test' . DS . 'Fixture' . DS . 'titus.jpg';
        $this->Model->processImage($image, TMP . 'testSquareCenterCrop.jpg', [], [
            'squareCenterCrop' => [
                'size' => 255]]);

        $result = $this->Model->getImageSize(TMP . 'testSquareCenterCrop.jpg');
        $this->assertEquals(
            $result,
            [255, 255, 'x' => 255, 'y' => 255],
        );
    }

    /**
     * testgetImageSize
     *
     * @return void
     */
    public function testgetImageSize(): void
    {
        $image = CakePlugin::path('Imagine') . 'Test' . DS . 'Fixture' . DS . 'titus.jpg';
        $result = $this->Model->getImageSize($image);
        $this->assertEquals(
            $result,
            [500, 664, 'x' => 500, 'y' => 664],
        );
    }

    /**
     * testWidenAndHeighten
     *
     * @return void
     */
    public function testWidenAndHeighten(): void
    {
        $image = CakePlugin::path('Imagine') . 'Test' . DS . 'Fixture' . DS . 'titus.jpg';

        $result = $this->Model->getImageSize($image);
        $this->assertEquals(
            $result,
            [500, 664, 'x' => 500, 'y' => 664],
        );

        // Width
        $this->Model->processImage($image, TMP . 'thumbnail2.jpg', [], [
            'widen' => [
                'size' => 200]]);

        $result = $this->Model->getImageSize(TMP . 'thumbnail2.jpg');
        $this->assertEquals(
            $result,
            [200, 266, 'x' => 200, 'y' => 266],
        );

        // Height
        $this->Model->processImage($image, TMP . 'thumbnail3.jpg', [], [
            'heighten' => [
                'size' => 200]]);

        $result = $this->Model->getImageSize(TMP . 'thumbnail3.jpg');
        $this->assertEquals(
            $result,
            [151, 200, 'x' => 151, 'y' => 200],
        );
    }

    /**
     * testScale
     *
     * @return void
     */
    public function testScale(): void
    {
        $image = CakePlugin::path('Imagine') . 'Test' . DS . 'Fixture' . DS . 'titus.jpg';

        // Scale
        $this->Model->processImage($image, TMP . 'thumbnail4.jpg', [], [
            'scale' => [
                'factor' => 2]]);

        $result = $this->Model->getImageSize(TMP . 'thumbnail4.jpg');
        $this->assertEquals(
            $result,
            [1000, 1328, 'x' => 1000, 'y' => 1328],
        );

        // Scale2
        $this->Model->processImage($image, TMP . 'thumbnail5.jpg', [], [
            'scale' => [
                'factor' => 1.25]]);

        $result = $this->Model->getImageSize(TMP . 'thumbnail5.jpg');
        $this->assertEquals(
            $result,
            [625, 830, 'x' => 625, 'y' => 830],
        );
    }

    /**
     * testPreventUpscale
     *
     * @return void
     */
    public function testPreventUpscale(): void
    {
        $image = CakePlugin::path('Imagine') . 'Test' . DS . 'Fixture' . DS . 'titus.jpg';

        // Height
        $this->Model->processImage($image, TMP . 'heighten-upscale.jpg', [], [
                'heighten' => [
                    'size' => 2000,
                    'preventUpscale' => true,
                ],
            ],);

        $result = $this->Model->getImageSize(TMP . 'heighten-upscale.jpg');
        $this->assertEquals($result, [500, 664, 'x' => 500, 'y' => 664]);

        // Width
        $this->Model->processImage($image, TMP . 'widen-upscale.jpg', [], [
            'widen' => [
                'size' => 2000,
                'preventUpscale' => true,
            ],
        ]);

        $result = $this->Model->getImageSize(TMP . 'widen-upscale.jpg');
        $this->assertEquals($result, [500, 664, 'x' => 500, 'y' => 664]);

        // Thumbnail
        $this->Model->processImage($image, TMP . 'thumbnail-upscale.jpg', [], [
            'thumbnail' => [
                'height' => 2000,
                'width' => 2000,
                'preventUpscale' => true,
            ],
        ]);

        $result = $this->Model->getImageSize(TMP . 'thumbnail-upscale.jpg');
        $this->assertEquals($result, [500, 664, 'x' => 500, 'y' => 664]);
    }
}
