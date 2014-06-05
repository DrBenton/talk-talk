<?php

namespace TalkTalk\CorePlugin\Core\Database;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Connection;

class ConnectionResolver implements ConnectionResolverInterface
{

    /**
     * All of the registered connections closures init.
     *
     * @var array
     */
    protected $connectionsInitCallables = array();
    /**
     * All of the registered connections.
     *
     * @var array
     */
    protected $connections = array();
    /**
     * The initialized connections closures.
     *
     * @var array
     */
    protected $initializedConnections = array();

    /**
     * The default connection name.
     *
     * @var string
     */
    protected $default;

    /**
     * Create a new connection resolver instance.
     *
     * @param  array $connections
     */
    public function __construct(array $connections = array())
    {
        foreach ($connections as $name => $connection) {
            $this->addConnection($name, $connection);
        }
    }

    /**
     * Get a database connection instance.
     *
     * @param  string $name
     * @return \Illuminate\Database\Connection
     */
    public function connection($name = null)
    {
        if (null === $name) {
            $name = $this->getDefaultConnection();
        }

        if (!isset($this->connections[$name]) && isset($this->connectionsInitCallables[$name])) {
            $this->initConnectionClosure($name);
        }

        return $this->connections[$name];
    }

    /**
     * Add a connection to the resolver, resolved through a Closure.
     *
     * @param  string $name
     * @param  callable $connectionInitClosure
     * @return void
     */
    public function addConnectionInitCallable(
        $name,
        /*callable*/
        $connectionInitClosure
    )
    {
        $this->connectionsInitCallables[$name] = $connectionInitClosure;
    }

    /**
     * Add a connection to the resolver.
     *
     * @param  string $name
     * @param  \Illuminate\Database\Connection $connection
     * @return void
     */
    public function addConnection($name, Connection $connection)
    {
        $this->connections[$name] = $connection;
    }

    /**
     * Check if a connection has been registered.
     *
     * @param  string $name
     * @return bool
     */
    public function hasConnection($name)
    {
        return isset($this->connections[$name]) || isset($this->connectionsInitCallables[$name]);
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->default;
    }

    /**
     * Set the default connection name.
     *
     * @param  string $name
     * @return void
     */
    public function setDefaultConnection($name)
    {
        $this->default = $name;
    }

    protected function initConnectionClosure($name)
    {
        $this->connections[$name] = call_user_func($this->connectionsInitCallables[$name]);
    }
}