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

use Vegas\Media\Swf;

class SwfTest extends \PHPUnit_Framework_TestCase
{
    public function testNoSource()
    {
        $swf = new Swf();

        try {
            $swf->renderEmbed();
        } catch (\Exception $exception) {
            $this->assertInstanceOf('\Vegas\Media\Swf\Exception\InvalidSourceExtensionException', $exception);
        }
    }

    public function testIsValid()
    {
        $method = new \ReflectionMethod('\Vegas\Media\Swf', 'isValid');

        $method->setAccessible(true);

        $swf = new Swf();
        $swf->setSource('vegas-cmf.swf');

        $this->assertNotEmpty($swf->getSource());
        $this->assertTrue($method->invoke($swf, $swf->getSource()));
    }

    public function testEmbedDecorator()
    {
        $swf = new Swf();
        $decorator = $swf->getEmbedDecorator();

        $this->assertNotEmpty($decorator);
        $this->assertContains('src=', $decorator);
        $this->assertContains('width', $decorator);
        $this->assertContains('height', $decorator);
        $this->assertContains('<embed', $decorator);
        $this->assertContains('</embed>', $decorator);
    }

    public function testObjectDecorator()
    {
        $swf = new Swf();
        $decorator = $swf->getObjectDecorator();

        $this->assertNotEmpty($decorator);
        $this->assertContains('data=', $decorator);
        $this->assertContains('width', $decorator);
        $this->assertContains('height', $decorator);
        $this->assertContains('<object', $decorator);
        $this->assertContains('</object>', $decorator);
    }
}