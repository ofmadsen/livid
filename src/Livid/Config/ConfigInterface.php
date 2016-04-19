<?php
namespace Livid\Config;

/**
 * Interface for the database configurations.
 *
 * @author Oliver Finn Madsen <mail@ofmadsen.com>
 */
interface ConfigInterface
{
    /**
     * Get the data source name.
     *
     * @return string
     */
    public function getDSN();

    /**
     * Get the username.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Get the password.
     *
     * @return string
     */
    public function getPassword();

    /**
     * Get the options.
     *
     * @return string[]
     */
    public function getOptions();
}
