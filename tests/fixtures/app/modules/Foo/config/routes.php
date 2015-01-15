<?php
return array(
    'testcrudupload' => array(
        'route' => '/test/crudupload/:action/:params',
        'paths' => array(
            'module' => 'Foo',
            'controller' => 'Backend\CrudUpload',
            'action' => 1,
            'params' => 2
        )
    )
);