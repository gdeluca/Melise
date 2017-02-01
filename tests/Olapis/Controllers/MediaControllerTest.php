<?php

namespace Olapis\Controllers;

use Olapis\Model\Location;
use Olapis\Model\Media;
use Olapis\Model\Place;
use PHPUnit_Framework_TestCase;

/**
 * Provides functionality test for media endopints.
 * Class MediaControllerTest
 * @package Olapis\Controllers
 *
 */
class MediaControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $mediaResponse
     * @param string $mediasResponse
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getInstagramMediaMock($mediaResponse, $mediasResponse)
    {
        $mockedMediaResult = null;
        $mockedMediasResult = null;
        if (isset($mediaResponse)) {
            $mockedMediaResult = file_get_contents($mediaResponse);
        }
        if (isset($mediasResponse)) {
            $mockedMediasResult = file_get_contents($mediasResponse);
        }

        // set mock
        $serviceMock = $this
            ->getMockBuilder("\Olapis\Services\InstagramService")
            ->setMethods(array('getRawMedia', 'rawSearch'))
            ->getMock();
        $serviceMock
            ->expects($this->once())
            ->method('getRawMedia')
            ->will($this->returnValue($mockedMediaResult));
        $serviceMock
            ->expects(parent::any())
            ->method('rawSearch')
            ->will($this->returnValue($mockedMediasResult));

        return $serviceMock;
    }

    /**
     * Common method to mock search request
     * @param string $mockedDataFile
     * @param string $serviceName
     * @param array $constructorArgs
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getSearchMock($mockedDataFile, $serviceName, $constructorArgs = [])
    {
        $mockedResult = file_get_contents($mockedDataFile);

        // set mock
        $serviceBuilderMock = $this
            ->getMockBuilder("\Olapis\Services\\$serviceName")
            ->setMethods(array('rawSearch'));
        if (!empty($constructorArgs)) {
            $serviceBuilderMock->setConstructorArgs($constructorArgs);
        }
        $serviceMock = $serviceBuilderMock->getMock();
        $serviceMock
            ->expects($this->once())
            ->method('rawSearch')
            ->will($this->returnValue($mockedResult));

        return $serviceMock;
    }

    /**
     * Test instagram media by id
     */
    public function testMediaByIdHappyPath()
    {
        $id = '983529485487949611_5072160';
        $controller = new MediaController();
        $mediaSource = dirname(__DIR__). "/resources/instagram_find_by_id.json";
        $mediasSource = dirname(__DIR__). "/resources/instagram_search_medias.json";
        $controller->setInstagramService($this->getInstagramMediaMock($mediaSource, $mediasSource));
        $mediaResponse = $controller->getLocation($id, []);

        $this->assertMedia($mediaResponse);
    }

    /**
     * Query all information related to an instagram media
     */
    public function testRegionalDataHappyPath()
    {
        $id = "983529485487949611_5072160";
        $constructorArgs = [FACEBOOK_CLIENT_ID, FACEBOOK_CLIENT_SECRET];
        $facebookMock = $this->getSearchMock(dirname(__DIR__). "/resources/facebook_search_places.json", "FacebookService", $constructorArgs);
        $foursquareMock = $this->getSearchMock(dirname(__DIR__)."/resources/foursquare_search_venues.json", "FoursquareService");
        $constructorArgs = [YELP_CONSUMER_KEY, YELP_CONSUMER_SECRET, YELP_TOKEN, YELP_TOKEN_SECRET];
        $yelpMock = $this->getSearchMock(dirname(__DIR__). "/resources/yelp_search_businesses.json", "YelpService", $constructorArgs);

        // controller instance
        $mediaController = new MediaController();

        // set mock data
        $mediaSource = dirname(__DIR__). "/resources/instagram_find_by_id.json";
        $mediasSource = dirname(__DIR__). "/resources/instagram_search_medias.json";
        $instagramMock = $this->getInstagramMediaMock($mediaSource, $mediasSource);
        $mediaController->setInstagramService($instagramMock);
        $mediaController->setFacebookService($facebookMock);
        $mediaController->setFoursquareService($foursquareMock);
        $mediaController->setYelpService($yelpMock);

        // cal service to test
        $regionalData = $mediaController->getRegionalLocation($id, []);

        // assertions
        $this->assertMedia($regionalData);
        $this->assertEquals(count($regionalData->getPlaces()), 4);

        /** @var Place $facebook */
        $facebook = $regionalData->getPlaces()[Place::FACEBOOK];
        $this->assertEquals($facebook->getMeta()->getCode(), 200);
        $this->assertEquals(count($facebook->getLocations()), 25);
        /** @var Location $fbLocation */
        $fbLocation = $facebook->getLocations()[0];
        $this->assertEquals($fbLocation->getId(), 130682413663773);
        $this->assertEquals($fbLocation->getName(), 'Chez Françoise');
        $this->assertEquals($fbLocation->getGeopoint()->getLatitude(), 48.861875368208);
        $this->assertEquals($fbLocation->getGeopoint()->getLongitude(), 2.3144168456087);
        $this->assertEquals($fbLocation->getStreet(), ' 2 rue Fabert - Aérogare des Invalides');
        $this->assertEquals($fbLocation->getCity(), 'Paris');
        $this->assertEquals($fbLocation->getState(), '');
        $this->assertEquals($fbLocation->getCountry(), 'France');
        $this->assertEquals($fbLocation->getZip(), '75007');

        /** @var Place $instagram */
        $instagram = $regionalData->getPlaces()[Place::INSTAGRAM];
        $this->assertEquals($instagram->getMeta()->getCode(), 200);
        $this->assertEquals(count($instagram->getLocations()), 20);
        /** @var Location $igLocation */
        $igLocation = $instagram->getLocations()[0];
        $this->assertEquals($igLocation->getId(), 259540571);
        $this->assertEquals($igLocation->getName(), 'Air France Invalides');
        $this->assertEquals($igLocation->getGeopoint()->getLatitude(), 48.862065309);
        $this->assertEquals($igLocation->getGeopoint()->getLongitude(), 2.314750969);

        /** @var Place $foursquare */
        $foursquare = $regionalData->getPlaces()[Place::FOURSQUARE];
        $this->assertEquals($foursquare->getMeta()->getCode(), 200);
        $this->assertEquals(count($foursquare->getLocations()), 30);
        /** @var Location $fqLocation */
        $fqLocation = $foursquare->getLocations()[0];
        $this->assertEquals($fqLocation->getId(), '4b30f9bbf964a5208ffd24e3');
        $this->assertEquals($fqLocation->getName(), 'Jardin des Tuileries');
        $this->assertEquals($fqLocation->getGeopoint()->getLatitude(), 48.863649);
        $this->assertEquals($fqLocation->getGeopoint()->getLongitude(), 2.326902);
        $this->assertEquals($fqLocation->getStreet(), 'Quai des Tuileries');
        $this->assertEquals($fqLocation->getCity(), 'Paris');
        $this->assertEquals($fqLocation->getState(), 'Île-de-France');
        $this->assertEquals($fqLocation->getCountry(), 'France');
        $this->assertEquals($fqLocation->getZip(), 'FR');
        $this->assertEquals($fqLocation->getAddress(), 'Rue de Rivoli');
        $this->assertEquals($fqLocation->getDistance(), 907);

        /** @var Place $yelp */
        $yelp = $regionalData->getPlaces()[Place::YELP];
        $this->assertEquals($yelp->getMeta()->getCode(), 200);
        $this->assertEquals(count($yelp->getLocations()), 10);
        /** @var Location $yeLocation */
        $yeLocation = $yelp->getLocations()[0];
        $this->assertEquals($yeLocation->getId(), 'david-toutain-paris');
        $this->assertEquals($yeLocation->getName(), 'David Toutain');
        $this->assertEquals($yeLocation->getGeopoint()->getLatitude(), 48.8602806);
        $this->assertEquals($yeLocation->getGeopoint()->getLongitude(), 2.3096544);
        $this->assertEquals($yeLocation->getCity(), 'Paris');
        $this->assertEquals($yeLocation->getState(), '75');
        $this->assertEquals($yeLocation->getCountry(), 'FR');
        $this->assertEquals($yeLocation->getZip(), '75007');
        $this->assertEquals($yeLocation->getAddress(), '29 rue Surcouf, 7ème, 75007 Paris, France');
        $this->assertEquals($yeLocation->getDistance(), 9.5);
    }

    /**
     * @param Media $regionalData
     */
    private function assertMedia($regionalData)
    {
        $this->assertEquals($regionalData->getMeta()->getCode(), 200);
        $this->assertEquals($regionalData->getLocation()->getId(), 259540571);
        $this->assertEquals($regionalData->getLocation()->getName(), "Air France Invalides");
        $this->assertEquals($regionalData->getLocation()->getGeopoint()->getLatitude(), 48.862065309);
        $this->assertEquals($regionalData->getLocation()->getGeopoint()->getLongitude(), 2.314750969);
    }

    /**
     * Test instagram media by id
     */
    public function testParserForFailedMedia()
    {
        $id = 'invalid_code';
        $controller = new MediaController();
        $mediaSource = dirname(__DIR__). "/resources/instagram_failed_find_by_id.json";
        $controller->setInstagramService($this->getInstagramMediaMock($mediaSource, null));
        $response = $controller->getLocation($id, []);

        $this->assertEquals($response->getMeta()->getMessage(), 'invalid media id');
        $this->assertEquals($response->getMeta()->getType(), 'APINotFoundError');
        $this->assertEquals($response->getMeta()->getCode(), 400);
        $this->assertEquals($response->getMeta()->getContentType(), 'application/json; charset=utf-8');
    }

    /**
     * Test error responses with parsers and normalizers
     */
    public function testParsersForFailedResponses()
    {
        $id = "983529485487949611_5072160";
        $constructorArgs = [FACEBOOK_CLIENT_ID, FACEBOOK_CLIENT_SECRET];
        $facebookMock = $this->getSearchMock(dirname(__DIR__). "/resources/facebook_failed_search_invalid_token.json", "FacebookService", $constructorArgs);
        $foursquareMock = $this->getSearchMock(dirname(__DIR__). "/resources/foursquare_failed_search_invalid_token.json", "FoursquareService");
        $constructorArgs = [YELP_CONSUMER_KEY, YELP_CONSUMER_SECRET, YELP_TOKEN, YELP_TOKEN_SECRET];
        $yelpMock = $this->getSearchMock(dirname(__DIR__). "/resources/yelp_failed_search_invalid_token.json", "YelpService", $constructorArgs);

        // controller instance
        $mediaController = new MediaController();

        // set mock data
        $mediaSource = dirname(__DIR__). "/resources/instagram_find_by_id.json";
        $mediasSource = dirname(__DIR__). "/resources/instagram_failed_search_invalid_token.json";
        $instagramMock = $this->getInstagramMediaMock($mediaSource, $mediasSource);
        $mediaController->setInstagramService($instagramMock);
        $mediaController->setFacebookService($facebookMock);
        $mediaController->setFoursquareService($foursquareMock);
        $mediaController->setYelpService($yelpMock);

        // cal service to test
        $regionalData = $mediaController->getRegionalLocation($id, []);

        // assertions
        $this->assertEquals($regionalData->getMeta()->getCode(), 200);
        $this->assertEquals(count($regionalData->getPlaces()), 4);

        /** @var Place $facebook */
        $facebook = $regionalData->getPlaces()[Place::FACEBOOK];
        $this->assertNull($facebook->getLocations());
        $this->assertEquals($facebook->getMeta()->getCode(), 400);
        $this->assertEquals($facebook->getMeta()->getMessage(), 'Invalid OAuth access token signature.');
        $this->assertEquals($facebook->getMeta()->getType(), 'OAuthException');
        $this->assertEquals($facebook->getMeta()->getContentType(), 'application/json; charset=UTF-8');

        /** @var Place $instagram */
        $instagram = $regionalData->getPlaces()[Place::INSTAGRAM];
        $this->assertNull($instagram->getLocations());
        $this->assertEquals($instagram->getMeta()->getCode(), 400);
        $this->assertEquals($instagram->getMeta()->getMessage(), 'The access_token provided is invalid.');
        $this->assertEquals($instagram->getMeta()->getType(), 'OAuthAccessTokenException');
        $this->assertEquals($instagram->getMeta()->getContentType(), 'application/json; charset=utf-8');

        /** @var Place $foursquare */
        $foursquare = $regionalData->getPlaces()[Place::FOURSQUARE];
        $this->assertNull($foursquare->getLocations());
        $this->assertEquals($foursquare->getMeta()->getCode(), 401);
        $this->assertEquals($foursquare->getMeta()->getMessage(), 'OAuth token invalid or revoked.');
        $this->assertEquals($foursquare->getMeta()->getType(), 'invalid_auth');
        $this->assertEquals($foursquare->getMeta()->getContentType(), 'application/json; charset=utf-8');

        /** @var Place $yelp */
        $yelp = $regionalData->getPlaces()[Place::YELP];
        $this->assertNull($yelp->getLocations());
        $this->assertEquals($yelp->getMeta()->getCode(), 400);
        $this->assertEquals($yelp->getMeta()->getMessage(), 'One or more parameters are invalid in request');
        $this->assertEquals($yelp->getMeta()->getType(), 'INVALID_PARAMETER');
        $this->assertEquals($yelp->getMeta()->getContentType(), 'application/json; charset=UTF-8');
    }
}
