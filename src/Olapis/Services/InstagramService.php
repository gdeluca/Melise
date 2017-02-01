<?php

namespace Olapis\Services;

use Httpful\Request;
use Httpful\Response;
use Olapis\Model\Media;
use Olapis\Services\Parsers\InstagramParser;
use Olapis\Services\Parsers\MediaParser;
use Olapis\Services\Serializer\CommonSerializer;
use Olapis\Services\Shared\MediaService;
use Olapis\Services\Shared\Searchable;
use Olapis\Services\Shared\URLHelper;

/**
 * Class InstagramService
 * @package Olapis\Services
 */
class InstagramService implements MediaService, Searchable
{
    const SEARCH_MEDIA_URL = "https://api.instagram.com/v1/media/{id}?access_token={token}";
    const SEARCH_URL = "https://api.instagram.com/v1/locations/search?lat={lat}&lng={lng}&access_token={token}";

    public $codeURL;

    public function __construct()
    {
        $this->codeURL = "https://instagram.com/OAuth/authorize/?client_id="
            . INSTAGRAM_CLIENT_ID . "&redirect_uri=" . REDIRECT_URL . "&response_type=code";
    }

    /**
     * Search for instagram media by coordinates, returns a Place object
     * @param array $params
     * @return \Olapis\Model\Place
     */
    public function search($params)
    {
        $rawResult = $this->rawSearch($params);
        $parsedResult = $this->parseSearch($rawResult);
        $responseObject = CommonSerializer::getPlaceObject($parsedResult);
        return $responseObject;
    }

    /**
     * Raw response from search query
     * @param $params
     * @return string
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function rawSearch($params)
    {
        $uri = InstagramService::SEARCH_URL;
        $params['token'] = INSTAGRAM_TOKEN;
        $uri = URLHelper::populateParameters($params, $uri);
        /** @var Response $result */
        $result = Request::get($uri)->send();
        return URLHelper::buildJsonFromHttpFulResponse($result->meta_data, $result->body);
    }

    /**
     * Parse json result into a Place object
     * @param $jsonContent
     * @return array
     */
    public function parseSearch($jsonContent)
    {
        $parser = new InstagramParser($jsonContent);
        return $parser->parse();
    }

    /**
     * Find media by id, returns a Media object
     * @param string $mediaId
     * @param array $urlParams
     * @return Media
     */
    public function getMedia($mediaId, $urlParams)
    {
        $rawResult = $this->getRawMedia($mediaId, $urlParams);
        $parsedResult = $this->parseMedia($rawResult);
        $responseObject = CommonSerializer::getMediaObject($parsedResult);
        return $responseObject;
    }

    /**
     * Raw response from media query
     * @param string $mediaId
     * @param array $params
     * @return string
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getRawMedia($mediaId, $params)
    {
        $uri = InstagramService::SEARCH_MEDIA_URL;
        $params['id'] = $mediaId;
        $params['token'] = INSTAGRAM_TOKEN;
        $uri = URLHelper::populateParameters($params, $uri);

        /** @var Response $result */
        $result = Request::get($uri)->send();
        return URLHelper::buildJsonFromHttpFulResponse($result->meta_data, $result->body);
    }

    /**
     *
     * Parse json result into a Media object
     * @param $jsonContent
     * @return array
     */
    public function parseMedia($jsonContent)
    {
        $parser = new MediaParser($jsonContent);
        return $parser->parse();
    }
}
