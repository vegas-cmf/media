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
namespace Test\Models;
use Vegas\Db\Decorator\CollectionAbstract;
use Vegas\Media\Db\FileInterface;

class FakeFile extends CollectionAbstract implements FileInterface
{
    public function getSource()
    {
        return 'vegas_files';
    }
}