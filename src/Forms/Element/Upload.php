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
use Vegas\Upload\Attributes;

class Upload extends Element
{
    use Attributes;

    const BROWSER_BUTTON = 'button';
    const BROWSER_DROPZONE = 'dropzone';

    private $model = null;
    private $path = null;
    private $uploadUrl = null;
    private $browserLabel = null;
    private $browserType = null;
    private $name = null;


    public function __construct($name)
    {
        parent::__construct($name);

        $this->path = 'files/';
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

    /**
     * @param $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path = 'files/')
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $uploadUrl Url where uploaded file will be sent
     * @return $this
     */
    public function setUploadUrl($uploadUrl = '/upload')
    {
        $this->uploadUrl = $uploadUrl;
        return $this;
    }

    /**
     * @param string $browserLabel Label on button or drop zone which tells you to select file
     * @return $this
     */
    public function setBrowserLabel($browserLabel = 'Select file')
    {
        $this->browserLabel = $browserLabel;
        return $this;
    }

    /**
     * @param string $browserType It can be button or drop zone (drag & drop)
     * @return $this
     */
    public function setBrowserType($browserType = self::BROWSER_BUTTON)
    {
        $this->browserType = $browserType;
        return $this;
    }

    public function render($attributes = array())
    {
        $attributes = $this->getAttributes();
        $fileElement = new Element\File($this->name);

        foreach ($attributes as $name => $value) {
            $fileElement->setAttribute('data-' . $name, $value);
        }

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