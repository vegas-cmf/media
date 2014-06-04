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

use \Vegas\Media\Swf\Exception\InvalidSourceExtensionException;

class Swf
{
    /**
     * Constant extension for swf files
     */
    CONST SOURCE_EXTENSION = 'swf';

    /**
     * Source of the swf file
     *
     * @var string
     */
    private $source = '';

    /**
     * Width of the html element
     *
     * @var string
     */
    private $width = '100%';

    /**
     * Height of the html element
     *
     * @var string
     */
    private $height = '100%';

    /**
     * Embed tag decorator
     *
     * @var string
     */
    private $embedDecorator = '<embed width="%s" height="%s" src="%s"></embed>';

    /**
     * Object tag decorator
     *
     * @var string
     */
    private $objectDecorator = '<object width="%s" height="%s" data="%s"></object>';

    /**
     * Constructs the swf object
     *
     * @param array $params
     */
    public function construct(array $params = array()) {}

    /**
     * Sets source of the .swf file
     *
     * @param string $source
     * @throws Swf\Exception\InvalidSourceExtensionException
     * @return $this;
     */
    public function setSource($source = '')
    {
        if(!$this->isValid($source)) {
            throw new InvalidSourceExtensionException();
        }

        $this->source = $source;

        return $this;
    }

    /**
     * Returns source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Sets width attribute
     *
     * @param $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = (string) $width;

        return $this;
    }

    /**
     * Sets height attribute
     *
     * @param $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Returns embed decorator
     *
     * @return string
     */
    public function getEmbedDecorator()
    {
        return $this->embedDecorator;
    }

    /**
     * Returns object decorator
     *
     * @return string
     */
    public function getObjectDecorator()
    {
        return $this->objectDecorator;
    }

    /**
     * Checks if the source has allowed extension
     *
     * @param string $source
     * @return bool
     */
    private function isValid($source = '')
    {
        if(empty($source)) {
            return false;
        } else {
            $extension = substr($source, -3);

            if(strtolower($extension) !== 'swf') {
                return false;
            }
        }

        return true;
    }

    /**
     * Renders the embed tag html with attached source
     *
     * @return string
     * @throws Swf\Exception\InvalidSourceExtensionException
     */
    public function renderEmbed()
    {
        if(!$this->isValid($this->source)) {
            throw new InvalidSourceExtensionException();
        }

        return sprintf($this->embedDecorator, $this->width, $this->height, $this->source);
    }

    /**
     * Renders the object tag html with attached source
     *
     * @return string
     * @throws Swf\Exception\InvalidSourceExtensionException
     */
    public function renderObject()
    {
        if(!$this->isValid($this->source)) {
            throw new InvalidSourceExtensionException();
        }

        return sprintf($this->objectDecorator, $this->width, $this->height, $this->source);
    }
}