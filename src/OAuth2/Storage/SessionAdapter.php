<?php

namespace OAuth2\Storage;

class SessionAdapter implements StorageInterface
{
    /**
     * @return string The session id for the current session or the empty string.
     */
    public function id()
    {
        return session_id();
    }

    /**
     * Start new or resume existing session.
     *
     * @return bool TRUE if a session was successfully started, otherwise FALSE.
     */
    public function start()
    {
        return session_start();
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        if (true === isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $_SESSION = array();
    }
}
