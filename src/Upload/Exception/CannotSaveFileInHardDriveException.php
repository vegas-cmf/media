<?php
/**
 * This file is part of Vegas package
 *
 * @author Radosław Fąfara <radek@amsterdamstandard.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://cmf.vegas
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Upload\Exception;


use Vegas\Upload\Exception as VegasException;

class CannotSaveFileInHardDriveException extends VegasException
{
    protected $message = 'Cannot save file in the filesystem';
}