<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Media\File;

use Vegas\Utils\Path;

class Decorator
{
    private $record;
    private $extraData;
    
    /**
     * @param \Vegas\Media\Model\File $record
     */
    public function __construct(\Vegas\Media\Model\File $record, $extraData = array())
    {
        $this->record = $record;
        $this->extraData = $extraData;
    }

    /**
     * @return \Vegas\Media\Model\File
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return Path::getRelativePath($this->getPath());
    }
    
    /**
     * @return string
     */
    public function getPath()
    {
        $paths = array();
        $paths[] = $this->getFileDirectoryPath();
        $paths[] = $this->record->temp_name;

        return DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $paths);
    }

    /**
     * @param int $width
     * @param int $height
     * @return mixed|string
     */
    public function getThumbnailUrl($width = 168, $height = 120)
    {
        return Path::getRelativePath($this->getThumbnailPath($width, $height));
    }

    /**
     * @param $width
     * @param $height
     * @return string
     */
    public function getThumbnailPath($width, $height)
    {
        $paths = array();
        $paths[] = $this->getFileDirectoryPath();
        $paths[] = 'thumbnails';
        $paths[] = $width . '_' . $height . '_' . $this->record->temp_name;
        
        return DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $paths);
    }
    
    /**
     * @return string
     */
    private function getFileDirectoryPath()
    {
        if ($this->record->is_temp) {
            return trim($this->record->temp_destination, DIRECTORY_SEPARATOR);
        } 
        
        return trim($this->record->original_destination, DIRECTORY_SEPARATOR);   
    }

    public function getMimeType()
    {
        return mime_content_type($this->getPath());
    }
    
    /**
     * @return \SplFileInfo
     */
    public function getFileInfo()
    {
        $file = new \SplFileInfo($this->getPath());

        return $file;
    }
    
    /**
     * @return string - icon url
     */
    public function getIcon()
    {
        $fileInfo = $this->getFileInfo();

        return '/assets/img/common/icons/filetypes/'.$fileInfo->getExtension().'.png';
    }
    
    public function getId()
    {
        return $this->record->getId();
    }
    
    public function __get($name)
    {
        if (empty($this->extraData[$name])) {
            return null;
        }
        
        return $this->extraData[$name];
    }
    
    public function save()
    {
        return $this->record->save();
    }
    
    /**
     * @TODO remove all thumbnails
     * @return bool
     */
    public function delete()
    {
        $filePath = $this->getPath();
   
        if (file_exists($filePath)) {
            unlink($filePath);
        }
       
        return $this->record->delete();
    }
}
