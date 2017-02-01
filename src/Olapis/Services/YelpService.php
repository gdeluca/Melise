<?php

namespace Olapis\Services;

use Httpful\Request;
use Httpful\Response;
use OAuth\OAuth1\OAuthConsumer;
use OAuth\OAuth1\OAuthRequest;
use OAuth\OAuth1\OAuthSignatureMethodHMACSHA1;
use OAuth\OAuth1\OAuthToken;
use Olapis\Services\Parsers\YelpParser;
use Olapis\Services\Serializer\CommonSerializer;
use Olapis\Services\Shared\Searchable;
use Olapis\Services\Shared\URLHelper;

/**
 * Class YelpService
 * @package Olapis\Services
 */
class YelpService implements Searchable
{
    const SEARCH_URL = "http://api.yelp.com/v2/search?location={location}";

    private $consumer;
    private $token;
    private $signature_method;

    private $unsignedURL = false;

    /**
     * @param $consumer_key
     * @param $consumer_secret
     * @param $token
     * @param $token_secret
     */
    public function __construct($consumer_key, $consumer_secret, $token, $token_secret)
    {
        $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret);
        $this->token = new OAuthToken($token, $token_secret);
        $this->signature_method = new OAuthSignatureMethodHMACSHA1();
    }

    /**
     * Build OAuth Request using the OAuth PHP library. Uses the consumer and token object created above.
     * @param $unsigned_url
     * @return string
     */
    private function signURL($unsigned_url)
    {
        $oauthRequest = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, 'GET', $unsigned_url);
        $oauthRequest->sign_request($this->signature_method, $this->consumer, $this->token);
        $signed_url = $oauthRequest->to_url();
        return $signed_url;
    }

    /**
     * Search in yelp service provider
     * Location can be any type of search term. Zip, city, street address, etc.
     * Terms contains the search string of what you're looking for,
     * like 'food''insurance agencies', 'department stores'. Can be an array.
     * @param array $params
     * @return \Httpful\mixed|string
     * @throws \Httpful\Exception\ConnectionErrorException
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
        if (!isset($params['limit']) || $params['limit'] > 10) {
            $params['limit'] = 10;
        }

//      Generates the unsigned URL for our API call.
        $uri = self::SEARCH_URL;
        $this->unsignedURL = URLHelper::populateParameters($params, $uri);
        $signed_url = $this->signURL($this->unsignedURL);
        /** @var Response $result */
        $result = Request::get($signed_url)->send();
        return URLHelper::buildJsonFromHttpFulResponse($result->meta_data, $result->body);
    }

    /**
     * Parse json result into a Place object
     * @param $jsonContent
     * @return array
     */
    public function parseSearch($jsonContent)
    {
        $parser = new YelpParser($jsonContent);
        return $parser->parse();
    }
}
