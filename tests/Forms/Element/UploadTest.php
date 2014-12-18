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
use Vegas\Forms\Element\File;
use Vegas\Forms\Element\Upload;
use Vegas\Tests\Stub\Models\FakeModel;
use Vegas\Forms\Form;
use Vegas\Forms\Element\Text;
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
        $this->assertEquals((new File('upload'))->render(), $this->form->get('upload')->render());
        $this->assertEquals((new File('upload'))->render(), $this->form->get('upload')->renderDecorated());

        $html1 = 'data-form-element-upload-wrapper="true"';
        $html2 = '<input type="file" id="upload" name="upload" data-form-element-upload="true"';
        $html3 = '<div data-jq-upload-error></div>';
        $html4 = '<div data-jq-upload-preview></div>';

        $this->form->get('upload')->getDecorator()->setTemplateName('jquery');

        $this->assertContains($html1, $this->form->get('upload')->renderDecorated());
        $this->assertContains($html2, $this->form->get('upload')->renderDecorated());
        $this->assertContains($html3, $this->form->get('upload')->renderDecorated());
        $this->assertContains($html4, $this->form->get('upload')->renderDecorated());
    }

    public function testBaseElements()
    {
        $textField = new Text('additional_text');

        $this->form->get('upload')->setBaseElements([$textField]);
        $this->form->get('upload')->getDecorator()->setTemplateName('jquery');

        $this->assertContains('<input type="text" name="{{additional_text}}"', $this->form->get('upload')->renderDecorated());
    }
}
