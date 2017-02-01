<?php

namespace Olapis\Model;

use JsonSerializable;

class Geopoint implements JsonSerializable
{
    /**
     * @var Float $latitude
     *
     */
    public $latitude;

    /**
     * @var Float $longitude
     *
     */
    public $longitude;

    /**
     * @return Float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param Float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return Float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param Float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @return array
     */
    public function jsonSerialize()
    {
        return array_filter(array(
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ), function ($val) {
            return !is_null($val);
        });
    }
}
