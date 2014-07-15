<?php
/**
 * This file is part of Vegas package
 *
 * @author Adrian Malik <adrian.malik.89@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * @info
 *      1. When you create: $avatar = new MultiUpload('avatar'); it will be single upload
 *      2. When you create $files = new MultiUpload('files[]') it will be multupload
 *      3. setUploadAction is necessary
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Forms\Element;

use \Vegas\Forms\Element\Exception\InvalidAssetsManagerException;
use \Phalcon\Forms\Element\File;

class Upload extends File implements AssetsInjectableInterface
{
    /**
     * Describes single mode of uploading file
     */
    const MODE_SINGLE = 'SINGLE';

    /**
     * Describes multi mode of uploading many files
     */
    const MODE_MULTI = 'MULTI';

    /**
     * Sets auto upload
     *
     * @var null
     */
    private $autoUpload = null;

    /**
     * Sets max file size
     *
     * @var null
     */
    private $maxFileSize = null;

    /**
     * Max files number
     *
     * @var null
     */
    private $maxFiles = null;

    /**
     * Button or Dropzone (trigger type)
     *
     * @var null
     */
    private $triggerType = null;

    /**
     * Mode of upload. (MULTI or SINGLE)
     *
     * @var null
     */
    private $mode = null;

    /**
     * Assets Manager
     *
     * @var null
     */
    private $assets = null;

    /**
     * Upload URL where the upload request is sent to
     *
     * @var null
     */

    private $uploadUrl = null;

    /**
     * Labels of buttons (every upload has at least 3 buttons available: add, upload, cancel)
     *
     * @var null
     */
    private $buttonLabels = null;

    /**
     * Array with sizes of preview images
     *
     * @var array
     */
    private $previewSize = array();

    /**
     * Base elements
     *
     * @var null
     */
    private $baseElements = null;

    /**
     * Allowed upload extensions
     *
     * @var null
     */
    private $extensions = null;

    /**
     * Allowed mime types
     *
     * @var null
     */
    private $mimeTypes = null;

    /**
     * Additional upload attributes. They are used in javascript code
     *
     * @var null
     */
    private $uploadAttributes = null;

    /**
     * Describes if preview items (for example for images) should be displayed when you edit form with upload element.
     *
     * @var bool
     */
    private $renderPreview = true;

    /**
     * Object that represents data model
     *
     * @var
     */
    private $model;

    /**
     * Constructs upload element
     *
     * @param string $name
     * @param null $attributes
     */
    public function __construct($name, $attributes = null)
    {
        if(!empty($this->mode) && $this->mode === self::MODE_MULTI) {
            if(strpos($name, '[]')) {
                $name .= '[]';
            }
        }

        parent::__construct($name, $attributes);
    }

    /**
     * Sets flag that describes if preview  should be rendered
     *
     * @param $renderPreview
     */
    public function setRenderPreview($renderPreview)
    {
        $this->renderPreview = $renderPreview;
    }

    /**
     * Returns the flag that describes if preview  should be rendered
     *
     * @return mixed
     */
    public function getRenderPreview()
    {
        return $this->renderPreview;
    }

    /**
     * Renders upload element using Upload\Renderer
     *
     * @param array $attributes
     * @return string
     */
    public function render($attributes = array())
    {
        $this->addAssets();
        $this->setUploadAttributes();

        $renderer = new Upload\Renderer($this, $attributes);

        return $renderer->run();
    }

    /**
     * Sets size of preview images
     *
     * @param $previewSize
     */
    public function setPreviewSize($previewSize)
    {
        $this->previewSize = $previewSize;
    }

    /**
     * @return array
     */
    public function getPreviewSize()
    {
        return $this->previewSize;
    }
    
    /**
     * Sets mode of the upload (MULTI or SINGLE)
     *
     * @param $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Sets auto upload mode after selecting file
     *
     * @param $autoUpload
     */
    public function setAutoUpload($autoUpload)
    {
        $this->autoUpload = $autoUpload;
    }

    /**
     * Sets button labels (add, cancel, upload)
     *
     * @param array $buttonLabels
     */
    public function setButtonLabels(array $buttonLabels = array())
    {
        $this->buttonLabels = $buttonLabels;
    }

    /**
     * Returns button labels
     *
     * @return null
     */
    public function getButtonLabels()
    {
        return $this->buttonLabels;
    }

    /**
     * Sets base elements which will be rendered under the main preview images
     *
     * @param $baseElements
     */
    public function setBaseElements($baseElements)
    {
        $this->baseElements = $baseElements;
    }

    /**
     * Returns base elements
     *
     * @return null
     */
    public function getBaseElements()
    {
        return $this->baseElements;
    }

    /**
     * Sets data model
     *
     * @param $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Returns data model
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets upload url
     *
     * @param $uploadUrl
     */
    public function setUploadUrl($uploadUrl)
    {
        $this->uploadUrl = $uploadUrl;
    }

    /**
     * Sets extensions
     *
     * @param $extensions
     */
    public function setExtensions($extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * Sets trigger type
     *
     * @param $triggerType
     */
    public function setTriggerType($triggerType)
    {
        $this->triggerType = $triggerType;
    }

    /**
     * Sets max files number
     *
     * @param $maxFiles
     */
    public function setMaxFiles($maxFiles)
    {
        $this->maxFiles = $maxFiles;
    }

    /**
     * Sets allowed mime types
     *
     * @param $mimeTypes
     */
    public function setMimeTypes($mimeTypes)
    {
        $this->mimeTypes = $mimeTypes;
    }

    /**
     * Sets max file size
     *
     * @param $maxFileSize
     */
    public function setMaxFileSize($maxFileSize)
    {
        $this->maxFileSize = $maxFileSize;
    }

    /**
     * Sets special upload attributes used in js
     */
    private function setUploadAttributes()
    {
        $attributes['data-form-element-upload'] = 'true';
        $attributes['data-url'] = $this->uploadUrl;
        $attributes['data-id'] = uniqid();

        if (!empty($this->autoUpload)) {
            $attributes['data-auto-upload'] = $this->autoUpload;
        }

        if(!empty($this->mode) && $this->mode === self::MODE_MULTI) {
            $attributes['multiple'] = 'multiple';
        }

        if(!empty($this->previewSize) && $this->previewSize) {
            $attributes['data-preview-width'] = $this->previewSize['width'];
            $attributes['data-preview-height'] = $this->previewSize['height'];
        }

        if(!empty($this->extensions) && is_array($this->extensions)) {
            $attributes['data-extensions'] = json_encode($this->extensions);
        }

        if(!empty($this->mimeTypes) && is_array($this->mimeTypes)) {
            $attributes['data-mime-types'] = json_encode($this->mimeTypes);
        }

        if(!empty($this->maxFileSize) && $this->maxFileSize) {
            $attributes['data-max-file-size'] = $this->maxFileSize;
        }

        if(!empty($this->triggerType) && $this->triggerType) {
            $attributes['data-trigger-type'] = $this->triggerType;
        } else {
            $attributes['data-trigger-type'] = 'button';
        }

        if(!empty($this->timeout)) {
            $attributes['data-timeout'] = $this->timeout;
        }

        if(!empty($this->maxFiles)) {
            $attributes['data-max-files'] = $this->maxFiles;
        }

        $baseElements = array();
        if(!empty($this->baseElements)) {
            foreach($this->baseElements as $baseElement) {
                $templateId = uniqid();
                $baseElement->setAttribute('data-template-id', $templateId);
                $baseElements[] = array(
                    'templateId' => $baseElement->getAttribute('data-template-id'),
                    'name' => $baseElement->getName()
                );
            }
        }

        if(!empty($baseElements)) {
            $attributes['data-base-elements'] = json_encode($baseElements);
        }


        $this->uploadAttributes = $attributes;
    }

    /**
     * Returns upload attributes
     *
     * @return null
     */
    public function getUploadAttributes()
    {
        return $this->uploadAttributes;
    }

    /**
     * Adds js and css to the website
     *
     * @throws Exception\InvalidAssetsManagerException
     */
    private function addAssets()
    {
        if(!$this->assets) {
            throw new InvalidAssetsManagerException();
        }

        $this->assets->addCss('assets/css/common/upload.css');
        $this->assets->addJs('assets/vendor/jquery-uploader/jquery-uploader.js');
        $this->assets->addJs('assets/js/lib/vegas/ui/upload.js');
    }

    /**
     * Sets assets manager
     *
     * @param \Phalcon\Assets\Manager $assets
     * @return $this
     */
    public function setAssetsManager(\Phalcon\Assets\Manager $assets)
    {
        $this->assets = $assets;

        return $this;
    }

    /**
     * Returns assets manager
     *
     * @return null
     */
    public function getAssetsManager()
    {
        return $this->assets;
    }
}