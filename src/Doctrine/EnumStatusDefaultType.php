<?php

namespace App\Doctrine;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class EnumStatusDefaultType
 * @package App\Doctrine
 */
class EnumStatusDefaultType extends Type
{
    /** @var string */
    const ENUM_STATUS_DEFAULT = 'enum_status_default';
    /** @var string */
    const STATUS_NEW = 'new';
    /** @var string */
    const STATUS_ACTIVE = 'active';
    /** @var string */
    const STATUS_INACTIVE = 'inactive';
    /** @var string */
    const STATUS_DELETED = 'deleted';
    /** @var array */
    const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
        self::STATUS_DELETED,
    ];

    /**
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $statuses = implode(', ', self::STATUSES);
        return "ENUM($statuses)";
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!in_array($value, self::STATUSES)) {
            throw new HttpException(
                JsonResponse::HTTP_BAD_REQUEST,
                'Invalid Status'
            );
        }
        return $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::ENUM_STATUS_DEFAULT;
    }

    /**
     * @param AbstractPlatform $platform
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}