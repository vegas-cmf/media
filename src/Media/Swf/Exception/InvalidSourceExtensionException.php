<?php
/**
 * This file is part of Vegas package
 *
 * @author Adrian Malik <adrian.malik.89@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Media\Swf\Exception;

use Vegas\Media\Swf\Exception as VegasMediaSwfException;

class InvalidSourceExtensionException extends VegasMediaSwfException
{
    protected $message = 'Invalid swf source extension';
}
