<?php

namespace Olapis\Services\Parsers;

use Httpful\Response;

class MediaParser extends Parser
{
    public function __construct($json)
    {
        parent::__construct($json);
    }

    /**
     * @param array $result
     * @return array
     */
    public function populatePayload($result)
    {
        $data = $this->getData();
        $payload = [];
        $payload = $this->setPayloadValues($data['location'], $payload);
        $result['location'] = $payload;
        return $result;
    }

    public function getData()
    {
        return $this->source['body']['data'];
    }

    /**
     * @param array $data
     * @param array $payload
     * @return array
     */
    protected function setPayloadValues($data, $payload)
    {
        $payload['name'] = $data['name'];
        $payload['id'] = $data['id'];
        $payload['geopoint']['latitude'] = $data['latitude'];
        $payload['geopoint']['longitude'] = $data['longitude'];
        return $payload;
    }

    public function populateErrors($result)
    {
        $meta = $this->source['body']['meta'];
        $result['meta']['message'] = $meta['error_message'];
        $result['meta']['type'] = $meta['error_type'];
        $result['meta']['errorCode'] = $meta['code'];
        return $result;
    }

    /**
     * @return bool
     */
    public function hasData()
    {
        return isset($this->source['body']['data']);
    }
}
