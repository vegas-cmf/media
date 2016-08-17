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

namespace Vegas\Upload;

use Vegas\Mvc\View;
use Vegas\Upload\Exception\ForbiddenFileExtensionException;
use Vegas\Upload\Exception\ForbiddenFileMimeTypeException;

trait Upload
{
    /**
     * Upload action for every controller which inherit from CRUDController
     *
     * @return mixed
     * @throws \Exception
     */
    public function uploadAction()
    {
        if ($this->request->hasFiles() == true) {
            $this->initializeScaffolding();
            $form = $this->scaffolding->getForm();
            $name = key($_FILES);
            /** @var \Vegas\Forms\Element\Upload $uploadElement */
            $uploadElement = $form->get($name);
            $model = $uploadElement->getModel();

            $path = $uploadElement->getPath();
            $maxFileSize = $uploadElement->getMaxFileSize();
            $minFileSize = $uploadElement->getMinFileSize();

            foreach ($this->request->getUploadedFiles() as $file) {
                $fileName = $file->getName();
                $fileSize = $file->getSize();
                $fileType = $file->getRealType();
                $fileExtensions = pathinfo($fileName, PATHINFO_EXTENSION);

                $allowed = $uploadElement->getAllowedExtensions();
                if (!empty($allowed)) {
                    if (!in_array($fileExtensions, $allowed)) {
                        throw new ForbiddenFileExtensionException();
                    }
                }

                $forbidden = $uploadElement->getForbiddenExtensions();
                if (!empty($forbidden)) {
                    if (in_array($fileExtensions, $forbidden)) {
                        throw new ForbiddenFileExtensionException();
                    }
                }

                $allowedMime = $uploadElement->getAllowedMimeTypes();
                if (!empty($allowedMime)) {
                    if (!in_array($fileType, $allowedMime)) {
                        throw new ForbiddenFileMimeTypeException();
                    }
                }

                $forbiddenMime = $uploadElement->getForbiddenMimeTypes();
                if (!empty($forbiddenMime)) {
                    if (in_array($fileType, $forbiddenMime)) {
                        throw new ForbiddenFileMimeTypeException();
                    }
                }

                if (!empty($maxFileSize)) {
                    if ($fileSize > $this->convertFileSizeToBytes($maxFileSize)) {
                        throw new \Exception('s');
                    }
                }

                if (!empty($minFileSize)) {
                    if ($fileSize < $this->convertFileSizeToBytes($minFileSize)) {
                        throw new \Exception('s');
                    }
                }

                if (empty($path)) {
                    $path = 'files/';
                }

                $model->name = $fileName;
                $model->mime_type = $fileType;
                $model->path = $path;
                $model->save();

                $file->moveTo($path . $model->_id);
                return $this->response->setJsonContent((string) $model->_id);
            }
        }
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
    }

    private function convertFileSizeToBytes($size)
    {
        $size = (string) $size;
        $letters = '';
        $bytes = '';

        for ($i = 0; $i < strlen($size); $i++) {
            if (!is_numeric($size[$i])) {
                $letters .= $size[$i];
            } else {
                $bytes .= (string) $size[$i];
            }
        }

        $bytes = (int) $bytes;

        if ($letters) {
            switch (strtoupper($letters)) {
                case 'B': break;
                case 'KB': $bytes = $bytes * 1024; break;
                case 'MB': $bytes = $bytes * 1024 * 1024; break;
                case 'GB': $bytes = $bytes * 1024 * 1024 * 1024; break;
                case 'TB': $bytes = $bytes * 1024 * 1024 * 1024 * 1024; break;
                case 'PB': $bytes = $bytes * 1024 * 1024 * 1024 * 1024 * 1024; break;
            }
        }

        return $bytes;
    }
}