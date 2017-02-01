<?php

namespace Olapis;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class PrettyJsonResponse: Pretty prints the Symfony JsonResponse, 100% compatible with JsonResponse
 * @package Olapis
 */
class PrettyJsonResponse extends JsonResponse
{
    /**
     * Sets the data to be sent as json.
     * @param mixed $data
     * @return JsonResponse
     */
    public function setData($data = array())
    {
        // Encode <, >, ', &, and " for RFC4627-compliant JSON, which may also be embedded into HTML.
        $this->data = json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
            | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return $this->update();
    }
}
