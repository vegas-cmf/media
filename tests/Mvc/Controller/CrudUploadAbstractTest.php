<?php
/**
 * This file is part of Vegas package
 *
 * @author Tomasz Borodziuk <tomasz.borodziuk@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\Mvc\Controller;

use Phalcon\DI;
use Test\Forms\Fake;
use Test\Models\Fake as FakeModel;
use Vegas\Mvc\Controller\Crud;
use Vegas\Test\TestCase;

class CrudUploadTest extends TestCase
{
    protected $model;

    public function setUp()
    {
        parent::setUp();
        $config = DI::getDefault()->get('config');
        require_once $config->application->moduleDir . '/Test/forms/Fake.php';
        $this->prepareFakeObject();
    }

    private function prepareFakeObject()
    {
        $this->model = FakeModel::findFirst(array(array(
            'fake_field' => base64_encode(date('Y-m-d'))
        )));

        if (!$this->model) {
            $this->model = new FakeModel();
            $this->model->fake_field = base64_encode(date('Y-m-d'));

            $this->model->save();
        }
    }

    public function testUpload()
    {
        $this->request()->setRequestMethod('GET');

        $content = $this->handleUri('/test/crud/upload')->getContent();
        $this->assertTrue(isset(json_decode($content)->files));
    }

    /*public function testNew()
    {
        $this->request()->setRequestMethod('GET');

        $form = new Fake();

        $content = $this->handleUri('/test/crud/new')->getContent();
        $this->assertContains($form->get('fake_field')->render(['class' => ' form-control']), $content);

        $this->assertContains('<form action="/test/crud/create/" method="POST" role="form">', $content);
    }*/

    public function testPostUpdateResponse()
    {
        $this->request()
            ->setRequestMethod('POST')
            ->setPost('fake_field', base64_encode('foobar'));

        $response = $this->handleUri('/test/crud/update/'.$this->model->getId());
        $contentArray = json_decode($response->getContent(), true);

        $model = FakeModel::findById($contentArray['$id']);

        $this->assertInstanceOf('\Test\Models\Fake', $model);
        $this->assertEquals(base64_encode('foobar'), $model->fake_field);

        $model->delete();
    }
}