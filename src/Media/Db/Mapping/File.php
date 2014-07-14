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
     * @var
     */
    private $modelClass;

    /**
     * Constructor
     * Sets model
     *
     * @param $modelClass
     */
    public function __construct($modelClass)
    {
        $this->modelClass = $modelClass;
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
        $files = array();
        if (is_array($value)) {
            $files = $this->resolveArray($value);
        }

        $decoratedFiles = new \ArrayObject();
        foreach ($files as $file) {
            if (!$file instanceof $this->modelClass) {
                continue;
            }
            $decoratedFiles->append(new Decorator($file));
        }
        $value = $decoratedFiles;

        return $value;
    }

    /**
     * @param array $value
     * @return array
     */
    private function resolveArray(array $value)
    {
        $files = array();

        if (!empty($value['file_id'])) {
            $files[] = call_user_func(array($this->modelClass, 'findById'), $value['file_id']);
        } else {
            foreach ($value as $file) {
                if (!empty($file['file_id'])) {
                    $files[] = call_user_func(array($this->modelClass, 'findById'), $file['file_id']);
                }
            }
        }

        return $files;
    }
}