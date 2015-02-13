<?php
/**
 * This file is part of Vegas package
 *
 * @author Tomasz Borodziuk <tomasz.borodziuk@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace VegasTest\Media;

use Vegas\Db\Decorator\CollectionAbstract;
use Vegas\Db\MappingManager;
use Vegas\Media\Db\FileInterface;

use Vegas\Media\Helper As MediaHelper;

class File extends CollectionAbstract implements FileInterface
{
    public function getSource()
    {
        return 'vegas_files';
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


class HelperTest extends \PHPUnit_Framework_TestCase
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

    public function testFileProcessing()
    {
        foreach (File::find() as $file) { $file->delete(); }
        foreach (Foo::find() as $foo) { $foo->delete(); }

        $mappingManager = new MappingManager();
        $fileMapper = new \Vegas\Media\Db\Mapping\File(new File());

        $mappingManager->add($fileMapper);

        $fooFile = $this->mockFile(true);

        $this->mockFooRecord($fooFile);

        $foo = Foo::findFirst();

        $mappedRecord = $foo->readMapped('image');

        MediaHelper::moveFilesFrom($mappedRecord);
        MediaHelper::generateThumbnailsFrom((array) $mappedRecord, ['width' => 600, 'height' => 300]);

        foreach($mappedRecord as $file) {
            $testFile = $file->getRecord();

            $this->assertTrue(!($testFile->is_temp));
            $this->assertTrue(file_exists($testFile->original_destination . '/' . $testFile->temp_name));
            $this->assertTrue(file_exists($testFile->original_destination . '/thumbnails/' . '600_300_'.$testFile->temp_name));

            //delete created thumbnails for next test
            unlink($testFile->original_destination . '/thumbnails/' . '600_300_'.$testFile->temp_name);

            //undo file moving the file for next test
            rename(
                $testFile->original_destination . '/' . $testFile->temp_name,
                $testFile->temp_destination . '/' . $testFile->temp_name
            );
        }
    }
} 