<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/1/11
 * Time: 14:22
 */

namespace LianYun\Passport\Commons;

use \Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class OrmTablePrefix
{
    protected $prefix = '';
    
    public function __construct($prefix)
    {
        $this->prefix = (string)$prefix;
    }
    
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        
        if (!$classMetadata->isInheritanceTypeSingleTable()
            || $classMetadata->getName()
               === $classMetadata->rootEntityName) {
            $classMetadata->setPrimaryTable(
                [
                    'name' => $this->prefix . $classMetadata->getTableName(),
                ]
            );
        }
        
        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if ($mapping['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY && $mapping['isOwningSide']) {
                $mappedTableName                                                     = $mapping['joinTable']['name'];
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mappedTableName;
            }
        }
    }
    
}