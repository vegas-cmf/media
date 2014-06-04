<?php
/**
 * This file is part of Vegas package
 *
 * @author Adrian Malik <adrian.malik@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VegasTest;

use Vegas\Media\Uploader;

class UploaderTest extends \PHPUnit_Framework_TestCase
{
    public function testNoFiles()
    {
        try {
            $files = array();

            $uploader = new Uploader();

            $uploader->setFiles($files);
            $uploader->handle();
        } catch(\Exception $exception) {

            $this->assertInstanceOf('\Vegas\Media\Uploader\Exception\NoFilesException', $exception);
        }
    }

    public function testFilterMaxSize()
    {
        $method = new \ReflectionMethod('\Vegas\Media\Uploader', 'filterMaxSize');

        $method->setAccessible(true);

        $this->assertEquals($method->invoke(new Uploader(), '100'), 100);
        $this->assertEquals($method->invoke(new Uploader(), '1KB'), 1024 * 1);
        $this->assertEquals($method->invoke(new Uploader(), '1MB'), 1024 * 1024 * 1);
        $this->assertEquals($method->invoke(new Uploader(), '1GB'), 1024 * 1024 * 1024 * 1);
        $this->assertEquals($method->invoke(new Uploader(), '1TB'), 1024 * 1024 * 1024 * 1024 * 1);
        $this->assertEquals($method->invoke(new Uploader(), '1B'), 1);

        try {
            $method->invoke(new Uploader(), '2FB');
        } catch (\Exception $exception) {
            $this->assertInstanceOf('\Vegas\Media\Uploader\Exception\InvalidMaxSizeException', $exception);
        }
    }
}