<?php

namespace TaylorNetwork\API;

use GuzzleHttp\Client;
use TaylorNetwork\API\Drivers\Driver;
use TaylorNetwork\API\Exceptions\APIException;

class API
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Driver
     */
    protected $driver;

    /**
     * The request
     * 
     * @var mixed
     */
    public $request;

    /**
     * The body of the request
     * 
     * @var mixed
     */
    public $body;

    /**
     * The contents of the request
     * 
     * @var mixed
     */
    public $content;

    /**
     * API constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Decode JSON from request content
     * 
     * @return mixed
     * @throws APIException
     */
    public function decodeContent()
    {
        if(!isset($this->content))
        {
            throw new APIException('You need to make a request first!');
        }
        
        return $this->decode($this->content);
    }

    /**
     * Decode JSON in array from request content
     * 
     * @return mixed
     * @throws APIException
     */
    public function recursiveDecodeContent()
    {
        $content = $this->decodeContent();
        
        foreach ($content as $key => $value)
        {
            $content[$key] = $this->decode($value);
        }
        
        return $content;
    }
    
    /**
     * Build URL
     *
     * @param $page
     * @return string
     * @throws APIException
     * @throws Exceptions\APIDriverException
     */
    public function buildURL($page)
    {
        $this->checkForDriver();
        
        $base = $this->driver->baseURL;
        
        if (!preg_match('#^(http://|ftp://)#', $base))
        {
            $base = 'http://' . $base;
        }
        
        $page = $this->driver->getPage($page);
        
        if (substr($page, 0, 1) != '/')
        {
            $page = '/' . $page;
        }
        
        return $base . $page;
    }

    /**
     * Make a request
     *
     * @param $type
     * @param $page
     * @param array $data
     * @return $this
     */
    public function makeRequest($type, $page, $data = [])
    {
        $url = $this->buildURL($page);
        
        if ($this->driver->credentialsOnEveryRequest)
        {
            $type = 'post';
            $data += $this->driver->getCredentials();
        }
        
        $this->request = $this->client->request($type, $url, [ 'json' => $data ]);
        $this->body = $this->request->getBody();
        $this->content = $this->body->getContents();
        
        return $this;
    }

    /**
     * POST request
     *
     * @param $page
     * @param array $data
     * @return API
     */
    public function post($page, $data = [])
    {
        return $this->makeRequest('post', $page, $data);
    }

    /**
     * GET request
     *
     * @param $page
     * @return API
     */
    public function get($page)
    {
        return $this->makeRequest('get', $page);
    }

    /**
     * Initialize Driver
     *
     * @param $driver
     * @return $this
     */
    public function initDriver($driver)
    {
        $driver = $this->buildDriverNamespace($driver);
        $this->driver = new $driver();
        return $this;
    }

    /**
     * Make the request using magic method.
     * 
     * @param $driver
     * @param $arguments
     * @return API
     */
    public function __call($driver, $arguments)
    {
        $this->initDriver($driver);
        
        switch(count($arguments))
        {
            case 1:
                return $this->get($arguments[0]);
                break;
            
            case 2:
                return $this->post($arguments[0], $arguments[1]);
                break;
            
            default:
                return $this;
                break;
        }
    }

    /**
     * Build the driver class
     *
     * @param $driver
     * @return string
     */
    protected function buildDriverNamespace($driver)
    {
        return '\\App\\' . config('api.namespace', 'APIDrivers') . '\\' . $driver;
    }

    /**
     * Check that a driver has been initialized
     *
     * @throws APIException
     */
    protected function checkForDriver()
    {
        if (!isset($this->driver) || !$this->driver instanceof Driver)
        {
            throw new APIException('No driver initialized!');
        }
    }

    /**
     * JSON decode
     * 
     * @param $data
     * @return mixed
     */
    protected function decode($data)
    {
        return json_decode($data);
    }

}