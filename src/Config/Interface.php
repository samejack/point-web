<?php
namespace point\web;

interface Config_Interface extends \IteratorAggregate, \ArrayAccess, \Countable
{
    /**
     * load configuration file
     *
     * @param string $filepath File path
     * @return mixed
     */
    public function loadfile($filepath);

    /**
     * Set environment value
     *
     * @param string $environment
     * @return void
     */
    public function setEnvironment ($environment);

    /**
     * Get environment value
     *
     * @return string
     */
    public function getEnvironment ();
}
