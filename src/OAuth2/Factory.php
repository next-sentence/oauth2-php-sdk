<?php

namespace OAuth2;

use OAuth2\Client\CurlAdapter;
use OAuth2\Client\CurlClient;
use OAuth2\Client\Header;
use OAuth2\Client\HttpClient;
use OAuth2\Client\Oauth2Client;
use OAuth2\Client\Request;
use OAuth2\Client\Response;
use OAuth2\Service\Configuration;
use OAuth2\Service\Environment;
use OAuth2\Service\Api;
use OAuth2\Storage\SessionAdapter;
use OAuth2\Storage\SessionStorage;

class Factory
{
    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var SessionAdapter
     */
    private $sessionAdapter;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * Create the Environment.
     *
     * @return Environment
     */
    private function createEnvironment()
    {
        if (null === $this->environment) {
            $this->environment = new Environment($_GET, $_SERVER);
        }
        return $this->environment;
    }

    /**
     * Create a Request.
     *
     * @return Request
     */
    private function createRequest()
    {
        return new Request(
            new Header()
        );
    }

    /**
     * Create a Response.
     *
     * @return Response
     */
    private function createResponse()
    {
        return new Response(
            new Header()
        );
    }

    /**
     * Create the SessionAdapter.
     */
    private function createSessionAdapter()
    {
        if (null === $this->sessionAdapter) {
            $this->sessionAdapter = new SessionAdapter();
        }
        return $this->sessionAdapter;
    }

    /**
     * Create a Persistence.
     *
     * @return SessionStorage
     */
    private function createSessionStorage()
    {
        return new SessionStorage(
            $this->createSessionAdapter()
        );
    }

    /**
     * Create a DateTime.
     *
     * @return \DateTime
     */
    private function createDateTime()
    {
        return new \DateTime();
    }

    /**
     * Create a HttpClient.
     *
     * @return HttpClient
     */
    private function createHttpClient()
    {
        return new CurlClient(
            $this->createResponse(),
            new CurlAdapter()
        );
    }

    /**
     * Create a Configuration.
     *
     * @return Configuration
     */
    private function createConfiguration($config)
    {
        if (null === $this->configuration) {
            $this->configuration = new Configuration(
                $this->createEnvironment(),
                $config
            );
        }
        return $this->configuration;
    }

    /**
     * Create an Oauth2Client.
     *
     * @return Oauth2Client
     */
    private function createOauth2Client($config)
    {
        return new Oauth2Client(
            $this->createEnvironment(),
            $this->createSessionStorage(),
            $this->createDateTime(),
            $this->createHttpClient(),
            $this->createRequest(),
            $this->createConfiguration($config)
        );
    }

    /**
     * Create the Api.
     *
     * @return Api
     */
    public function createApi(array $config)
    {
        return new Api(
            $this->createOauth2Client($config),
            $this->createHttpClient(),
            $this->createRequest(),
            $this->createConfiguration($config)
        );
    }
}
