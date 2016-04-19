<?php
namespace Livid\Config;

/**
 * Convenience trait for config classes.
 *
 * Defines properties and getters that are required by the ConfigInterface.
 *
 * @author Oliver Finn Madsen <mail@ofmadsen.com>
 */
trait ConfigTrait
{
    /** @var string */
    private $dsn = '';

    /** @var string */
    private $username = '';

    /** @var string */
    private $password = '';

    /** @var string[] */
    private $options = [];

    /**
     * Get the data source name.
     *
     * @return string
     */
    public function getDSN()
    {
        return $this->dsn;
    }

    /**
     * Get the username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get the options.
     *
     * @return string[]
     */
    public function getOptions()
    {
        return $this->options;
    }
}
