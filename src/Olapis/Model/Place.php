<?php

namespace Olapis\Model;

use JsonSerializable;

/**
 * Place definition
 * @package Olapis\Services
 */
class Place implements JsonSerializable
{
    const INSTAGRAM = "Instagram";
    const FACEBOOK = "Facebook";
    const FOURSQUARE = "Foursquare";
    const YELP = "Yelp";

    /**
     * @var Meta
     */
    private $meta;

    /**
     * @var array
     */
    private $locations;

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        $locations = [];
        for ($i = 0; $i < count($this->getLocations()); $i++) {
            /** @var  Location $location */
            $location = $this->getLocations()[$i];
            $locations[$i] = $location->jsonSerialize();
        }

        return array(
            'meta' => $this->getMeta()->jsonSerialize(),
            'locations' => $locations,
        );
    }

    /**
     * @return array
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * @param array $locations
     */
    public function setLocations($locations)
    {
        $this->locations = $locations;
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
