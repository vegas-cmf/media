<?php
namespace Vegas\Media\Model;

use Vegas\Db\Decorator\CollectionAbstract;

class File extends CollectionAbstract
{
    public function getSource()
    {
        return 'vegas_files';
    }
}