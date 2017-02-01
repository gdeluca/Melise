<?php

namespace Olapis\Services\Parsers;

use Httpful\Response;

class InstagramParser extends MediaParser
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
        for ($i = 0; $i < count($data); $i++) {
            $payload[$i] = $this->setPayloadValues($data[$i], $payload[$i]);
        }
        $result['location'] = $payload;
        return $result;
    }
}
