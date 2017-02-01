<?php

namespace Olapis\Services\Serializer;

use Olapis\Model\Location;
use Olapis\Model\Media;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * De-normalize a media response
 * Class MediaResponseDeNormalizer
 * @package Olapis\Services\Serializer
 */
class MediaDeNormalizer implements DenormalizerInterface
{
    /**
     * @var DeNormalizerService $deNormalizerService
     */
    private $deNormalizerService;

    public function __construct($deNormalizerService)
    {
        $this->deNormalizerService = $deNormalizerService;
    }

    /**
     * @return DeNormalizerService
     */
    public function getDeNormalizerService()
    {
        return $this->deNormalizerService;
    }


    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed $data data to restore
     * @param string $class the expected class to instantiate
     * @param string $format format the given data was extracted from
     * @param array $context options available to the denormalizer
     *
     * @return object
     */
    /**
     * @param mixed $data
     * @param string $class
     * @param null $format
     * @param array $context
     * @return Location
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $object = new Media();
        if (isset($data['location'])) {
            $object->setLocation($this->getDeNormalizerService()
                ->getLocationDeNormalizer()
                ->denormalize($data['location'], 'Olapis\Model\Location', $format, $context));
        }
        if (isset($data['meta'])) {
            $object->setMeta($this->getDeNormalizerService()
                ->getMetaDeNormalizer()
                ->denormalize($data['meta'], 'Olapis\Model\Meta', $format, $context));
        }
        return $object;
    }

    /**
     * Checks whether the given class is supported for denormalization by this normalizer.
     *
     * @param mixed $data Data to denormalize from.
     * @param string $type The class to which the data should be denormalized.
     * @param string $format The format being deserialized from.
     *
     * @return bool
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return true;
    }
}
