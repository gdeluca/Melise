<?php

namespace Olapis\Services\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Service for entities de-normalization
 *
 * Class DeNormalizerService
 * @package Olapis\Services\Serializer
 */
class DeNormalizerService
{
    const JSON = 'json';

    /**
     * @var MediaDeNormalizer
     */
    private $mediaResponseDeNormalizer;

    /**
     * @var MetaDeNormalizer
     */
    private $metaDeNormalizer;

    /**
     * @var LocationDeNormalizer
     */
    private $locationDeNormalizer;

    /**
     * @var GeoPointDeNormalizer
     */
    private $geoPointDeNormalizer;

    /**
     * @var PlaceDeNormalizer
     */
    private $placeDeNormalizer;

    /**
     * @return GeoPointDeNormalizer
     */
    public function getGeoPointDeNormalizer()
    {
        if ($this->geoPointDeNormalizer == null) {
            $this->geoPointDeNormalizer = new GeoPointDeNormalizer($this);
        }

        return $this->geoPointDeNormalizer;
    }

    /**
     * @return LocationDeNormalizer
     */
    public function getLocationDeNormalizer()
    {
        if ($this->locationDeNormalizer == null) {
            $this->locationDeNormalizer = new LocationDeNormalizer($this);
        }

        return $this->locationDeNormalizer;
    }

    /**
     * @return MediaDeNormalizer
     */
    public function getMediaResponseDeNormalizer()
    {
        if ($this->mediaResponseDeNormalizer == null) {
            $this->mediaResponseDeNormalizer = new MediaDeNormalizer($this);
        }

        return $this->mediaResponseDeNormalizer;
    }

    /**
     * @return MetaDeNormalizer
     */
    public function getMetaDeNormalizer()
    {
        if ($this->metaDeNormalizer == null) {
            $this->metaDeNormalizer = new MetaDeNormalizer($this);
        }

        return $this->metaDeNormalizer;
    }

    /**
     * @return PlaceDeNormalizer
     */
    public function getPlaceDeNormalizer()
    {
        if ($this->placeDeNormalizer == null) {
            $this->placeDeNormalizer = new PlaceDeNormalizer($this);
        }

        return $this->placeDeNormalizer;
    }

    /**
     * @return Serializer
     */
    public function getMediaDeserializer()
    {
        $serializer = new Serializer(
            array(new MediaDeNormalizer($this)), array(self::JSON => new JsonEncoder()));
        return $serializer;
    }

    /**
     * @return Serializer
     */
    public function getPlaceDeserializer()
    {
        $serializer = new Serializer(
            array(new PlaceDeNormalizer($this)), array(self::JSON => new JsonEncoder()));
        return $serializer;
    }
}
