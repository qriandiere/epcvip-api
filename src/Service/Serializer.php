<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

/**
 * Class Serializer
 * @package App\Service
 */
class Serializer
{
    /** @var array */
    const SERIALIZATION_SUPPORTED_FORMAT = ['json'];
    /** @var \Symfony\Component\Serializer\Serializer $serializer */
    private $serializer;

    /**
     * SerializerService constructor.
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function __construct()
    {
        $callbackDatetime = function ($dateTime) {
            return $dateTime instanceof \DateTime ?
                [
                    'date' => $dateTime->format('Y-m-d H:i:s.u'),
                    'timezone' => $dateTime->getTimezone()->getName(),
                ] : $dateTime;
        };
        $circularReferenceHandler = function ($object) {
            return $object->getId();
        };
        $callbacks = [
            'createdAt' => $callbackDatetime,
            'modifiedAt' => $callbackDatetime,
            'dateOfBirth' => $callbackDatetime,
        ];
        $circularReferenceLimit = 1;
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer(
            $classMetadataFactory,
            null,
            null,
            null,
            null,
            null,
            [
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => $circularReferenceHandler,
                ObjectNormalizer::CIRCULAR_REFERENCE_LIMIT => $circularReferenceLimit,
                ObjectNormalizer::CALLBACKS => $callbacks
            ]
        );
        $this->serializer = new \Symfony\Component\Serializer\Serializer([$normalizer], [new JsonEncoder()]);
    }

    /**
     * @param $data
     * @param array $groups
     * @param string $format
     * @return bool|float|int|string
     * @throws \Exception
     */
    public function serialize($data, array $groups, string $format = 'json')
    {
        if (!in_array($format, self::SERIALIZATION_SUPPORTED_FORMAT))
            throw new HttpException(
                JsonResponse::HTTP_BAD_REQUEST,
                "The format '$format' is currently not supported by the serializer",
            );
        //We always return the author of a record
        if (!isset($groups['user'])) $groups[] = 'user';
        return $this->serializer->serialize($data, $format, ['groups' => $groups]);
    }

    /**
     * @param string $data
     * @param string $format
     * @return array|object
     * @throws \Exception
     */
    public function deserialize(string $data, string $format = 'json')
    {
        if (!in_array($format, self::SERIALIZATION_SUPPORTED_FORMAT))
            throw new HttpException(
                JsonResponse::HTTP_BAD_REQUEST,
                "The format '$format' is currently not supported by the serializer",
            );
        return $this->serializer->deserialize($data, $format, $format);
    }
}