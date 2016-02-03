<?php

namespace OAuth2\Client;

use OAuth2\Service\Configuration;
use OAuth2\Service\Environment;
use OAuth2\Storage\SessionStorage;
use OAuth2\Storage\StorageInterface;

class Oauth2Client
{
    /**
     *  @var string
     */
    const GRAND_TYPE_KEY = 'grant_type';

    /**
     * @var string
     */
    const ACCESS_TOKEN_KEY = 'access_token';

    /**
     * @var string
     */
    const EXPIRE_TIME_KEY = 'expire_time';

    /**
     * @var string
     */
    const TOKEN_ENDPOINT = 'oauth2/token';

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Environment $environment
     * @param PersistenceInterface $persistence
     * @param HttpClient $httpClient
     * @param Request $request
     * @param Configuration $configuration
     */
    public function __construct(
        Environment $environment,
        SessionStorage $persistence,
        \DateTime $dateTime,
        HttpClient $httpClient,
        Request $request,
        Configuration $configuration
    ) {
        $this->environment = $environment;
        $this->storage = $persistence;
        $this->dateTime = $dateTime;
        $this->httpClient = $httpClient;
        $this->request = $request;
        $this->configuration = $configuration;
    }

    public function prepareRequest()
    {
        $this->request->setHeader('Accept', 'application/json');
        $this->request->setHeader(
            'Content-type',
            'application/x-www-form-urlencoded'
        );
    }

    /**
     * @return Response
     */
    public function requestAccessToken()
    {
        $this->prepareRequest();

        $this->request->setUri(
            $this->configuration->getBaseUri() . self::TOKEN_ENDPOINT
        );

        $this->request->setMethod('POST');

        $payload = $this->httpClient->buildQuery(
            array(
                'client_id' => $this->configuration->getClientId(),
                'client_secret' => $this->configuration->getClientSecret(),
                'grant_type' => $this->configuration->getGrantType(),
                'username' => $this->configuration->getUsername(),
                'password' => $this->configuration->getPassword(),
                'company' => $this->configuration->getCompany(),
            )
        );

        $this->request->setPayload($payload);

        return $this->httpClient->send($this->request);
    }

    /**
     * Return an access token.
     *
     * @return string|null
     */
    private function getAccessTokenFromPersistence()
    {
        $accessToken = $this->storage->get(self::ACCESS_TOKEN_KEY, null);

        if (null === $accessToken) {
            return null;
        }

        $expireTime = $this->storage->get(self::EXPIRE_TIME_KEY, null);
        if ($expireTime < $this->dateTime->getTimestamp()) {
            $this->flushAccessToken();
            return null;
        }

        return $accessToken;
    }

    /**
     * @param Response $response
     * @return string
     */
    private function setAccessTokenToPersistence(Response $response)
    {
        // '{"access_token":"[VALUE]","token_type":"bearer","expires_in":43146,"scope":"b a"}'
        $content = json_decode($response->getContent());

        $expireTime = $this->dateTime->getTimestamp() + $content->expires_in;

        $this->storage->set(self::ACCESS_TOKEN_KEY, $content->access_token);
        $this->storage->set(self::EXPIRE_TIME_KEY, $expireTime);
        $this->storage->set(self::GRAND_TYPE_KEY, $this->configuration->getGrantType());

        return $content->access_token;
    }

    /**
     * Return an access token.
     *
     * @throws \RuntimeException
     * @return string|null
     */
    public function getAccessToken()
    {
        $accessToken = $this->getAccessTokenFromPersistence();
        $grandType = $this->configuration->getGrantType();

        if (null !== $accessToken) {
            if ($grandType !== $this->storage->get(self::GRAND_TYPE_KEY, null)) {
                throw new \RuntimeException('grand_type switching not supported');
            }
            return $accessToken;
        }

        if ('password' === $grandType) {
            $response = $this->requestAccessToken();
        } else {
            throw new \RuntimeException('invalid grand_type configured');
        }

        if ((null === $response) || (200 != $response->getStatusCode())) {
            return null;
        }

        return $this->setAccessTokenToPersistence($response);
    }

    public function flushAccessToken()
    {
        $this->storage->delete(self::ACCESS_TOKEN_KEY);
        $this->storage->delete(self::EXPIRE_TIME_KEY);
        $this->storage->delete(self::GRAND_TYPE_KEY);
    }
}
