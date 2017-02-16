<?php

namespace TaylorNetwork\API\Drivers;


use TaylorNetwork\API\Exceptions\APIDriverException;

abstract class Driver
{

    /**
     * The base URL of the API
     * 
     * @var string
     */
    public $baseURL;

    /**
     * Pages to allow access to, '*' for all pages.
     * 
     * @var array
     */
    public $allowedPages = ['*'];

    /**
     * Pages to block access to.
     * 
     * @var array
     */
    public $blockedPages = [];

    /**
     * Aliases for specific pages.
     * 
     * Key is alias
     * Value is page itself
     * 
     * @var array
     */
    public $pageAliases = [];

    /**
     * Login credentials if needed
     * 
     * Note: you can hard code in the driver here or keep in a separate file
     * and override the loadCredentials() function below in your driver class.
     * 
     * @var array
     */
    public $credentials;

    /**
     * POST login credentials on every request, for middleware and such.
     * 
     * @var bool
     */
    public $credentialsOnEveryRequest = false;

    /**
     * Driver constructor.
     */
    public function __construct()
    {
        if (!isset($this->baseURL))
        {
            throw new APIDriverException('You must set a base URL in your API driver class.');
        }
        
        if (!isset($this->credentials))
        {
            $this->loadCredentials();   
        }
    }

    /**
     * Get the credentials
     * 
     * @return array
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * Load the credentials
     * 
     * Note: you can hard code credentials here, or add code to load from a separate file.
     */
    public function loadCredentials()
    {
        $this->credentials = [];
    }

    /**
     * Get a page or throw an error if blocked/not allowed.
     * 
     * @param $page
     * @return mixed
     * @throws APIDriverException
     */
    public function getPage($page)
    {
        if (array_key_exists($page, $this->pageAliases))
        {
            $page = $this->pageAliases[$page];
        }
        
        if (in_array($page, $this->blockedPages))
        {
            throw new APIDriverException($page . ' is blocked in driver.');
        }
        
        if (!in_array('*', $this->allowedPages))
        {
            if (!in_array($page, $this->allowedPages))
            {
                throw new APIDriverException($page . ' is not allowed in driver.');
            }
        }
        
        return $page;
    }
    
}