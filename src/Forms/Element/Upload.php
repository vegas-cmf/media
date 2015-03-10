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
namespace Vegas\Forms\Element;

use \Phalcon\Forms\Element;

class Upload extends Element
{
    const BROWSER_BUTTON = 'button';
    const BROWSER_DROPZONE = 'dropzone';

    private $uploadUrl = null;
    private $browserLabel = null;
    private $browserType = null;
    private $name = null;
    private $maxFiles = null;
    private $maxFileSize = null;
    private $minFileSize = null;
    private $allowedExtensions = null;
    private $forbiddenExtensions = null;
    private $allowedMimeTypes = null;
    private $forbiddenMimeTypes = null;

    public function __construct($name)
    {
        $this->name = $name;

        $this->maxFiles = 1;
        $this->uploadUrl = '/upload';
        $this->minFileSize = '1B';
        $this->maxFileSize = '10MB';
        $this->browserLabel = 'Select file';
        $this->browserType = self::BROWSER_BUTTON;
        $this->allowedExtensions = [];
        $this->forbiddenExtensions = [];
        $this->allowedMimeTypes = [];
        $this->forbiddenMimeTypes = [];
    }

    public function setUploadUrl($uploadUrl = '/upload')
    {
        $this->uploadUrl = $uploadUrl;
        return $this;
    }

    public function setBrowserLabel($browserLabel = 'Select file')
    {
        $this->browserLabel = $browserLabel;
        return $this;
    }

    public function setBrowserType($browserType = self::BROWSER_BUTTON)
    {
        $this->browserType = $browserType;
        return $this;
    }

    public function setMaxFiles($maxFiles = 1)
    {
        $this->maxFiles = $maxFiles;
        return $this;
    }

    public function setMinFileSize($minFileSize = '1B')
    {
        $this->minFileSize = $minFileSize;
        return $this;
    }

    public function maxFileSize($maxFileSize = '10MB')
    {
        $this->maxFileSize = $maxFileSize;
        return $this;
    }

    public function setAllowedExtensions(array $allowedExtensions = [])
    {
        $this->allowedExtensions = $allowedExtensions;
        return $this;
    }

    public function setForbiddenExtensions(array $forbiddenExtensions = [])
    {
        $this->forbiddenExtensions = $forbiddenExtensions;
        return $this;
    }

    public function setAllowedMimeTypes(array $allowedMimeTypes = [])
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
        return $this;
    }

    public function setForbiddenMimeTypes(array $forbiddenMimeTypes = [])
    {
        $this->forbiddenMimeTypes = $forbiddenMimeTypes;
        return $this;
    }

    public function render($attributes = array())
    {
        $fileElement = new Element\File($this->name);
        $fileElement->setAttribute('vegas-cmf', 'upload');
        $fileElement->setAttribute('max-files', $this->maxFiles);
        $fileElement->setAttribute('upload-url', $this->uploadUrl);
        $fileElement->setAttribute('min-file-size', $this->minFileSize);
        $fileElement->setAttribute('max-file-size', $this->maxFileSize);
        $fileElement->setAttribute('browser-type', $this->browserType);
        $fileElement->setAttribute('browser-label', $this->browserLabel);
        $fileElement->setAttribute('allowed-extensions', implode(',', $this->allowedExtensions));
        $fileElement->setAttribute('forbidden-extensions', implode(',', $this->forbiddenExtensions));
        $fileElement->setAttribute('allowed-mime-types', implode(',', $this->allowedMimeTypes));
        $fileElement->setAttribute('forbidden-mime-types', implode(',', $this->forbiddenMimeTypes));
        return $fileElement->render($attributes);
    }
}