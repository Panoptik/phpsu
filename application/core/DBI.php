<?php


/**
 * DBI
 *
 * Database connections interface
 */

class DBI
{


    /**
     * $_connections
     *
     * Pool of connections
     */

    private static $_connections = array();


    /**
     * $_stat
     *
     * Transactions (queries) statistics counter
     */

    private static $_stat = array('read' => 0, 'change' => 0);


    /**
     * getConnection
     *
     * Return connection object (instance of self)
     *
     * @param  string $key Key of connection instance
     * @return DBC             Database connection object
     */

    public static function getConnection($key = null)
    {
        if (!$key || sizeof(self::$_connections) == 1) {
            reset(self::$_connections);
            $key = key(self::$_connections);
        }
        if (!array_key_exists($key, self::$_connections)) {
            self::$_connections[$key] = new DBC(App::getConfig('main')->db);
        }

        return self::$_connections[$key];
    }


    /**
     * getStat
     *
     * Return queries statistics
     *
     * @return array Statistics result
     */

    public static function getStat()
    {
        return self::$_stat;
    }


    /**
     * addToStat
     *
     * Add query into statistics
     *
     * @param  string $queryString SQL query string
     * @return null
     */

    public static function addToStat($queryString)
    {
        $preg = '/^\s*(?:INSERT|REPLACE|UPDATE|DELETE|DROP)/i';
        $type = preg_match($preg, $queryString) ? 'change' : 'read';
        self::$_stat[$type] += 1;
    }
}
