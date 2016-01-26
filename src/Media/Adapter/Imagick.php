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

namespace Vegas\Media\Adapter;
use Phalcon\Image;


/**
 * Class Imagick
 * Provides compatibility for Vegas Media library users updating from older version due to WideImage replacement.
 * @package Vegas\Media\Adapter
 */
class Imagick extends \Phalcon\Image\Adapter\Imagick
{
    /**
     * {@inheritdoc}
     */
    public function resize($width = null, $height = null, $master = null)
    {
        switch ($master) {
            case 'fill':
                $master = Image::TENSILE;
                break;
            case 'inside':
                $master = Image::AUTO;
                break;
            case 'outside':
                $master = Image::INVERSE;
                break;
            case 'precise':
                $master = Image::PRECISE;
                break;
        }
        return parent::resize($width, $height, $master);
    }

    /**
     * {@inheritdoc}
     */
    public function crop($width, $height, $offset_x = null, $offset_y = null)
    {
        switch ($offset_x) {
            case 'left':
                $offset_x = 0;
                break;
            case 'right':
                $offset_x = (int)($this->getWidth() - $width);
                break;
            case 'center':
                $offset_x = (int)(($this->getWidth() - $width) / 2);
                break;
        }
        switch ($offset_y) {
            case 'top':
                $offset_y = 0;
                break;
            case 'bottom':
                $offset_y = (int)($this->getHeight() - $height);
                break;
            case 'middle':
                $offset_y = (int)(($this->getHeight() - $height) / 2);
                break;
        }
        return parent::crop($width, $height, $offset_x, $offset_y);
    }

}