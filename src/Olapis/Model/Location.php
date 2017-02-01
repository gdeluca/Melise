<?php

namespace Olapis\Model;

use JsonSerializable;

/**
 * Class Location
 * @package Olapis\Model
 */
class Location implements JsonSerializable
{
    /**
     * @var String $id
     */
    private $id;

    /**
     * @var String name
     */
    private $name;

    /**
     * @var Geopoint $geopoint
     */
    private $geopoint;

    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $country;

    /**
     * @var mixed
     */
    private $zip;

    /**
     * @var mixed
     */
    private $address;

    /**
     * @var mixed
     */
    private $distance;

    /**
     * @return String
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param String $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param String $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param mixed $distance
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
    }


    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return array_filter(array(
            'id' => $this->id,
            'geoPoint' => $this->getGeopoint()->jsonSerialize(),
            'name' => $this->name,
            'street' => $this->street,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'zip' => $this->zip,
            'address' => $this->address,
            'distance' => $this->distance,
        ), function ($val) {
            return !is_null($val);
        });
    }

    /**
     * @return Geopoint
     */
    public function getGeopoint()
    {
        return $this->geopoint;
    }

    /**
     * @param Geopoint $geopoint
     */
    public function setGeopoint($geopoint)
    {
        if ($this->geopoint == null) {
            $this->geopoint = new Geopoint();
        }
        $this->geopoint = $geopoint;
    }
}
