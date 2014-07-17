<?php
/**
 * This file is part of Vegas package
 *
 * @author Adrian Malik <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Forms\Element\Upload;

use Phalcon\Forms\Element,
    Vegas\Forms\Element\Upload;

class Renderer
{
    private $upload;
    private $attributes = array();

    public function __construct(Upload $element, array $attributes = array()) {
        $this->upload = $element;
        $this->attributes = $attributes;
    }

    public function run()
    {
        return $this->wrapWithDecorator($this->render());
    }

    private function render()
    {
        $attributes = $this->upload->getAttributes();

        $file = new \Phalcon\Forms\Element\File($this->upload->getName());
        $file->setAttributes(array_merge($attributes, $this->upload->getUploadAttributes()));

        $label = 'Add file';

        $buttonLabels = $this->upload->getButtonLabels();

        if(isset($buttonLabels) && isset($buttonLabels['add'])) {
            $label = $buttonLabels['add'];
        }

        $file->setAttribute('data-button-add-label', $label);
        $file->setAttribute('value', null);

        return $file->render();
    }

    private function wrapWithDecorator($html)
    {
        return sprintf($this->getDecorator(), $html);
    }

    private function getDecorator()
    {
        $uploadAttributes = $this->upload->getUploadAttributes();

        $baseElementsTemplates = '';
        $baseElements = $this->upload->getBaseElements();
        if(isset($baseElements)) {
            foreach($baseElements as $baseElement) {
                $baseElementsTemplates .= '<script id="' . $baseElement->getAttribute('data-template-id') . '" type="text/x-handlebars-template">';
                $baseElement->setName('[[' . $baseElement->getName() . ']]');
                $baseElementsTemplates .= $baseElement->render();
                $baseElementsTemplates .= '</script>';
            }
        }

        $html =
            '<div data-for-id="'.$uploadAttributes['data-id'].'" data-form-element-upload-wrapper="true">' .
                '%s' .
                '<div data-jq-upload-error></div>'.
                '<div data-jq-upload-preview></div>'.
                '<div data-templates>' .
                    $baseElementsTemplates .
                '</div>' .
            '</div>';

        if($this->upload->getRenderPreview()) {
            $values = $this->upload->getValue();
            if(!empty($values)) {
                if(is_string($values)) {
                    $values = json_decode($values, true);
                }

                foreach($values as $index => $file) {
                    $html .= $this->getPreviewDecorator($index, $file);
                }
            }
        }

        return $html;
    }

    private function getPreviewDecorator($index, $file)
    {
        $fileField = $this->upload->getModel()->findById($file['file_id']);

        $decorator = new \Vegas\Media\File\Decorator($fileField); 

        $baseElementsHtml = '';

        $baseElements = $this->upload->getBaseElements();
        if(!empty($baseElements)) {
            foreach($baseElements as $baseElement) {
                $baseElementName = $baseElement->getName();
                $originalName = substr($baseElementName, 2, -2);
                $defaultValue = null;
                if(!empty($file[$originalName])) {
                    $defaultValue = $file[$originalName];
                }
                $baseElement->setDefault($defaultValue);
                $baseElementHtml = $baseElement->render();

                $baseElementsHtml .= str_replace($baseElementName, $this->upload->getName().'['.$index.']['.$originalName.']', $baseElementHtml);
            }
        }

        $fileHtml = $decorator->getFileInfo()->getBasename();
        $fileMimeType = $decorator->getMimeType();
        if(!empty($fileMimeType) && is_numeric(strpos($fileMimeType, 'image'))) {
            $fileHtml = '<img src="'.$decorator->getUrl().'" width="190" >';
        }

        return '
            <div data-jq-upload-preview-stored>
                <p>
                    '.$fileHtml.'
                    '.$baseElementsHtml.'
                    <input type="hidden" name="'.$this->upload->getName().'['.$index.'][file_id]" value="'.$decorator->getId().'">
                    <br>
                    <button type="button" class="btn btn-danger" data-button="cancel" style="margin-left: 10px; float: right;">Remove</button>
                </p>
            </div>
        ';
    }
}
