<?php
/**
 * This file is part of Vegas package
 *
 * @author Adrian Malik <adrian.malik.89@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VegasTest\Forms\Element;

use Vegas\Forms\Element\Upload;

class UploadTest extends \PHPUnit_Framework_TestCase
{
    public function testNullParams()
    {
        $name = 'upload';
        $upload = new Upload($name);
        $this->assertNotnull($upload->getPath());
        $this->assertNotnull($upload->getName());
        $this->assertNotnull($upload->getMaxFiles());
        $this->assertNotnull($upload->getUploadUrl());
        $this->assertNotnull($upload->getMinFileSize());
        $this->assertNotnull($upload->getMaxFileSize());
        $this->assertNotnull($upload->getBrowserLabel());
        $this->assertNotnull($upload->getBrowserType());
        $this->assertNotnull($upload->getAllowedExtensions());
        $this->assertNotnull($upload->getForbiddenExtensions());
        $this->assertNotnull($upload->getAllowedMimeTypes());
        $this->assertNotnull($upload->getForbiddenMimeTypes());
    }

    public function testEmptyParams()
    {
        $name = 'upload';
        $upload = new Upload($name);
        $this->assertEmpty($upload->getAllowedExtensions());
        $this->assertEmpty($upload->getForbiddenExtensions());
        $this->assertEmpty($upload->getAllowedMimeTypes());
        $this->assertEmpty($upload->getForbiddenMimeTypes());
    }

    public function testNotEmptyParams()
    {
        $name = 'upload';
        $upload = new Upload($name);
        $this->assertNotEmpty($upload->getPath());
        $this->assertNotEmpty($upload->getName());
        $this->assertNotEmpty($upload->getMaxFiles());
        $this->assertNotEmpty($upload->getUploadUrl());
        $this->assertNotEmpty($upload->getMinFileSize());
        $this->assertNotEmpty($upload->getMaxFileSize());
        $this->assertNotEmpty($upload->getBrowserLabel());
        $this->assertNotEmpty($upload->getBrowserType());
    }

    public function testDefaultParams()
    {
        $name = 'upload';
        $upload = new Upload($name);
        $this->assertEquals($upload->getPath(), 'files/');
        $this->assertEquals($upload->getName(), $name);
        $this->assertEquals($upload->getMaxFiles(), 1);
        $this->assertEquals($upload->getUploadUrl(), '/upload');
        $this->assertEquals($upload->getMinFileSize(), '1B');
        $this->assertEquals($upload->getMaxFileSize(), '10MB');
        $this->assertEquals($upload->getBrowserLabel(), 'Select file');
        $this->assertEquals($upload->getBrowserType(), Upload::BROWSER_BUTTON);
        $this->assertEquals($upload->getAllowedExtensions(), []);
        $this->assertEquals($upload->getForbiddenExtensions(), []);
        $this->assertEquals($upload->getAllowedMimeTypes(), []);
        $this->assertEquals($upload->getForbiddenMimeTypes(), []);
    }

    public function testSetModel()
    {
        $name = 'upload';
        $model = new \stdClass();
        $upload = new Upload($name);
        $upload->setModel($model);
        $this->assertEquals($upload->getModel(), $model);
    }

    public function testSetPath()
    {
        $name = 'upload';
        $path = 'files/upload/';
        $upload = new Upload($name);
        $upload->setPath($path);
        $this->assertEquals($upload->getPath(), $path);
    }

    public function testSetName()
    {
        $name = 'upload';
        $upload = new Upload($name);
        $this->assertEquals($upload->getName(), $name);
    }

    public function testSetMaxFiles()
    {
        $name = 'upload';
        $maxFiles = 44;
        $upload = new Upload($name);
        $upload->setMaxFiles($maxFiles);
        $this->assertEquals($upload->getMaxFiles(), $maxFiles);
    }

    public function testSetUploadUrl()
    {
        $name = 'upload';
        $uploadUrl = '/upload/somewhere';
        $upload = new Upload($name);
        $upload->setUploadUrl($uploadUrl);
        $this->assertEquals($upload->getUploadUrl(), $uploadUrl);
    }

    public function testSetMinFileSize()
    {
        $name = 'upload';
        $minFileSize = '1KB';
        $upload = new Upload($name);
        $upload->setMinFileSize($minFileSize);
        $this->assertEquals($upload->getMinFileSize(), $minFileSize);
    }

    public function testSetMaxFileSize()
    {
        $name = 'upload';
        $maxFileSize = '20MB';
        $upload = new Upload($name);
        $upload->setMaxFileSize($maxFileSize);
        $this->assertEquals($upload->getMaxFileSize(), $maxFileSize);
    }

    public function testSetBrowserLabel()
    {
        $name = 'upload';
        $browserLabel = 'Select';
        $upload = new Upload($name);
        $upload->setBrowserLabel($browserLabel);
        $this->assertEquals($upload->getBrowserLabel(), $browserLabel);
    }

    public function testSetBrowserType()
    {
        $name = 'upload';
        $upload = new Upload($name);
        $upload->setBrowserType(Upload::BROWSER_DROPZONE);
        $this->assertEquals($upload->getBrowserType(), Upload::BROWSER_DROPZONE);
    }

    public function testSetAllowedExtensions()
    {
        $name = 'upload';
        $allowedExtensions = ['jpg'];
        $upload = new Upload($name);
        $upload->setAllowedExtensions($allowedExtensions);
        $this->assertEquals($upload->getAllowedExtensions(), $allowedExtensions);
    }

    public function testSetForbiddenExtensions()
    {
        $name = 'upload';
        $forbiddenExtensions = ['pdf'];
        $upload = new Upload($name);
        $upload->setForbiddenExtensions($forbiddenExtensions);
        $this->assertEquals($upload->getForbiddenExtensions(), $forbiddenExtensions);
    }

    public function testSetAllowedMimeTypes()
    {
        $name = 'upload';
        $allowedMimeTypes = ['image/jpg'];
        $upload = new Upload($name);
        $upload->setAllowedMimeTypes($allowedMimeTypes);
        $this->assertEquals($upload->getAllowedMimeTypes(), $allowedMimeTypes);
    }

    public function testSetForbiddenMimeTypes()
    {
        $name = 'upload';
        $forbiddenMimeTypes = ['application/pdf'];
        $upload = new Upload($name);
        $upload->setForbiddenMimeTypes($forbiddenMimeTypes);
        $this->assertEquals($upload->getForbiddenMimeTypes(), $forbiddenMimeTypes);
    }

    public function testRender()
    {
        $name = 'upload';
        $upload = new Upload($name);
        $output = $upload->render();
        $this->assertEquals($output, '<input type="file" id="upload" name="upload" vegas-cmf="upload" max-files="1" upload-url="/upload" min-file-size="1B" max-file-size="10MB" browser-type="button" browser-label="Select file" allowed-extensions="" forbidden-extensions="" allowed-mime-types="" forbidden-mime-types="" />');
    }
}