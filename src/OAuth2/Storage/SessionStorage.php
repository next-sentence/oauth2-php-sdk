<?php

namespace OAuth2\Storage;

class SessionStorage implements StorageInterface
{
    /**
     * @var SessionAdapter
     */
    private $sessionAdapter;

    /**
     * @param SessionAdapter $sessionAdapter
     */
    public function __construct(SessionAdapter $sessionAdapter)
    {
        $this->sessionAdapter = $sessionAdapter;

        $this->initSession();
    }

    /**
     * Start new or resume existing session.
     */
    private function initSession()
    {
        $sessionId = $this->sessionAdapter->id();

        if (true === empty($sessionId)) {
            $this->sessionAdapter->start();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->sessionAdapter->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return $this->sessionAdapter->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->sessionAdapter->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->sessionAdapter->flush();
    }
}
