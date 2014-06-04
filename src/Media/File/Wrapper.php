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

use Vegas\Media\Model\File As FileModel;

class Wrapper
{
    /**
     * @param \Vegas\Media\Model\File $record
     * @return \Vegas\Media\File\Decorator
     */
    public function wrapRecord(FileModel $record, $extraData = array())
    {
        return new Decorator($record, $extraData);
    }
    
    public function wrapValues($values = array())
    {
        $wrappedRecords = array();
        
        if (!empty($values) && is_array($values)) {
            $preparedValues = array();

            foreach ($values As $value) {
                $preparedValues['ids'][] = new \MongoId($value['file_id']);
                $preparedValues[$value['file_id']] = $value;
            }
            
            $wrappedRecords = $this->wrapPreparedValues($preparedValues);
        }
        
        return $wrappedRecords;
    }
    
    private function wrapPreparedValues($preparedValues = array())
    {
        $files = array();
        $records = FileModel::find(array(array('_id' => array('$in' => $preparedValues['ids']))));
        foreach ($records As $record) {
            $files[] = $this->wrapRecord($record, $preparedValues[(string)$record->getId()]); 
        }
        
        return $files;
    }
} 