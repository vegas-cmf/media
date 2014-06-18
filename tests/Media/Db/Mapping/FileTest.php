<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace VegasTest\Db\Mapping;

use Vegas\Db\Decorator\CollectionAbstract;
use Vegas\Db\MappingManager;
use Vegas\Media\Model\File;

class Foo extends CollectionAbstract
{
    public function getSource()
    {
        return 'foo_media_files';
    }

    protected $mappings = array(
        'image' =>  'file'
    );
}


class FileTest extends \PHPUnit_Framework_TestCase
{
    protected function mockFile($isTemp = false)
    {
        $file = new File();
        $file->name = 'origin.jpg';
        $file->is_temp = $isTemp;
        $file->temp_name = 'temp.jpg';
        $file->temp_destination = '/tmp';
        $file->original_destination = '/origin';
        $file->save();

        return $file;
    }

    protected function mockFooRecord(File $file)
    {
        $foo = new Foo();
        $foo->image = array((string)$file->getId());
        $foo->width = 800;
        $foo->height = 700;
        $foo->title = 'Lorem ipsum';
        $foo->save();

        return $foo;
    }

    public function testMapper()
    {
        foreach (File::find() as $file) { $file->delete(); }
        foreach (Foo::find() as $foo) { $foo->delete(); }

        $mappingManager = new MappingManager();
        $mappingManager->add(new \Vegas\Media\Db\Mapping\File());

        $file = $this->mockFile(false);
        $this->mockFooRecord($file);

        $foo = Foo::findFirst();

        $this->assertInstanceOf('\ArrayObject', $foo->readMapped('image'));
        $this->assertCount(1, $foo->readMapped('image'));
        $this->assertInternalType('array', $foo->readMapped('image')->getArrayCopy());
        $this->assertInstanceOf('\Vegas\Media\File\Decorator', $foo->readMapped('image')[0]);
        $this->assertInstanceOf('\Vegas\Media\File\Decorator', $foo->readMapped('image')->offsetGet(0));
        $this->assertEquals('origin.jpg', $foo->readMapped('image')[0]->getRecord()->name);
        $this->assertTrue($foo->readMapped('image')[0]->save());
        $this->assertEquals($file->getId(), $foo->readMapped('image')[0]->getRecord()->getId());
        $this->assertEquals('/origin/temp.jpg', $foo->readMapped('image')[0]->getPath());
        $this->assertEquals('/origin/temp.jpg', $foo->readMapped('image')[0]->getUrl());
        $this->assertInstanceOf('\SplFileInfo', $foo->readMapped('image')[0]->getFileInfo());
        $this->assertEquals('/origin/thumbnails/100_100_temp.jpg', $foo->readMapped('image')[0]->getThumbnailUrl(100,100));
        $this->assertEquals('/origin/thumbnails/100_100_temp.jpg', $foo->readMapped('image')[0]->getThumbnailPath(100,100));
        $this->assertTrue($foo->readMapped('image')[0]->delete());
        $this->assertCount(0, $foo->readMapped('image'));
    }

} 