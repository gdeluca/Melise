<?php

namespace Olapis\Services\Parsers;

abstract class Parser
{
    /**
     * @var array
     */
    protected $source;

    public function __construct($source)
    {
        $this->source = json_decode($source, true);
    }

    /**
     * Remove information that is not related to location
     * @return array
     */
    public function parse()
    {
        $result = [];
        $result = $this->populateMeta($result);
        if ($this->hasData()) {
            $result = $this->populatePayload($result);
        }
        if ($this->hasErrors()) {
            $result = $this->populateErrors($result);
        }
        return json_encode($result);
    }

    /**
     * @param array $result
     * @return array
     */
    public function populateMeta($result)
    {
        if (isset($this->source['meta']['http_code'])) {
            $result['meta']['code'] = $this->source['meta']['http_code'];
        }
        $result['meta']['content_type'] = $this->source['meta']['content_type'];
        return $result;
    }

    /**
     * @return bool
     */
    abstract public function hasData();

    /**
     * @param array $result
     * @return array
     */
    abstract public function populatePayload($result);

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return $this->source['meta']['http_code'] >= 400;
    }

    /**
     * @param $result
     * @return array
     */
    abstract public function populateErrors($result);

    /**
     * @return array
     */
    abstract public function getData();
}
