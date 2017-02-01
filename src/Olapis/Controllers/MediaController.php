<?php

namespace Olapis\Controllers;

use Olapis\Model\Media;
use Olapis\Model\Place;
use Olapis\PrettyJsonResponse;
use Olapis\Services\FacebookService;
use Olapis\Services\FoursquareService;
use Olapis\Services\InstagramService;
use Olapis\Services\YelpService;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MediaController
 * @package Olapis\Controllers
 */
class MediaController
{
    /**
     * @var InstagramService
     */
    private $instagramService;

    /**
     * @var FacebookService
     */
    private $facebookService;

    /**
     * @var FoursquareService
     */
    private $foursquareService;

    /**
     * @var YelpService
     */
    private $yelpService;


    public function __construct()
    {
        $this->instagramService = new InstagramService();
        $this->facebookService = new FacebookService(FACEBOOK_CLIENT_ID, FACEBOOK_CLIENT_SECRET);
        $this->foursquareService = new FoursquareService();
        $this->yelpService = new YelpService(YELP_CONSUMER_KEY, YELP_CONSUMER_SECRET, YELP_TOKEN, YELP_TOKEN_SECRET);
    }

    /**
     * Get an html response for media location information
     * @param $id
     * @param Request $request
     * @param Application $app
     * @return JsonResponse
     */
    public function getLocationResponse($id, Request $request, Application $app)
    {
        $location = $this->getLocation($id, $request->query->all());
        $response = PrettyJsonResponse::create($location);
        return $response;
    }

    /**
     *  Get a media location information from Instagram service
     * @param mixed $id
     * @param array $params
     * @return Media
     */
    public function getLocation($id, $params)
    {
        return $this->instagramService->getMedia($id, $params);
    }

    /**
     * Get an html response for the media location and all the regional location info
     * @param $id
     * @param Request $request
     * @param Application $app
     * @return JsonResponse
     */
    public function getRegionalLocationResponse($id, Request $request, Application $app)
    {
        $location = $this->getRegionalLocation($id, $request->query->all());
        $response = PrettyJsonResponse::create($location);
        return $response;
    }

    /**
     * Get the media location and all the regional location info
     * @param $id
     * @param $params
     * @return Media
     */
    public function getRegionalLocation($id, $params)
    {
        /** @var Media $media */
        $media = $this->instagramService->getMedia($id, $params);

        if ($media->getMeta()->getCode() >= 400) {
            return $media;
        }

        // override default values
        if (isset($params['distance'])) {
            $distance = $params['distance'];
        } else {
            $distance = 1000;
        }

        $location = $media->getLocation()->getName();
        if (isset($params['location'])) {
            $location = $params['location'];
        }

        $lat = $media->getLocation()->getGeopoint()->getLatitude();
        $lng = $media->getLocation()->getGeopoint()->getLongitude();
        $response = [];

        $response[Place::FACEBOOK] = $this->facebookService->search(['lat' => $lat, 'lng' => $lng, 'distance' => $distance]);

        $response[Place::INSTAGRAM] = $this->instagramService->search(['lat' => $lat, 'lng' => $lng, 'location' => $location]);

        $version = 20150520;
        $response[Place::FOURSQUARE] = $this->foursquareService->search(['lat' => $lat, 'lng' => $lng,
            'location' => $location, 'distance' => $distance, 'version' => $version]);

        $response[Place::YELP] = $this->yelpService->search(['lat' => $lat, 'lng' => $lng,
            'location' => $location, 'radius_filter' => 00]);

        $media->setPlaces($response);

        return $media;
    }

    /**
     * @param InstagramService $instagramService
     */
    public function setInstagramService($instagramService)
    {
        $this->instagramService = $instagramService;
    }

    /**
     * @param FacebookService $facebookService
     */
    public function setFacebookService($facebookService)
    {
        $this->facebookService = $facebookService;
    }

    /**
     * @param FoursquareService $foursquareService
     */
    public function setFoursquareService($foursquareService)
    {
        $this->foursquareService = $foursquareService;
    }

    /**
     * @param YelpService $yelpService
     */
    public function setYelpService($yelpService)
    {
        $this->yelpService = $yelpService;
    }
}
