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
        
        $file = new File();
        $file->is_temp = true;
        $file->temp_destination = '/abcd/efg/';
        $file->temp_name = 'qwerty.jpg';
        
        $extraData = array('test' => array('a','b'));
        
        $wrappedFile = $wrapper->wrapRecord($file, $extraData);
        
        $this->assertInstanceOf('\Vegas\Media\File\Decorator', $wrappedFile);
        
        $this->assertTrue($wrappedFile->save());
        
        $this->assertEquals($wrappedFile->getId(), $file->getId());
        $this->assertEquals($wrappedFile->test, $extraData['test']);
        
        $this->assertTrue($wrappedFile->delete());
        $this->assertTrue($wrappedFile->delete());
        
        $file = File::findById($wrappedFile->getId());
        $this->assertFalse($file);
    }
}