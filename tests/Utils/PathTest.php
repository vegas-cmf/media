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
    public function testAllPaths()
    {
        $this->assertEquals('/', Path::getRelativePath(Path::getRootPath()));
        $this->assertEquals('/app', Path::getRelativePath(Path::getAppPath()));
        $this->assertEquals('/config', Path::getRelativePath(Path::getConfigPath()));
        $this->assertEquals('/tests/Utils', Path::getRelativePath(Path::getFileDirectory(__FILE__)));
        $this->assertEquals('/tests', Path::getRelativePath(Path::getTestsPath()));
        $this->assertEquals('/temp', Path::getRelativePath(Path::getTempPath()));
        $this->assertEquals('', Path::getRelativePath(Path::getPublicPath()));
        $this->assertEquals('/lib', Path::getRelativePath(Path::getLibPath()));
    }
}