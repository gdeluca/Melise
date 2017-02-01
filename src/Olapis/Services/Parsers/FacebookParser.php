<?php

namespace Olapis\Services\Parsers;

use Httpful\Response;

class FacebookParser extends Parser
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
            $payload[$i]['street'] = $data[$i]['location']['street'];
            $payload[$i]['city'] = $data[$i]['location']['city'];
            $payload[$i]['state'] = $data[$i]['location']['state'];
            $payload[$i]['country'] = $data[$i]['location']['country'];
            $payload[$i]['zip'] = $data[$i]['location']['zip'];
            $payload[$i]['geopoint']['latitude'] = $data[$i]['location']['latitude'];
            $payload[$i]['geopoint']['longitude'] = $data[$i]['location']['longitude'];
        }
        $result['location'] = $payload;
        return $result;
    }

    public function getData()
    {
        return $this->source['body']['data'];
    }

    public function populateErrors($result)
    {
        $error = $this->source['body']['error'];
        $result['meta']['message'] = $error['message'];
        $result['meta']['type'] = $error['type'];
        $result['meta']['errorCode'] = $error['code'];
        return $result;
    }

    public function hasData()
    {
        return isset($this->source['body']['data']);
    }
}
