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

use Vegas\Db\Decorator\CollectionAbstract;
use Vegas\Media\Db\FileInterface;
use Vegas\Media\Uploader;

use Vegas\Db\MappingManager;

class File extends CollectionAbstract implements FileInterface
{
    public function getSource()
    {
        return 'vegas_files';
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getName()
    {
        return $this->original_destination.'/'.$this->name;
    }

    public function getTempName()
    {
        return  $this->temp_destination.'/'.$this->temp_name;
    }

    public function getType()
    {
        return $this->type;
    }
}


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

class PhalconFile extends \Phalcon\Http\Request\File
{
    public $testDestination = [];

    public function moveTo($destination)
    {
        //leave the test file for next test
        copy($this->getTempName(),$destination);

        $this->testDestination[] = $destination;
    }

    //delete files created for test
    function __destruct()
    {
        foreach($this->testDestination as $filePath) unlink($filePath);
    }
}

class UploaderTest extends \PHPUnit_Framework_TestCase
{
    protected function mockFile($isTemp = true)
    {
        $file = new File();
        $file->name = 'test.png';
        $file->is_temp = $isTemp;
        $file->temp_name = 'test.png';
        $file->temp_destination = './tests/fixtures/public/tmp';
        $file->original_destination = './tests/fixtures/public/origin';

        $file->save();

        return $file;
    }

    protected function mockUploadedFile()
    {
        $files = array( 'name' => 'test.png', 'tmp_name' => './tests/fixtures/public/tmp/test.png', 'type' => 'image/png', 'size' => 42, 'error' => 0  );
        $file = new PhalconFile($files);

        return $file;
    }

    protected function mockFooRecord(File $file)
    {
        $foo = new Foo();
        $foo->image = array(array('file_id' => (string)$file->getId()));
        $foo->width = 800;
        $foo->height = 700;
        $foo->title = 'Test file';
        $foo->save();

        return $foo;
    }

    public function testSetFiles()
    {
        try {
            $files = array();

            $uploader = new Uploader(new File());

            $uploader->setFiles($files);
            $uploader->handle();
        } catch(\Exception $exception) {

            $this->assertInstanceOf('\Vegas\Media\Uploader\Exception\NoFilesException', $exception);
        }
    }

    public function testSetMimeTypes()
    {
        try {
            $files = array();

            $uploader = new Uploader(new File());

            $uploader->setMimeTypes(['image/jpeg', 'image/png']);
            $uploader->handle();
        } catch(\Exception $exception) {

            $this->assertInstanceOf('\Vegas\Media\Uploader\Exception\NoFilesException', $exception);
        }
    }

    public function testSetOriginalDestination()
    {
        try {
            $files = array();

            $uploader = new Uploader(new File());

            $uploader->setOriginalDestination('/origin');
            $uploader->handle();
        } catch(\Exception $exception) {

            $this->assertInstanceOf('\Vegas\Media\Uploader\Exception\NoFilesException', $exception);
        }
    }

    public function testSetMaxFileSize()
    {
        try {
            $files = array();

            $uploader = new Uploader(new File());

            $uploader->setMaxFileSize('10MB');
            $uploader->handle();
        } catch(\Exception $exception) {

            $this->assertInstanceOf('\Vegas\Media\Uploader\Exception\NoFilesException', $exception);
        }
    }

    public function testSetTempDestination()
    {
        try {
            $files = array();

            $uploader = new Uploader(new File());

            $uploader->setTempDestination('/tmp');
            $uploader->handle();
        } catch(\Exception $exception) {

            $this->assertInstanceOf('\Vegas\Media\Uploader\Exception\NoFilesException', $exception);
        }
    }

    public function testFilterMaxSize()
    {
        $method = new \ReflectionMethod('\Vegas\Media\Uploader', 'filterMaxSize');

        $method->setAccessible(true);

        $this->assertEquals($method->invoke(new Uploader(new File()), '100'), 100);
        $this->assertEquals($method->invoke(new Uploader(new File()), '1KB'), 1024 * 1);
        $this->assertEquals($method->invoke(new Uploader(new File()), '1MB'), 1024 * 1024 * 1);
        $this->assertEquals($method->invoke(new Uploader(new File()), '1GB'), 1024 * 1024 * 1024 * 1);
        $this->assertEquals($method->invoke(new Uploader(new File()), '1TB'), 1024 * 1024 * 1024 * 1024 * 1);
        $this->assertEquals($method->invoke(new Uploader(new File()), '1B'), 1);

        try {
            $method->invoke(new Uploader(new File()), '2FB');
        } catch (\Exception $exception) {
            $this->assertInstanceOf('\Vegas\Media\Uploader\Exception\InvalidMaxSizeException', $exception);
        }
    }

    public function testSetExtensions()
    {
        $inputExtensions = array('jpg', 'png');
        $uploader = new Uploader(new File());
        $uploader->setExtensions($inputExtensions);

        $reflectionProperty = new \ReflectionProperty('\Vegas\Media\Uploader', 'extensions');
        $reflectionProperty->setAccessible(true);
        $outputExtensions = $reflectionProperty->getValue($uploader);

        $this->assertTrue($inputExtensions == $outputExtensions);
    }

    public function testHandle()
    {
        $mappingManager = new MappingManager();
        $fileMapper = new \Vegas\Media\Db\Mapping\File(new File());

        $mappingManager->add($fileMapper);

        $fooFile = $this->mockFile(true);

        $testFile = $this->mockUploadedFile();

        $uploader = new Uploader($fooFile);
        $uploader->setOriginalDestination('./tests/fixtures/public/tmp');
        $uploader->setTempDestination('./tests/fixtures/public/tmp');
        $uploader->setFiles([1=>$testFile]);

        $resultFiles = $uploader->handle();

        $file = File::findById($resultFiles[0]['file_id']);
        $this->assertEquals($testFile->getName(),$file->name);
    }

    public function testFileMimeContentType()
    {
        $reflection_class = new \ReflectionClass("Vegas\Media\Uploader");
        $fileMimeContentType = $reflection_class->getMethod("fileMimeContentType");
        $fileMimeContentType->setAccessible(true);

        $fooFile = $this->mockFile(true);

        $testFile = $this->mockUploadedFile();

        $uploader = new Uploader($fooFile);
        $uploader->setOriginalDestination('./tests/fixtures/public/tmp');
        $uploader->setTempDestination('./tests/fixtures/public/tmp');
        $uploader->setFiles([1=>$testFile]);

        $this->assertEquals( $testFile->getType(), $fileMimeContentType->invoke($uploader,$testFile->getTempName()) );
    }

    public static function tearDownAfterClass()
    {
        foreach (File::find() as $file) { $file->delete(); }
        foreach (Foo::find() as $foo) { $foo->delete(); }
    }
}