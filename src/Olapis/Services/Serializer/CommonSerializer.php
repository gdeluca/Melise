<?php

namespace Olapis\Services\Serializer;

use Httpful\Response;
use Olapis\Model\Media;
use Olapis\Model\Place;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

/**
 * Define media normalization
 * Class MediaSerializer
 * @package Olapis
 */
class CommonSerializer
{
    const JSON = 'json';

    /**
     * @var DeNormalizerService
     */
    private $deNormalizerService;

    /**
     * @var GetSetMethodNormalizer
     */
    private $normalizer;

    /**
     * @var JsonEncoder;
     */
    private $encoder;

    public function __construct()
    {
        $this->normalizer = new GetSetMethodNormalizer();
        $this->encoder = new JsonEncoder();
        $this->deNormalizerService = new DeNormalizerService();
    }

    /**
     * @return GetSetMethodNormalizer
     */
    public function getNormalizer()
    {
        return $this->normalizer;
    }

    /**
     * @return JsonEncoder
     */
    public function getEncoder()
    {
        return $this->encoder;
    }

    /**
     * @param string $rawData
     * @param string $className
     * @return Media
     */
    public function deserializeMedia($rawData, $className)
    {
        return $this
            ->deNormalizerService
            ->getMediaDeserializer()
            ->deserialize($rawData, $className, self::JSON);
    }

    /**
     * @param $rawData
     * @param $className
     * @return Place
     */
    public function deserializePlace($rawData, $className)
    {
        return $this
            ->deNormalizerService
            ->getPlaceDeserializer()
            ->deserialize($rawData, $className, self::JSON);
    }

    public static function getMediaObject($json)
    {
        $object = (new CommonSerializer())->deserializeMedia($json, 'Olapis\Model\Media');
        return $object;
    }

    public static function getPlaceObject($json)
    {
        $object = (new CommonSerializer())->deserializePlace($json, 'Olapis\Model\Place');
        return $object;
    }
}
