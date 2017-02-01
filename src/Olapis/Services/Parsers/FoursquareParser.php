<?php

namespace Olapis\Services\Parsers;

use Httpful\Response;

class FoursquareParser extends Parser
{
    public function __construct($json)
    {
        parent::__construct($json);
    }

    public function populatePayload($result)
    {
        $data = $this->getData();
        $payload = [];
        for ($i = 0; $i < count($data); $i++) {
            $payload[$i]['name'] = $data[$i]['name'];
            $payload[$i]['id'] = $data[$i]['id'];
            $payload[$i]['address'] = $data[$i]['location']['address'];
            $payload[$i]['street'] = $data[$i]['location']['crossStreet'];
            $payload[$i]['zip'] = $data[$i]['location']['cc'];
            $payload[$i]['city'] = $data[$i]['location']['city'];
            $payload[$i]['state'] = $data[$i]['location']['state'];
            $payload[$i]['country'] = $data[$i]['location']['country'];
            $payload[$i]['distance'] = $data[$i]['location']['distance'];
            $payload[$i]['geopoint']['latitude'] = $data[$i]['location']['lat'];
            $payload[$i]['geopoint']['longitude'] = $data[$i]['location']['lng'];
        }
        $result['location'] = $payload;
        return $result;
    }

    public function getData()
    {
        return $this->source['body']['response']['venues'];
    }

    /**
     * @param $result
     * @return mixed
     */
    public function populateErrors($result)
    {
        $meta = $this->source['body']['meta'];
        $result['meta']['message'] = $meta['errorDetail'];
        $result['meta']['type'] = $meta['errorType'];
        $result['meta']['errorCode'] = $meta['code'];
        return $result;
    }

    public function hasData()
    {
        return isset($this->source['body']['response']['venues']);
    }
}
