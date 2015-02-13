<?php
return array(
    'testcrud' => array(
        'route' => '/test/crud/:action/:params',
        'paths' => array(
            'module' => 'Test',
            'controller' => 'Backend\Crud',
            'action' => 1,
            'params' => 2
        )
    )

);