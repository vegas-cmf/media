<?php
/**
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * Date: 7/14/14
 * Time: 1:04 PM
 */

namespace Vegas\Media\Db;

/**
 * Interface FileInterface
 * @package Vegas\Media\Db
 */
interface FileInterface 
{
    /**
     * Returns ID from database
     *
     * @return mixed
     */
    public function getId();
} 