<?php

error_reporting(0);

class RestAPI
{
    private $realm = "";
    // Username
    private string $username = "";
    // Password
    private string $password = "";
    // Is Auth
    private bool $is_auth;
    // Has Auth
    private bool $has_auth = false;

    function __construct($_realm = "API")
    {
        $this->has_auth = false;
        header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Etag: " . md5(uniqid(mt_rand(), true)));
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('WWW-Authenticate: Basic realm="' . $_realm . '"');
        $this->header_status(401);
        // Apache
        if (isset($_SERVER['PHP_AUTH_USER'])) :
            $this->username = $_SERVER['PHP_AUTH_USER'];
            $this->password = $_SERVER['PHP_AUTH_PW'];
            $this->has_auth = true;
        // (NGINX, etc)
        elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) :
            if (preg_match('/^basic/i', $_SERVER['HTTP_AUTHORIZATION'])) {
                list($this->username, $this->password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
                $this->has_auth = true;
            };
        endif;

        if (!$this->has_auth) {
            $this->header_status(401);
            die();
        }
    }

    // Retorna o status  da requisição
    function header_status($statusCode)
    {
        static $status_codes = null;

        if ($status_codes === null) {
            $status_codes = array(
                100 => 'Continue',
                101 => 'Switching Protocols',
                102 => 'Processing',
                200 => 'OK',
                201 => 'Created',
                202 => 'Accepted',
                203 => 'Non-Authoritative Information',
                204 => 'No Content',
                205 => 'Reset Content',
                206 => 'Partial Content',
                207 => 'Multi-Status',
                300 => 'Multiple Choices',
                301 => 'Moved Permanently',
                302 => 'Found',
                303 => 'See Other',
                304 => 'Not Modified',
                305 => 'Use Proxy',
                307 => 'Temporary Redirect',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                402 => 'Payment Required',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                406 => 'Not Acceptable',
                407 => 'Proxy Authentication Required',
                408 => 'Request Timeout',
                409 => 'Conflict',
                410 => 'Gone',
                411 => 'Length Required',
                412 => 'Precondition Failed',
                413 => 'Request Entity Too Large',
                414 => 'Request-URI Too Long',
                415 => 'Unsupported Media Type',
                416 => 'Requested Range Not Satisfiable',
                417 => 'Expectation Failed',
                422 => 'Unprocessable Entity',
                423 => 'Locked',
                424 => 'Failed Dependency',
                426 => 'Upgrade Required',
                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
                504 => 'Gateway Timeout',
                505 => 'HTTP Version Not Supported',
                506 => 'Variant Also Negotiates',
                507 => 'Insufficient Storage',
                509 => 'Bandwidth Limit Exceeded',
                510 => 'Not Extended'
            );
        }

        if ($status_codes[$statusCode] !== null) {
            $status_string = $statusCode . ' ' . $status_codes[$statusCode];
            header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status_string, true, $statusCode);
        }
    }

    function request_success()
    {
        $this->header_status(200);
    }

    function request_error($e = null)
    {
        $this->header_status(500);
        if (is_null($e)) :
            die();
        else :
            echo "Error Code: " . $e->getCode() . "\n\n";
            echo "Error Line: " . $e->getLine() . "\n\n";
            echo "Error File: " . $e->getFile() . "\n\n";
            echo "Error Message: \n" . $e->getMessage() . "\n\n";
            echo "Error Trace: \n" . $e->getTraceAsString() . "\n";
            die();
        endif;
    }

    function request_not_found()
    {
        $this->header_status(404);
    }

    function request_unauthorized()
    {
        $this->header_status(401);
    }

    function validate_auth($callback)
    {
        $this->is_auth = $callback();

        if ($this->is_auth) {
            $this->header_status(202);
        } else {
            $this->request_unauthorized();
            die();
        }
    }

    function get_username()
    {
        return $this->username;
    }

    function array_to_json(array $arr, $obj = false)
    {
        header('Content-type:application/json;charset=utf-8');
        if ($obj) :
            echo json_encode($arr, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        else :
            echo json_encode($arr, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        endif;
    }
}
