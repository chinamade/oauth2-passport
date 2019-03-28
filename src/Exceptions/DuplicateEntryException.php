<?php
/**
 * Created by PhpStorm.
 * User: minhao
 * Date: 2016-06-16
 * Time: 15:18
 */

namespace LianYun\Passport\Exceptions;

use Doctrine\ORM\ORMException;

class DuplicateEntryException extends ORMException
{
    protected $entryType;
    protected $identifiers;

    /**
     * Static constructor.
     *
     * @param string         $className
     * @param mixed|string[] $ids
     *
     * @return static
     */
    public static function fromClassNameAndIdentifier($className, $ids)
    {
        if (!is_array($ids)) {
            $ids = [
                'id' => $ids,
            ];
        }

        $ret = new static(
            "Entity of type '$className', identified by " . json_encode($ids) . ", is duplicate."
        );

        $ret->entryType   = $className;
        $ret->identifiers = $ids;

        return $ret;
    }

    /**
     * Typically fully qualified class name.
     *
     * @return string
     */
    public function getEntryType()
    {
        return $this->entryType;
    }

    /**
     * Array in the form of 'field' => 'value'
     *
     * @return array
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }
}
