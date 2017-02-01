<?php

namespace Olapis\Services;

use Httpful\Request;
use Httpful\Response;
use Olapis\Services\Parsers\FacebookParser;
use Olapis\Services\Serializer\CommonSerializer;
use Olapis\Services\Shared\Searchable;
use Olapis\Services\Shared\URLHelper;

/**
 * Class FacebookService
 * @package Olapis\Services
 */
class FacebookService implements Searchable
{
    const SEARCH_URL = "https://graph.facebook.com/search?type=place&center={lat},{lng}&distance={distance}&access_token={token}";

    /**
     * @var string
     */
    private $tokenUrl;

    /**
     * @param $clientId
     * @param $clientSecret
     */
    public function __construct($clientId, $clientSecret)
    {
        $this->tokenUrl = "https://graph.facebook.com/oauth/access_token?client_id="
            . $clientId . "&client_secret="
            . $clientSecret . "&grant_type=client_credentials";
    }


    /**
     * Search in facebook service provider
     * @param array $params
     * @return string
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
        $uri = self::SEARCH_URL;
        $params['token'] = self::getToken();

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
        $parser = new FacebookParser($jsonContent);
        return $parser->parse();
    }

    /**
     * Calculate a token for the service
     * @return mixed
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getToken()
    {
        /** @var Response $response */
        $response = Request::get($this->tokenUrl)->send();
        return explode("=", $response->body)[1];
    }
}
