<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Media\Db\Mapping;

use Vegas\Db\MappingInterface;
use Vegas\Media\Db\FileInterface;
use Vegas\Media\File\Decorator;
use Vegas\Media\File\Exception;

/**
 * Class File
 *
 * Usage
 *
 * Add somewhere in \Bootstrap class
 * <code>
 * $mappingManager = new MappingManager();
 * $mappingManager->add(new \Vegas\Media\Db\Mapping\File());
 * </code>
 *
 * Add mappings definition to model
 * <code>
 * class Foo extends CollectionAbstract
 * {
 *  ....
 *  protected $mappings = array('image' => 'file');
 *  ....
 * }
 * </code>
 *
 * Read mapped values
 * <code>
 * $foo = Foo::findFirst();
 * echo $foo->readMapped('image')->offsetGet(0)->getPath();
 * echo $foo->readMapped('image')[0]->getUrl();
 * </code>
 *
 * @package Vegas\Media\Db\Mapping
 */
class File implements MappingInterface
{
    /**
     * Class represents model
     *
     * @var FileInterface
     */
    private $fileModel;

    /**
     * Constructor
     * Sets model
     *
     * @param FileInterface $fileModel
     */
    public function __construct(FileInterface $fileModel)
    {
        $this->fileModel = $fileModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(& $value)
    {
        $extraData = array();
        $files = array();
        if (is_array($value)) {
            if (!empty($value['file_id'])) {
                $extraDataTmp = array();
                foreach ($value as $extraDataKey => $extraDataValue) {
                    if ($extraDataKey !== 'file_id') {
                        $extraDataTmp[$extraDataKey] = $extraDataValue;
                    }
                }

                $extraData = $extraDataTmp;
                $files[] = call_user_func(array($this->fileModel, 'findById'), $value['file_id']);
            } else {
                foreach ($value as $index => $file) {
                    if (!empty($file['file_id'])) {
                        $modelFile = call_user_func(array($this->fileModel, 'findById'), $file['file_id']);

                        $extraDataTmp = array();
                        foreach ($file as $extraDataKey => $extraDataValue) {
                            if ($extraDataKey !== 'file_id') {
                                $extraDataTmp[$extraDataKey] = $extraDataValue;
                            }
                        }

                        $extraData[$index] = $extraDataTmp;
                        $files[] = $modelFile;
                    }
                }
            }
        }

        $decoratedFiles = new \ArrayObject();
        foreach ($files as $index => $file) {
            if (!$file instanceof $this->fileModel) {
                continue;
            }


            $decoratedFiles->append(new Decorator($file, $extraData[$index]));
        }
        $value = $decoratedFiles;

        return $value;
    }
}