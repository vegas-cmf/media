<?php
/**
 * This file is part of Vegas package.
 * 
 * Crud with additional upload action.
 * 
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Mvc\Controller;

use Vegas\Mvc\Controller\Crud\Exception\UploaderNotSetException,
    Vegas\Mvc\Controller\Crud\Events;

abstract class CrudUploadAbstract extends CrudAbstract
{

    public function initialize() {
        parent::initialize();

        $this->dispatcher->getEventsManager()->attach(Events::AFTER_UPDATE, $this->deleteFiles());
    }

    /**
     * @ACL(name="upload", inherit='edit')
     */
    public function uploadAction()
    {
        if (!$this->di->has('uploader')) {
            throw new UploaderNotSetException();
        }

        $this->view->disable();
        $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_UPLOAD, $this);

        $files = array();
        
        if($this->request->isAjax() && $this->request->hasFiles()) {
            try {
                $uploader = $this->di->get('uploader');
                $files[] = $uploader->setFiles($this->request->getUploadedFiles())->handle();
            } catch (\Exception $e) {
                $files[] = array('error' => $e->getMessage());
            }
        }
        
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_UPLOAD, $this);
        return $this->response->setJsonContent(array('files' => $files));
    }

    /**
     * @return callable
     */
    private function deleteFiles() {
        return function() {
            $deletedFiles = $this->request->getPost('deleted_files');
            if(!$deletedFiles) {
                return false;
            }

            $record = $this->scaffolding->getRecord();
            foreach($deletedFiles as $fileFieldName => $files) {
                $record->$fileFieldName = $files;

                $mappedFields = $record->readMapped($fileFieldName);
                if(!$mappedFields) {
                    return false;
                }

                foreach($mappedFields as $file) {
                    $file = $file->getRecord();
                    if(!$this->checkIfImplementsFileInterface($file)) {
                        return false;
                    }

                    $filePath = $file->original_destination . '/'. $file->temp_name;
                    if(file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $file->delete();
                }
            }
        };
    }

    /**
     * @param $file
     * @return bool
     */
    private function checkIfImplementsFileInterface($file) {
        $implements = class_implements($file);
        if(isset($implements['Vegas\Media\Db\FileInterface'])) {
            return true;
        }

        return false;
    }
}