<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace VegasTest;

use Vegas\Media\File\Wrapper;
use Vegas\Media\Model\File;

class WrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testWrapRecord()
    {
        $wrapper = new Wrapper();
        
        $file = $this->prepareFile();
        
        $extraData = array('test' => array('a','b'));
        
        $wrappedFile = $wrapper->wrapRecord($file, $extraData);
        
        $this->assertInstanceOf('\Vegas\Media\File\Decorator', $wrappedFile);

        // decorator tests
        $this->assertTrue($wrappedFile->save());

        $this->assertEquals($wrappedFile->getId(), $file->getId());
        $this->assertEquals($wrappedFile->test, $extraData['test']);

        $this->assertEquals('/abcd/efg/'.$file->temp_name, $wrappedFile->getPath());;
        $this->assertEquals('/abcd/efg/'.$file->temp_name, $wrappedFile->getUrl());

        $this->assertEquals('/abcd/efg/thumbnails/100_100_'.$file->temp_name, $wrappedFile->getThumbnailPath(100, 100));
        $this->assertEquals('/abcd/efg/thumbnails/100_100_'.$file->temp_name, $wrappedFile->getThumbnailUrl(100, 100));
        $this->assertEquals('/assets/img/common/icons/filetypes/jpg.png', $wrappedFile->getIcon());

        $this->assertTrue($wrappedFile->delete());
        $this->assertTrue($wrappedFile->delete());
        
        $file = File::findById($wrappedFile->getId());
        $this->assertFalse($file);
    }

    public function testWrapValues()
    {
        $wrapper = new Wrapper();

        $file1 = $this->prepareFile();
        $file1->save();

        $file2 = $this->prepareFile();
        $file2->save();

        $values = array(
            array('file_id' => (string)$file1->getId()),
            array('file_id' => 31238)
        );

        try {
            $wrapper->wrapValues($values);
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\MongoException', $ex);
        }

        $values = array(
            array('file_id' => (string)$file1->getId()),
            array('file_id' => (string)$file2->getId())
        );

        $wrappedFiles = $wrapper->wrapValues($values);

        $this->assertInstanceOf('\Vegas\Media\File\Decorator', $wrappedFiles[0]);
        $this->assertInstanceOf('\Vegas\Media\File\Decorator', $wrappedFiles[1]);

        $this->assertEquals($wrappedFiles[0]->getId(), $file1->getId());
        $this->assertEquals($wrappedFiles[1]->getId(), $file2->getId());

        $this->assertNotEquals($wrappedFiles[0]->getId(), $wrappedFiles[1]->getId());

        $wrappedFiles[0]->delete();
        $wrappedFiles[1]->delete();

        $wrappedFiles = $wrapper->wrapValues($values);

        $this->assertCount(0, $wrappedFiles);
    }

    private function prepareFile()
    {
        $file = new File();
        $file->is_temp = true;
        $file->temp_destination = '/abcd/efg/';
        $file->temp_name = uniqid().'.jpg';

        return $file;
    }
}