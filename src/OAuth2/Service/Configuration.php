<?php

namespace OAuth2\Service;

class Configuration
{
    /**
     * @var string
     */
    private $baseUri = null;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $grantType;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $company;

    /**
     * @param Environment $environment
     * @param array $config
     */
    public function __construct(Environment $environment, array $config)
    {
        $this->environment = $environment;
        $this->baseUri = $config['baseUri'];
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->grantType = $config['grant_type'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->company = $config['company'];
    }

    /**
     * @return string
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @return string
     */
    public function getGrantType()
    {
        return $this->grantType;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }
}
