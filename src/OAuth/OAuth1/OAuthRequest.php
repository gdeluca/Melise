<?php

namespace OAuth\OAuth1;

class OAuthRequest
{
    public static $version = '1.0';
    public static $POST_INPUT = 'php://input';
    public $base_string;
    // for debug purposes
    protected $parameters;
    protected $http_method;
    protected $http_url;

    public function __construct($http_method, $http_url, $parameters = null)
    {
        $parameters = ($parameters) ? $parameters : array();
        $parameters = array_merge(OAuthUtil::parse_parameters(parse_url($http_url, PHP_URL_QUERY)), $parameters);
        $this->parameters = $parameters;
        $this->http_method = $http_method;
        $this->http_url = $http_url;
    }


    /**
     * attempt to build up a request from what was passed to the server
     */
    public static function from_request($http_method = null, $http_url = null, $parameters = null)
    {
        $scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on")
            ? 'http'
            : 'https';
        $http_url = ($http_url) ? $http_url : $scheme .
            '://' . $_SERVER['SERVER_NAME'] .
            ':' .
            $_SERVER['SERVER_PORT'] .
            $_SERVER['REQUEST_URI'];
        $http_method = ($http_method) ? $http_method : $_SERVER['REQUEST_METHOD'];

        // We weren't handed any parameters, so let's find the ones relevant to
        // this request.
        // If you run XML-RPC or similar you should use this to provide your own
        // parsed parameter-list
        if (!$parameters) {
            // Find request headers
            $request_headers = OAuthUtil::get_headers();

            // Parse the query-string to find GET parameters
            $parameters = OAuthUtil::parse_parameters($_SERVER['QUERY_STRING']);

            // It's a POST request of the proper content-type, so parse POST
            // parameters and add those overriding any duplicates from GET
            if ($http_method == "POST"
                && isset($request_headers['Content-Type'])
                && strstr($request_headers['Content-Type'],
                    'application/x-www-form-urlencoded')
            ) {
                $post_data = OAuthUtil::parse_parameters(
                    file_get_contents(self::$POST_INPUT)
                );
                $parameters = array_merge($parameters, $post_data);
            }

            // We have a Authorization-header with OAuth data. Parse the header
            // and add those overriding any duplicates from GET or POST
            if (isset($request_headers['Authorization']) && substr($request_headers['Authorization'], 0, 6) == 'OAuth ') {
                $header_parameters = OAuthUtil::split_header(
                    $request_headers['Authorization']
                );
                $parameters = array_merge($parameters, $header_parameters);
            }
        }

        return new OAuthRequest($http_method, $http_url, $parameters);
    }

    /**
     * pretty much a helper function to set up the request
     */
    public static function from_consumer_and_token($consumer, $token, $http_method, $http_url, $parameters = null)
    {
        $parameters = ($parameters) ? $parameters : array();
        $defaults = array("oauth_version" => OAuthRequest::$version,
            "oauth_nonce" => OAuthRequest::generate_nonce(),
            "oauth_timestamp" => OAuthRequest::generate_timestamp(),
            "oauth_consumer_key" => $consumer->key);
        if ($token) {
            $defaults['oauth_token'] = $token->key;
        }

        $parameters = array_merge($defaults, $parameters);

        return new OAuthRequest($http_method, $http_url, $parameters);
    }

    /**
     * util function: current nonce
     */
    private static function generate_nonce()
    {
        $mt = microtime();
        $rand = mt_rand();

        return md5($mt . $rand); // md5s look nicer than numbers
    }

    /**
     * util function: current timestamp
     */
    private static function generate_timestamp()
    {
        return time();
    }

    public function get_parameter($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    public function get_parameters()
    {
        return $this->parameters;
    }

    public function unset_parameter($name)
    {
        unset($this->parameters[$name]);
    }

    /**
     * Returns the base string of this request
     *
     * The base string defined as the method, the url
     * and the parameters (normalized), each urlencoded
     * and the concated with &.
     */
    public function get_signature_base_string()
    {
        $parts = array(
            $this->get_normalized_http_method(),
            $this->get_normalized_http_url(),
            $this->get_signable_parameters()
        );

        $parts = OAuthUtil::urlencode_rfc3986($parts);

        return implode('&', $parts);
    }

    /**
     * just uppercases the http method
     */
    public function get_normalized_http_method()
    {
        return strtoupper($this->http_method);
    }

    /**
     * parses the url and rebuilds it to be
     * scheme://host/path
     */
    public function get_normalized_http_url()
    {
        $parts = parse_url($this->http_url);

        $scheme = (isset($parts['scheme'])) ? $parts['scheme'] : 'http';
        $port = (isset($parts['port'])) ? $parts['port'] : (($scheme == 'https') ? '443' : '80');
        $host = (isset($parts['host'])) ? strtolower($parts['host']) : '';
        $path = (isset($parts['path'])) ? $parts['path'] : '';

        if (($scheme == 'https' && $port != '443')
            || ($scheme == 'http' && $port != '80')
        ) {
            $host = "$host:$port";
        }
        return "$scheme://$host$path";
    }

    /**
     * The request parameters, sorted and concatenated into a normalized string.
     * @return string
     */
    public function get_signable_parameters()
    {
        // Grab all parameters
        $params = $this->parameters;

        // Remove oauth_signature if present
        // Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
        if (isset($params['oauth_signature'])) {
            unset($params['oauth_signature']);
        }

        return OAuthUtil::build_http_query($params);
    }

    /**
     * builds the Authorization: header
     */
    public function to_header($realm = null)
    {
        $first = true;
        if ($realm) {
            $out = 'Authorization: OAuth realm="' . OAuthUtil::urlencode_rfc3986($realm) . '"';
            $first = false;
        } else {
            $out = 'Authorization: OAuth';
        }

        $total = array();
        foreach ($this->parameters as $k => $v) {
            if (substr($k, 0, 5) != "oauth") {
                continue;
            }
            if (is_array($v)) {
                throw new OAuthException('Arrays not supported in headers');
            }
            $out .= ($first) ? ' ' : ',';
            $out .= OAuthUtil::urlencode_rfc3986($k) .
                '="' .
                OAuthUtil::urlencode_rfc3986($v) .
                '"';
            $first = false;
        }
        return $out;
    }

    public function __toString()
    {
        return $this->to_url();
    }

    /**
     * builds a url usable for a GET request
     */
    public function to_url()
    {
        $post_data = $this->to_postdata();
        $out = $this->get_normalized_http_url();
        if ($post_data) {
            $out .= '?' . $post_data;
        }
        return $out;
    }

    /**
     * builds the data one would send in a POST request
     */
    public function to_postdata()
    {
        return OAuthUtil::build_http_query($this->parameters);
    }

    public function sign_request($signature_method, $consumer, $token)
    {
        $this->set_parameter(
            "oauth_signature_method",
            $signature_method->get_name(),
            false
        );
        $signature = $this->build_signature($signature_method, $consumer, $token);
        $this->set_parameter("oauth_signature", $signature, false);
    }

    public function set_parameter($name, $value, $allow_duplicates = true)
    {
        if ($allow_duplicates && isset($this->parameters[$name])) {
            // We have already added parameter(s) with this name, so add to the list
            if (is_scalar($this->parameters[$name])) {
                // This is the first duplicate, so transform scalar (string)
                // into an array so we can add the duplicates
                $this->parameters[$name] = array($this->parameters[$name]);
            }

            $this->parameters[$name][] = $value;
        } else {
            $this->parameters[$name] = $value;
        }
    }

    public function build_signature($signature_method, $consumer, $token)
    {
        $signature = $signature_method->build_signature($this, $consumer, $token);
        return $signature;
    }
}

class OAuthServer
{
    protected $timestamp_threshold = 300; // in seconds, five minutes
    protected $version = '1.0';             // hi blaine
    protected $signature_methods = array();

    protected $data_store;

    public function __construct($data_store)
    {
        $this->data_store = $data_store;
    }

    public function add_signature_method($signature_method)
    {
        $this->signature_methods[$signature_method->get_name()] =
            $signature_method;
    }

    // high level functions

    /**
     * process a request_token request
     * returns the request token on success
     * @param $request
     * @return mixed
     * @throws OAuthException
     */
    public function fetch_request_token(&$request)
    {
        $this->get_version($request);

        $consumer = $this->get_consumer($request);

        // no token required for the initial token request
        $token = null;

        $this->check_signature($request, $consumer, $token);

        // Rev A change
        $callback = $request->get_parameter('oauth_callback');
        $new_token = $this->data_store->new_request_token($consumer, $callback);

        return $new_token;
    }

    /**
     * version 1
     */
    private function get_version(&$request)
    {
        $version = $request->get_parameter("oauth_version");
        if (!$version) {
            // Service Providers MUST assume the protocol version to be 1.0 if this parameter is not present.
            // Chapter 7.0 ("Accessing Protected Ressources")
            $version = '1.0';
        }
        if ($version !== $this->version) {
            throw new OAuthException("OAuth version '$version' not supported");
        }
        return $version;
    }

    /**
     * try to find the consumer for the provided request's consumer key
     */
    private function get_consumer($request)
    {
        $consumer_key = $request instanceof OAuthRequest
            ? $request->get_parameter("oauth_consumer_key")
            : null;

        if (!$consumer_key) {
            throw new OAuthException("Invalid consumer key");
        }

        $consumer = $this->data_store->lookup_consumer($consumer_key);
        if (!$consumer) {
            throw new OAuthException("Invalid consumer");
        }

        return $consumer;
    }

    // Internals from here

    /**
     * all-in-one function to check the signature on a request
     * should guess the signature method appropriately
     */
    private function check_signature($request, $consumer, $token)
    {
        // this should probably be in a different method
        $timestamp = $request instanceof OAuthRequest
            ? $request->get_parameter('oauth_timestamp')
            : null;
        $nonce = $request instanceof OAuthRequest
            ? $request->get_parameter('oauth_nonce')
            : null;

        $this->check_timestamp($timestamp);
        $this->check_nonce($consumer, $token, $nonce, $timestamp);

        $signature_method = $this->get_signature_method($request);

        $signature = $request->get_parameter('oauth_signature');
        $valid_sig = $signature_method->check_signature(
            $request,
            $consumer,
            $token,
            $signature
        );

        if (!$valid_sig) {
            throw new OAuthException("Invalid signature");
        }
    }

    /**
     * check that the timestamp is new enough
     */
    private function check_timestamp($timestamp)
    {
        if (!$timestamp) {
            throw new OAuthException(
                'Missing timestamp parameter. The parameter is required'
            );
        }

        // verify that timestamp is recentish
        $now = time();
        if (abs($now - $timestamp) > $this->timestamp_threshold) {
            throw new OAuthException(
                "Expired timestamp, yours $timestamp, ours $now"
            );
        }
    }

    /**
     * check that the nonce is not repeated
     */
    private function check_nonce($consumer, $token, $nonce, $timestamp)
    {
        if (!$nonce) {
            throw new OAuthException(
                'Missing nonce parameter. The parameter is required'
            );
        }

        // verify that the nonce is uniqueish
        $found = $this->data_store->lookup_nonce(
            $consumer,
            $token,
            $nonce,
            $timestamp
        );
        if ($found) {
            throw new OAuthException("Nonce already used: $nonce");
        }
    }

    /**
     * figure out the signature with some defaults
     */
    private function get_signature_method($request)
    {
        $signature_method = $request instanceof OAuthRequest
            ? $request->get_parameter("oauth_signature_method")
            : null;

        if (!$signature_method) {
            // According to chapter 7 ("Accessing Protected Ressources") the signature-method
            // parameter is required, and we can't just fallback to PLAINTEXT
            throw new OAuthException('No signature method parameter. This parameter is required');
        }

        if (!in_array($signature_method,
            array_keys($this->signature_methods))
        ) {
            throw new OAuthException(
                "Signature method '$signature_method' not supported " .
                "try one of the following: " .
                implode(", ", array_keys($this->signature_methods))
            );
        }
        return $this->signature_methods[$signature_method];
    }

    /**
     * process an access_token request
     * returns the access token on success
     */
    public function fetch_access_token(&$request)
    {
        $this->get_version($request);

        $consumer = $this->get_consumer($request);

        // requires authorized request token
        $token = $this->get_token($request, $consumer, "request");

        $this->check_signature($request, $consumer, $token);

        // Rev A change
        $verifier = $request->get_parameter('oauth_verifier');
        $new_token = $this->data_store->new_access_token($token, $consumer, $verifier);

        return $new_token;
    }

    /**
     * try to find the token for the provided request's token key
     */
    private function get_token($request, $consumer, $token_type = "access")
    {
        $token_field = $request instanceof OAuthRequest
            ? $request->get_parameter('oauth_token')
            : null;

        $token = $this->data_store->lookup_token(
            $consumer, $token_type, $token_field
        );
        if (!$token) {
            throw new OAuthException("Invalid $token_type token: $token_field");
        }
        return $token;
    }

    /**
     * verify an api call, checks all the parameters
     */
    public function verify_request(&$request)
    {
        $this->get_version($request);
        $consumer = $this->get_consumer($request);
        $token = $this->get_token($request, $consumer, "access");
        $this->check_signature($request, $consumer, $token);
        return array($consumer, $token);
    }
}
