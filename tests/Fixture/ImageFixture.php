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

class ImageFixture extends CakeTestFixture
{
    /**
     * name property
     *
     * @var string
     */
    public $name = 'Image';

    /**
     * fields property
     *
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'integer', 'key' => 'primary'],
        'title' => ['type' => 'string', 'null' => false]];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        ['title' => 'First Image'],
        ['title' => 'Second Image']];
}
