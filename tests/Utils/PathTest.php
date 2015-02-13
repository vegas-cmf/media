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

use Vegas\Utils\Path;

class PathTest extends \PHPUnit_Framework_TestCase
{
    public function testRootPath()
    {
        $this->assertEquals('/', Path::getRelativePath(Path::getRootPath()));
    }

    public function testPublicPath()
    {
        $this->assertEquals('', Path::getRelativePath(Path::getPublicPath()));
    }

    public function testLibPath()
    {
        $this->assertEquals('/lib', Path::getRelativePath(Path::getLibPath()));
    }

    public function testAppPath()
    {
        $this->assertEquals('/app', Path::getRelativePath(Path::getAppPath()));
    }

    public function testConfigPath()
    {
        $this->assertEquals('/config', Path::getRelativePath(Path::getConfigPath()));
    }

    public function testTempPath()
    {
        $this->assertEquals('/temp', Path::getRelativePath(Path::getTempPath()));
    }

    public function testTestsPath()
    {
        $this->assertEquals('/tests', Path::getRelativePath(Path::getTestsPath()));
    }

    public function testWrongRelativePathParam()
    {
        try {
            $this->assertEquals('/tests', Path::getRelativePath(array()));
        } catch(\Exception $exception) {
            $this->assertInstanceOf('\Vegas\Utils\Path\Exception\InvalidPathException', $exception);
        }
    }

    public function testWrongFileDirectoryParam()
    {
        try {
            $this->assertEquals('/tests', Path::getFileDirectory(array()));
        } catch(\Exception $exception) {
            $this->assertInstanceOf('\Vegas\Utils\Path\Exception\InvalidPathException', $exception);
        }
    }
}