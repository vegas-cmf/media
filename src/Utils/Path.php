<?php

/**
 * This file is part of Vegas package
 *
 * How to use Atom class:
 *
 *
 * @author Adrian Malik <adrian.malik.89@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Utils;

final class Path
{
    /**
     * Returns root path of the project (/var/www/vegas)
     *
     * @return string
     */
    public static final function getRootPath()
    {
        return APP_ROOT;
    }

    /**
     * Returns path to the public directory (/var/www/vegas/public)
     *
     * @return string
     */
    public static final function getPublicPath()
    {
        return self::getRootPath() . '/public';
    }

    /**
     * Returns path to the library directory (/var/www/vegas/lib)
     *
     * @return string
     */
    public static final function getLibPath()
    {
        return self::getRootPath() . '/lib';
    }

    /**
     * Returns path to the application directory (/var/www/vegas/app)
     *
     * @return string
     */
    public static final function getAppPath()
    {
        return self::getRootPath() . '/app';
    }

    /**
     * Returns path to the config directory (/var/www/vegas/config)
     *
     * @return string
     */
    public static final function getConfigPath()
    {
        return self::getRootPath() . '/config';
    }

    /**
     * Returns path to the temporary directory (/var/www/vegas/temp)
     *
     * @return string
     */
    public static final function getTempPath()
    {
        return self::getRootPath() . '/temp';
    }

    /**
     * Returns path to the tests directory (/var/www/vegas/tests)
     *
     * @return string
     */
    public static final function getTestsPath()
    {
        return self::getRootPath() . '/tests';
    }

    /**
     * Returns relative path of the absolute path. When you pass $absolutePath='/var/www/vegas/config'  then the result
     * should be '/config'
     *
     * @param $absolutePath
     * @return mixed|string
     */
    public static final function getRelativePath($absolutePath)
    {
        $pattern = array(self::getRootPath(), DIRECTORY_SEPARATOR, '//');
        $replacement = array('', '/', '/');
        
        $raltivePath = str_replace($pattern, $replacement, $absolutePath);
            
        if(substr($raltivePath, 0, 1) != '/') {
            $raltivePath = '/' . $raltivePath;
        }
        
        return str_replace('/public', '', $raltivePath);
    }

    /**
     * Returns the name of directory where file is located
     *
     * @param $path
     * @return string
     */
    public static final function getFileDirectory($path)
    {
        $directoryName = dirname($path);

        return $directoryName;
    }
}