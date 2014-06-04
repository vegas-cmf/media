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
namespace Vegas\Media;

use Vegas\Media\Model\File as FileModel;
use Vegas\Media\File\Exception as FileException;
use Vegas\Media\Uploader\Exception\CannotSaveFileInHardDriveException;
use WideImage;

class Helper
{
    /**
     * Moves files from record (from "files" property of collection) from temporary destination
     * to the original destination.
     * Example of $record:
     *      {
     *          "_id": ObjectId("53195f675c65d1ba0b7b23c7"),
     *          "created_at": NumberInt(1394171751),
     *          "files": {                                <---- This line is the most important
     *              "0": {
     *                  "title": "Our Beautiful File",
     *                  "file_id": "53195f665c65d1ba0b7b23c6"
     *              }
     *          },
     *          "updated_at": NumberInt(1394171751)
     *      }
     *
     * @param \Vegas\Mvc\CollectionAbstract $record
     * @throws CannotSaveFileInHardDriveException
     * @throws \Exception
     */
    public static final function moveFilesFrom(\Vegas\Mvc\CollectionAbstract $record)
    {
        if(isset($record->files)) {
            foreach($record->files as $file) {
                self::moveFile(FileModel::findById($file['file_id']));
            }
        } else {
            throw new FileException();
        }
    }

    /**
     * Move file from the temporary destination to the original destination
     *
     * @param FileModel $fileModel
     * @throws CannotSaveFileInHardDriveException
     * @throws \Exception
     */
    public static final function moveFile(\Vegas\Media\Model\File $fileModel)
    {
        try {
            if($fileModel->is_temp) {
                rename(
                    $fileModel->temp_destination . '/' . $fileModel->temp_name,
                    $fileModel->original_destination . '/' . $fileModel->temp_name
                );

                if(!file_exists($fileModel->original_destination . '/' . $fileModel->temp_name)) {
                    throw new CannotSaveFileInHardDriveException();
                }

                $fileModel->is_temp = false;
                $fileModel->expire = null;
                $fileModel->save();
            }
        } catch (\Exception $exception) {
            throw new FileException($exception->getMessage());
        }
    }

    /**
     * Generates a thumbnails of the files associated with the record
     *
     * @param \Vegas\Mvc\CollectionAbstract $record
     * @param array $size
     * @throws File\Exception
     */
    public static final function generateThumbnailsFrom(\Vegas\Mvc\CollectionAbstract $record, array $size)
    {
        if(isset($record->files)) {
            foreach($record->files as $file) {
                self::generateThumbnail(FileModel::findById($file['file_id']), $size);
            }
        } else {
            throw new FileException();
        }
    }

    /**
     * Generates a thumbnails of the images in the following location:
     * {ORIGINAL_DESTINATION_OF_THE_FILE}/thumbnails/filename.{ext}
     *
     * @param FileModel $fileModel
     * @param array $size
     */
    public static final function generateThumbnail(\Vegas\Media\Model\File $fileModel, array $size = array('width' => 168, 'height' => 120))
    {
        if(!empty($fileModel->original_destination) && isset($size['width']) && isset($size['height'])) {

            // ie. string(47) "/var/www/vegas/public/uploads/5326acd311dd4.jpg"
            $file = $fileModel->original_destination . '/' . $fileModel->temp_name;

            if (!file_exists($fileModel->original_destination . '/thumbnails')) {
                mkdir($fileModel->original_destination . '/thumbnails', 0777, true);
            }

            WideImage::load($file)
                ->resize($size['width'], $size['height'], 'outside')
                ->crop('center', 'middle', $size['width'], $size['height'])
                ->saveToFile($fileModel->original_destination . '/thumbnails/' . $size['width'] . '_' . $size['height'] . '_' . $fileModel->temp_name);
        }
    }
}
