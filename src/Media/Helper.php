<?php
/**1
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
     * @param array|ArrayObject $files
     */
    public static final function moveFilesFrom($files)
    {
        foreach($files as $file) {
            self::moveFile($file->getRecord());
        }
    }

    /**
     * Move file from the temporary destination to the original destination
     *
     * @param FileModel $file
     * @throws File\Exception
     */
    public static final function moveFile($file)
    {
        try {
            if($file->is_temp) {
                rename(
                    $file->temp_destination . '/' . $file->temp_name,
                    $file->original_destination . '/' . $file->temp_name
                );

                if(!file_exists($file->original_destination . '/' . $file->temp_name)) {
                    throw new CannotSaveFileInHardDriveException();
                }

                $file->is_temp = false;
                $file->expire = null;
                $file->save();
            }
        } catch (\Exception $exception) {
            throw new FileException($exception->getMessage());
        }
    }

    /**
     * Generates a thumbnails of the files associated with the record
     *
     * @param array|ArrayObject $files
     * @param array $size
     */
    public static final function generateThumbnailsFrom($files, array $size)
    {
        foreach($files as $file) {
            self::generateThumbnail($file->getRecord(), $size);
        }
    }

    /**
     * Generates a thumbnails of the images in the following location:
     * {ORIGINAL_DESTINATION_OF_THE_FILE}/thumbnails/filename.{ext}
     *
     * @param FileModel $file
     * @param array $size
     */
    public static final function generateThumbnail($file, array $size = array('width' => 168, 'height' => 120))
    {
        if(!empty($file->original_destination) && isset($size['width']) && isset($size['height'])) {

            // ie. string(47) "/var/www/vegas/public/uploads/5326acd311dd4.jpg"
            $filePath = $file->original_destination . '/' . $file->temp_name;

            if (!file_exists($file->original_destination . '/thumbnails')) {
                mkdir($file->original_destination . '/thumbnails', 0777, true);
            }

            WideImage::load($filePath)
                ->resize($size['width'], $size['height'], 'outside')
                ->crop('center', 'middle', $size['width'], $size['height'])
                ->saveToFile($file->original_destination . '/thumbnails/' . $size['width'] . '_' . $size['height'] . '_' . $file->temp_name);
        }
    }
}
