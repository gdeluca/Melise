<?php

namespace Olapis\Services\Shared;

use Olapis\Model\Media;

interface MediaService
{
    /**
     * Find media by id, returns a Media object
     * @param mixed $mediaId
     * @param array $urlParams
     * @return Media
     */
    public function getMedia($mediaId, $urlParams);

    /**
     * Raw response from media query
     * @param $mediaId
     * @param $params
     * @return string
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getRawMedia($mediaId, $params);

    /**
     *
     * Parse json result into a Media object
     * @param $jsonContent
     * @return array
     */
    public function parseMedia($jsonContent);
}
