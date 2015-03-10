<?php

/**
 * This file is part of Vegas package
 *
 * How to use Atom class in Controller:
 *
 *      if($this->request->hasFiles()) {
 *          $uploader = $this->di->get('uploader');
 *          $uploader->setMaxSize('10MB')
 *                   ->setExtensions(array('jpg', 'png'))
 *                   ->setMimeTypes(array('image/jpeg', 'image/png'))
 *                   ->setTempDestination(Path::getRootPath() . '/temp/uploads')
 *                   ->setOriginalDestination(Path::getRootPath() . '/public/uploads');
 *
 *          $uploader->setFiles($this->request->getUploadedFiles())
 *                   ->handle();
 *      } else {
 *          echo 'There is no files here';
 *      }
 *
 * !IMPORTANT - remember to have directory /var/www/vegas/temp/uploads with chmod 777 permissions
 *
 * @author Adrian Malik <adrian.malik.89@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Media;

use \Vegas\Utils\Path;

class Uploader
{

}