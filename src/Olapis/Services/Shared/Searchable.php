<?php

namespace Olapis\Services\Shared;

use Olapis\Model\Place;

interface Searchable
{
    /**
     * Search for instagram media by coordinates, returns a Place object
     * @param array $params
     * @return Place
     */
    public function search($params);

    /**
     * Raw response from search query
     * @param $params
     * @return string
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function rawSearch($params);

    /**
     * Parse json result into a Place object
     * @param $jsonContent
     * @return array
     */
    public function parseSearch($jsonContent);
}
