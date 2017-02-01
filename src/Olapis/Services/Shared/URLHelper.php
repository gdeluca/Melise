<?php

namespace Olapis\Services\Shared;

/**
 * Class URLHelper
 * @package Olapis\Services
 */
abstract class URLHelper
{
    /**
     * Set URL information
     * @param $params
     * @param $urlTemplate
     * @return mixed|string
     */
    public static function populateParameters($params, $urlTemplate)
    {
        $otherUrlParams = [];
        $predefinedParamKeys = array("lat", "lng", "token", "distance", "location", "id", "version", "terms", "radius_filter", "limit");
        foreach ($params as $paramKey => $paramValue) {
            if (in_array($paramKey, $predefinedParamKeys) && strpos($urlTemplate, $paramKey)) {
                $urlTemplate = str_replace('{' . $paramKey . '}', $paramValue, $urlTemplate);
            } elseif ($paramValue != null) {
                $otherUrlParams[$paramKey] = $paramValue;
            }
        }
        if (!empty($otherUrlParams)) {
            if (strpos($urlTemplate, '?')) {
                $urlTemplate .= '&' . http_build_query($otherUrlParams);
            } else {
                $urlTemplate .= '?' . http_build_query($otherUrlParams);
            }
        }
        return $urlTemplate;
    }

    /**
     * Used to parse httpful respose to json for the parser
     * @param string $meta
     * @param mixed $body
     * @return string json
     */
    public static function buildJsonFromHttpFulResponse($meta, $body)
    {
        $parsedResult = [];
        $parsedResult['meta'] = $meta;
        $parsedResult['body'] = $body;
        $arrayResult = json_decode(json_encode($parsedResult), true);
        return json_encode($arrayResult, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
    }
}
