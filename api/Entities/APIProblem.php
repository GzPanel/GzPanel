<?php
/**
 * Created by PhpStorm.
 * User: Samer
 * Date: 2015-07-06
 * Time: 1:17 PM
 */

namespace api\Entities;


class APIProblem {
    public $httpStatusTitles = array(
        // INFORMATIONAL
        100 => 'Continue',
        101 => 'Switching Protocols',
        // SUCCESS
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => '204 No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // REDIRECTION
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    );
    public $status;
    public $detail;
    public $title;


    public function __construct($status, $detail, $title = null)
    {
        $this->status = $status;
        $this->detail = $detail;
        $this->title  = $title;
    }

    public function __toString(){
        return json_encode(array("type" => "http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html", "title" => $this->getTitle(), "status" => $this->status, "detail" => $this->detail));
    }

    public function getTitle(){
        if ($this->title == null){
            if (array_key_exists($this->status, $this->httpStatusTitles)){
                return $this->httpStatusTitles[$this->status];
            }
        }
        return $this->title;
    }
}