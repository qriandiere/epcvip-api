<?php

namespace App\Doctrine;

use function Couchbase\defaultDecoder;

/**
 * Class NamingStrategy
 * @package App\Doctrine
 */
class NamingStrategy implements \Doctrine\ORM\Mapping\NamingStrategy
{
    /**
     * @param string $className
     * @return string
     * We use singular for our entities, because one entity represent one row
     * (example : Customer represent one customer), but plural for our tables, because one table represent many rows
     * (example : customers represent all of our customers)
     * We also keep camelCase in our table names to be coherent with our columns naming.
     */
    public function classToTableName($className)
    {
        if (strpos($className, '\\') !== false) {
            $className = substr($className, strrpos($className, '\\') + 1);
        }
        return $this->camelCase($className, true);
    }

    /**
     * @param string $propertyName
     * @param null $className
     * @return string
     * No modification to do, our column are in camelCase just like our entity properties
     */
    public function propertyToColumnName($propertyName, $className = null)
    {
        return $this->camelCase($propertyName, false);
    }

    /**
     * @param string $propertyName
     * @param string $embeddedColumnName
     * @param null $className
     * @param null $embeddedClassName
     * @return string
     */
    public function embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className = null, $embeddedClassName = null)
    {
        return $this->camelCase($propertyName, false) . ucfirst($embeddedColumnName);
    }

    public function referenceColumnName()
    {
        return 'id';
    }

    /**
     * @param string $propertyName
     * @return string
     */
    public function joinColumnName($propertyName)
    {
        return $this->camelCase($propertyName, false) . $this->referenceColumnName();
    }

    /**
     * @param string $sourceEntity
     * @param string $targetEntity
     * @param null $propertyName
     * @return string|void
     */
    public function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
    {
        return $this->classToTableName($sourceEntity) . ucfirst($this->classToTableName($targetEntity));
    }

    /**
     * @param string $entityName
     * @param null $referencedColumnName
     * @return string|void
     */
    public function joinKeyColumnName($entityName, $referencedColumnName = null)
    {
        return $this->classToTableName($entityName) . ($referencedColumnName ?: ucfirst($this->referenceColumnName()));
    }

    /**
     * @param string $string
     * @param bool $plural
     * @return string
     */
    private function camelCase(string $string, bool $plural)
    {
        $string = lcfirst($string);
        return $plural ? $string . 's' : $string;
    }
}