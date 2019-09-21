<?php

namespace App\Doctrine;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Class Filter
 * @package App\Doctrine
 */
class Filter extends SQLFilter
{
    /** @var string $targetEntity */
    private $targetEntity;

    /**
     * @param ClassMetaData $targetEntity
     * @param string $targetTableAlias
     * @return string
     * We don't want to return deleted element
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->hasField('status')) {
            $this->setParameter('deleted', EnumStatusDefaultType::STATUS_DELETED);
            $deleted = $this->getParameter('deleted');
            $filter = "$targetTableAlias.status != $deleted";
            return $filter;
        }
        return '';
    }
}