<?php

namespace Olapis\Model;

use JsonSerializable;

class Media implements JsonSerializable
{
    /**
     * @var Meta
     */
    private $meta;

    /**
     * @var Location
     */
    private $location;

    /**
     * @var array
     */
    private $places;

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        $placesConstants = [Place::FACEBOOK, Place::FOURSQUARE, Place::INSTAGRAM, Place::YELP];
        $places = null;
        $location = null;
        if ($this->getPlaces() != null) {
            $places = [];
            foreach ($placesConstants as $p) {
                /** @var  place $place */
                $place = $this->getPlaces()[$p];
                if ($place != null) {
                    $places[$p] = $place->jsonSerialize();
                }
            }
        }
        if ($this->getLocation() != null) {
            $location = $this->getLocation()->jsonSerialize();
        }

        return array_filter(array(
            'meta' => $this->getMeta()->jsonSerialize(),
            'location' => $location,
            'places' => $places,
        ), function ($val) {
            return !is_null($val);
        });
    }

    /**
     * @return array
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * @param array $places
     */
    public function setPlaces($places)
    {
        $this->places = $places;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return Meta
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param Meta $meta
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
    }
}
