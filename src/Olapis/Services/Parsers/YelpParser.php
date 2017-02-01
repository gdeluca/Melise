<?php

namespace Olapis\Services\Parsers;

use Httpful\Response;

class YelpParser extends Parser
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
            $payload[$i]['city'] = $data[$i]['location']['city'];
            $payload[$i]['name'] = $data[$i]['name'];
            $payload[$i]['id'] = $data[$i]['id'];
            $payload[$i]['address'] = implode(", ", $data[$i]['location']['display_address']);
            $payload[$i]['zip'] = $data[$i]['location']['postal_code'];
            $payload[$i]['state'] = $data[$i]['location']['state_code'];
            $payload[$i]['country'] = $data[$i]['location']['country_code'];
            $payload[$i]['distance'] = $data[$i]['location']['geo_accuracy'];
            $payload[$i]['geopoint']['latitude'] = $data[$i]['location']['coordinate']['latitude'];
            $payload[$i]['geopoint']['longitude'] = $data[$i]['location']['coordinate']['longitude'];
        }
        $result['location'] = $payload;
        return $result;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->source['body']['businesses'];
    }

    /**
     * @param $result
     * @return mixed
     */
    public function populateErrors($result)
    {
        $meta = $this->source['body']['error'];
        $result['meta']['message'] = $meta['text'];
        $result['meta']['type'] = $meta['id'];
        $result['meta']['errorCode'] = $meta['field'];
        return $result;
    }

    public function hasData()
    {
        return isset($this->source['body']['businesses']);
    }
}
