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

namespace Vegas\Upload;

trait Attributes
{
    private $maxFiles = null;
    private $maxFileSize = null;
    private $minFileSize = null;
    private $allowedExtensions = null;
    private $forbiddenExtensions = null;
    private $allowedMimeTypes = null;
    private $forbiddenMimeTypes = null;

    /**
     * @param int $maxFiles Max number of uploaded files
     * @return $this
     */
    public function setMaxFiles($maxFiles = 1)
    {
        $this->maxFiles = $maxFiles;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxFiles()
    {
        return $this->maxFiles;
    }

    /**
     * @param string $minFileSize Min allowed file size
     * @return $this
     */
    public function setMinFileSize($minFileSize = '')
    {
        $this->minFileSize = $minFileSize;
        return $this;
    }

    /**
     * @return string
     */
    public function getMinFileSize()
    {
        return $this->minFileSize;
    }

    /**
     * @param string $maxFileSize Max allowed file size
     * @return $this
     */
    public function setMaxFileSize($maxFileSize = '10MB')
    {
        $this->maxFileSize = $maxFileSize;
        return $this;
    }

    /**
     * @return string
     */
    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    /**
     * @param array $allowedExtensions Array of allowed extensions
     * @return $this
     */
    public function setAllowedExtensions(array $allowedExtensions = [])
    {
        $this->allowedExtensions = $allowedExtensions;
        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    /**
     * @param array $forbiddenExtensions Array of forbidden extensions
     * @return $this
     */
    public function setForbiddenExtensions(array $forbiddenExtensions = [])
    {
        $this->forbiddenExtensions = $forbiddenExtensions;
        return $this;
    }

    /**
     * @return array
     */
    public function getForbiddenExtensions()
    {
        return $this->forbiddenExtensions;
    }

    /**
     * @param array $allowedMimeTypes Array of allowed mime types
     * @return $this
     */
    public function setAllowedMimeTypes(array $allowedMimeTypes = [])
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedMimeTypes()
    {
        return $this->allowedMimeTypes;
    }

    /**
     * @param array $forbiddenMimeTypes Array of forbidden mime types
     * @return $this
     */
    public function setForbiddenMimeTypes(array $forbiddenMimeTypes = [])
    {
        $this->forbiddenMimeTypes = $forbiddenMimeTypes;
        return $this;
    }

    /**
     * @return array
     */
    public function getForbiddenMimeTypes()
    {
        return $this->forbiddenMimeTypes;
    }
}