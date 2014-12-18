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

use Phalcon\Forms\Element\File;
use Vegas\Forms\Exception;
use Vegas\Forms\Decorator\DecoratedTrait;
use Vegas\Forms\Decorator;

class Upload extends File implements Decorator\DecoratedInterface
{
    use DecoratedTrait {
        renderDecorated as renderDecoratedDefault;
    }
    
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
        $this->setName($name);

        $templatePath = implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'Upload', 'views', '']);
        $this->setDecorator(new Decorator($templatePath));
    }
    
    /**
     * Render element decorated with specific view/template.
     *
     * @param array|null $attributes
     * @return string
     */
    public function renderDecorated($attributes = null)
    {
        $this->setUploadAttributes();
        return $this->renderDecoratedDefault($attributes);
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
     * Returns upload attributes
     *
     * @return null
     */
    public function getUploadAttribute($key)
    {
        if(isset($this->uploadAttributes[$key])) return $this->uploadAttributes[$key];
        return null;
    }
    
    public function getFileInput()
    {
        $file = new \Phalcon\Forms\Element\File($this->getName());
        $file_attributes = array_merge($this->getAttributes(), $this->getUploadAttributes());
        $file->setAttributes($file_attributes);
        $label = 'Add file';

        $buttonLabels = $this->getButtonLabels();

        if(isset($buttonLabels) && isset($buttonLabels['add'])) {
            $label = $buttonLabels['add'];
        }

        $file->setAttribute('data-button-add-label', $label);
        $file->setAttribute('value', null);
        return $file->render();
    }
    
    public function getPreviewData() {
        $preview_data = array();
        $values = $this->getValue();
        if(!empty($values)) {
            if(is_string($values)) {
                $values = json_decode($values, true);
            }
            if($values == null) $values = array();
            foreach($values as $index => $file) {
                $decorator = $this->getPreviewDecorator($index, $file);
                $data = array();
                $data['index'] = $index;
                $data['file_id'] = $decorator->getId();
                $data['file_basename'] = $decorator->getFileInfo()->getBasename();
                $data['file_mimetype'] = $decorator->getMimeType();
                if(!empty($data['file_mimetype']) && is_numeric(strpos($data['file_mimetype'], 'image'))) {
                    $data['file_is_image'] = true;
                }
                else $data['file_is_image'] = false;
                $data['base_elements'] = $this->getBaseElementsRendered($index, $file);
                $preview_data[] = $data;
            }
        }
        return $preview_data;
    }
    
    private function getPreviewDecorator($index, $file) {
        $fileField = $this->getModel()->findById($file['file_id']);
        $decorator = new \Vegas\Media\File\Decorator($fileField); 

        return $decorator;
    }
    
    private function getBaseElementsRendered($index, $file) {
        $baseElementsArray = array();
        $baseElements = $this->getBaseElements();
        if(!empty($baseElements)) {
            foreach($baseElements as $baseElement) {
                $baseElementName = $baseElement->getName();
                $originalName = substr($baseElementName, 2, -2);
                $defaultValue = null;
                if(!empty($file[$originalName])) {
                    $defaultValue = $file[$originalName];
                }
                $baseElement->setDefault($defaultValue);
                $baseElementHtml = $baseElement->renderDecorated();
                $baseElementsArray[] = str_replace($baseElementName, $this->getName().'['.$index.']['.$originalName.']', $baseElementHtml);
            }
        }
        return $baseElementsArray;
    }
}