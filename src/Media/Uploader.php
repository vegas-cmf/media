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

use Vegas\Media\Db\FileInterface;
use Vegas\Media\Uploader\Exception\InvalidUploadedFileMimeTypeException;
use \Vegas\Utils\Path;
use \Vegas\Media\Uploader\Exception\NoFilesException;
use \Vegas\Media\Uploader\Exception\InvalidMaxSizeException;
use \Vegas\Media\Uploader\Exception\CannotSaveFileInDatabaseException;
use \Vegas\Media\Uploader\Exception\CannotSaveFileInHardDriveException;
use \Vegas\Media\Uploader\Exception\InvalidUploadedFileSizeException;
use \Vegas\Media\Uploader\Exception\InvalidUploadedFileExtensionException;

class Uploader
{
    /**
     * Default max allowed file size
     */
    const DEFAULT_MAX_SIZE = '10MB';

    /**
     * Default path of the original destination of uploaded files
     */
    const DEFAULT_ORIGINAL_DESTINATION = '/public/uploads';

    /**
     * Default path of the temporary destination of uploaded files
     */
    const DEFAULT_TEMPORARY_DESTINATION = '/public/temp';

    /**
     * Array of files
     *
     * @var null
     */
    private $files = null;

    /**
     * Max allowed file size. In example. 10MB, 2GB, 2048B. After filtering size is measured in bytes.
     *
     * @var string
     */
    private $maxSize = null;

    /**
     * Allowed file extensions.
     *
     * @var array
     */
    private $extensions = null;

    /**
     * Allowed file mime types.
     *
     * @var array
     */
    private $mimeTypes = null;

    /**
     * Original destination of the uploaded file
     *
     * @var string
     */
    private $originalDestination = null;

    /**
     * Temporary destination of the uploaded file
     *
     * @var null
     */
    private $tempDestination = null;

    /**
     * Class represents model
     *
     * @var FileInterface
     */
    private $fileModel;

    /**
     * Constructor
     * Sets model class
     *
     * @param FileInterface $fileModel
     */
    public function __construct(FileInterface $fileModel)
    {
        $this->tempDestination = Path::getRootPath() . self::DEFAULT_TEMPORARY_DESTINATION;
        $this->originalDestination = Path::getRootPath() . self::DEFAULT_ORIGINAL_DESTINATION;
        $this->maxSize = $this->filterMaxSize(self::DEFAULT_MAX_SIZE);
        $this->fileModel = $fileModel;
    }
    
    /**
     * Filters max file size. Parameter $maxSize can have values such as '10MB', '2GB', '421452', '5251B' but the
     * private class property $this->maxSize can only have size in bytes.
     *
     * @param $maxSize
     * @return integer
     * @throws InvalidMaxSizeException
     */
    private function filterMaxSize($maxSize)
    {
        if(ctype_digit($maxSize)) {
            return (int) $maxSize;
        }

        if(strpos(strtoupper($maxSize), 'KB') !== false) {
            return (int) 1024 * substr($maxSize, 0, -2);
        }

        if(strpos(strtoupper($maxSize), 'MB') !== false) {
            return (int) 1024 * 1024 * substr($maxSize, 0, -2);
        }

        if(strpos(strtoupper($maxSize), 'GB') !== false) {
            return (int) 1024 * 1024 * 1024 * substr($maxSize, 0, -2);
        }

        if(strpos(strtoupper($maxSize), 'TB') !== false) {
            return (int) 1024 * 1024 * 1024 * 1024 * substr($maxSize, 0, -2);
        }

        if(ctype_digit(substr($maxSize, 0, -1)) && strpos(strtoupper($maxSize), 'B') !== false) {
            return (int) substr($maxSize, 0, -1);
        }

        throw new InvalidMaxSizeException();
    }

    /**
     * Sets files in uploader
     *
     * @param array $files
     * @return $this
     */
    public function setFiles(array $files = array())
    {
        $this->files = $files;
        return $this;
    }

    /**
     * Sets allowed extensions
     *
     * @param array $extensions
     * @return $this
     */
    public function setExtensions(array $extensions = array())
    {
        $this->extensions = $extensions;
        return $this;
    }

    /**
     * Sets allowed mime types
     *
     * @param array $mimeTypes
     * @return $this
     */
    public function setMimeTypes(array $mimeTypes = array())
    {
        $this->mimeTypes = $mimeTypes;
        return $this;
    }

    /**
     * Sets the original destination of uploaded files
     *
     * @param $originalDestination
     * @return $this
     */
    public function setOriginalDestination($originalDestination)
    {
        $this->originalDestination = $originalDestination;
        return $this;
    }

    /**
     * Sets the temporary destination of uploaded files
     *
     * @param $tempDestination
     * @return $this
     */
    public function setTempDestination($tempDestination)
    {
        $this->tempDestination = $tempDestination;
        return $this;
    }

    /**
     * Sets max available size of uploaded file
     *
     * @param $maxSize
     * @return $this
     */
    public function setMaxFileSize($maxSize)
    {
        $this->maxSize = $this->filterMaxSize($maxSize);
        return $this;
    }

    /**
     * Handles the main behaviour of the uploader class. It must call all validations functions and saves data
     * in the specific destinations
     *
     * @return $this
     * @throws Uploader\Exception\InvalidUploadedFileSizeException
     * @throws Uploader\Exception\NoFilesException
     * @throws InvalidUploadedFileMimeTypeException
     * @throws Uploader\Exception\InvalidUploadedFileExtensionException
     */
    public function handle()
    {
        if(empty($this->files)) {
            throw new NoFilesException();
        }

        $resultFiles = array();

        foreach($this->files as $file) {

            $this->validateFile($file);
            $tempName = $this->moveUploadedFileToTempDestination($file);
            $model = $this->saveUploadedFile($file, $tempName);

            $resultFiles[] = array(
                'file_id' => (string) $model->getId()
            );
        }

        return $resultFiles;
    }

    /**
     * Validates single file
     *
     * @param $file
     * @throws Uploader\Exception\InvalidUploadedFileSizeException
     * @throws Uploader\Exception\InvalidUploadedFileMimeTypeException
     * @throws Uploader\Exception\InvalidUploadedFileExtensionException
     */
    private function validateFile($file)
    {
        if(!$this->hasAllowedSize($file)) {
            throw new InvalidUploadedFileSizeException();
        }

        if(!$this->hasAllowedExtension($file)) {
            throw new InvalidUploadedFileExtensionException();
        }

        if(!$this->hasAllowedMimeType($file)) {
            throw new InvalidUploadedFileMimeTypeException();
        }
    }
    
    /**
     * Moves uploaded file to the temporary directory
     *
     * @param \Phalcon\Http\Request\FileInterface $file
     * @return string $tempFileName
     * @throws CannotSaveFileInHardDriveException
     */
    private function moveUploadedFileToTempDestination(\Phalcon\Http\Request\FileInterface $file)
    {
        $extension = pathinfo($file->getName(), PATHINFO_EXTENSION);

        $tempName = uniqid() . '.' . $extension;

        $file->moveTo($this->tempDestination . '/' . $tempName);

        if(!file_exists($this->tempDestination . '/' . $tempName)) {
            throw new CannotSaveFileInHardDriveException();
        }

        return $tempName;
    }

    /**
     * Saves uploaded file in the mongo "file" collection
     *
     * @param \Phalcon\Http\Request\FileInterface $file
     * @param $tempName
     * @return FileInterface
     * @throws Uploader\Exception\CannotSaveFileInDatabaseException
     */
    private function saveUploadedFile(\Phalcon\Http\Request\FileInterface $file, $tempName)
    {
        $expire = date('Y-m-d H:i:s') . ' + 2 hours';

        $model = new $this->fileModel;
        $model->name = $file->getName();
        $model->expire = strtotime($expire);
        $model->is_temp = true;
        $model->temp_name = $tempName;
        $model->temp_destination = $this->tempDestination;
        $model->original_destination = $this->originalDestination;
        if($model->save() == false) {
            throw new CannotSaveFileInDatabaseException();
        }
        
        return $model;
    }

    /**
     * Checks if file has allowed size
     *
     * @param $file
     * @return bool
     */
    private function hasAllowedSize($file)
    {
        if(empty($this->maxSize) || $file->getSize() <= $this->maxSize) {
            return true;
        }   
        
        return false;
    }

    /**
     * Checks if file has allowed extension
     *
     * @param $file
     * @return bool
     */
    private function hasAllowedExtension($file)
    {
        $tmp = explode('.', $file->getName());

        $extension = end($tmp);

        if(empty($this->extensions) || in_array($extension, $this->extensions)) {
            return true;
        }
        
        return false;
    }

    /**
     * Checks if file has allowed mime type
     *
     * @param $file
     * @return bool
     */
    private function hasAllowedMimeType($file)
    {
        if (function_exists('mime_content_type')) {
            $mimeType = mime_content_type($file->getTempName());
        } else {
            $mimeType = $this->fileMimeContentType($file->getTempName());
        }

        if(empty($this->mimeTypes) || in_array($mimeType, $this->mimeTypes)) {
            return true;
        }

        return false;
    }

    /**
     * Returns file mime type
     *
     * @param $filename
     * @return bool|string
     */
    private function fileMimeContentType($filename)
    {
        // Sanity check
        if (!file_exists($filename)) {
            return false;
        }

        $filename = escapeshellarg($filename);
        $out = `file -iL $filename 2>/dev/null`;
        if (empty($out)) {
            return 'application/octet-stream';
        }
        // Strip off filename
        $t = substr($out, strpos($out, ':') + 2);
        if (strpos($t, ';') !== false) {
            // Strip MIME parameters
            $t = substr($t, 0, strpos($t, ';'));
        }
        // Strip any remaining whitespace
        return trim($t);
    }
}