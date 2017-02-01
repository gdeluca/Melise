<?php

namespace Olapis\Services;

use Httpful\Request;
use Httpful\Response;
use Olapis\Services\Parsers\FoursquareParser;
use Olapis\Services\Serializer\CommonSerializer;
use Olapis\Services\Shared\Searchable;
use Olapis\Services\Shared\URLHelper;

/**
 * Class FoursquareService
 * @package Olapis\Services
 */
class FoursquareService implements Searchable
{
    const SEARCH_URL = "https://api.foursquare.com/v2/venues/search?ll={lat},{lng}&intent=browse&radius={distance}&oauth_token={token}&v={version}";

    private $codeURL;
    private $tokenURL;

    public function __construct()
    {
        $this->codeURL = "https://foursquare.com/oauth2/authenticate?client_id="
            . FOURSQUARE_CLIENT_ID . "&redirect_uri=" . REDIRECT_URL . "&response_type=code";
        $this->tokenURL = "https://foursquare.com/oauth2/access_token?client_id="
            . FOURSQUARE_CLIENT_ID . "&client_secret=" . FOURSQUARE_CLIENT_SECRET
            . "&grant_type=authorization_code&redirect_uri=" . REDIRECT_URL . "&code=" . FOURSQUARE_CODE;
    }

    /**
     * Search in service provider
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
        $params['token'] = FOURSQUARE_TOKEN;

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
        $parser = new FoursquareParser($jsonContent);
        return $parser->parse();
    }
}
