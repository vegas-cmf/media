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
namespace Vegas\Tests\Forms\Element;

use Phalcon\DI;
use Vegas\Forms\Element\Upload;
use Vegas\Tests\Stub\Models\FakeModel;
use \Vegas\Forms\Form,
    \Phalcon\Forms\Element\Text;
use Vegas\Validation\Validator\PresenceOf;

class FakeVegasForm extends Form
{
    public function initialize()
    {
        $field = new Text('fake_field');
        $field->addValidator(new PresenceOf());
        $this->add($field);
    }
}

class UploadTest extends \PHPUnit_Framework_TestCase
{
    protected $di;
    protected $form;
    protected $model;

    protected function setUp()
    {
        $this->di = DI::getDefault();
        $this->model = new FakeModel();
        $this->form = new FakeVegasForm();

        $upload = new Upload('upload');
        $this->form->add($upload);
    }

    public function testRender()
    {
        $this->assertNull($this->form->get('upload')->getAssetsManager());

        try {
            $this->form->get('upload')->render();
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Forms\Exception', $ex);
        }

        $this->form->get('upload')->setAssetsManager($this->di->get('assets'));

        $generatedHtmlLength = strlen('<div data-for-id="537ca0e52a49c" data-form-element-upload-wrapper="true"><input type="file" id="upload" name="upload" data-form-element-upload="true" data-id="537ca0e52a49c" data-trigger-type="button" data-button-add-label="Add file" /><div data-jq-upload-error></div><div data-jq-upload-preview></div><div data-templates></div></div>');

        $this->assertInstanceOf('\Phalcon\Assets\Manager', $this->form->get('upload')->getAssetsManager());
        $this->assertEquals($generatedHtmlLength, strlen($this->form->get('upload')->render()));
    }
}
