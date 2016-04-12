<?php
/**
 *  @author brian.mosigisi
 *  Populate and unpopulate DB for testing
 */

namespace Burayan\PotatoORM\Tests;

class DBTestSetup
{
    public static function populate($connection)
    {
        $Db = null;
        try {
            $Db = new \PDO(
                'mysql:host='.$connection['host'].';'.
                'dbname='.$connection['database'].'',
                $connection['username'],
                $connection['password']
            );

            $Db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $sql = 'CREATE TABLE IF NOT EXISTS users ('.
                'id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,'.
                'username VARCHAR( 50 ) NOT NULL,'.
                'email VARCHAR( 30 ) NOT NULL)';
            $Db->exec($sql);

            $sql = "INSERT INTO users (username, email) ".
                "VALUES ('tester', 'test@test.com')";
            $Db->exec($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function drop($connection)
    {
        try {
            $Db = new \PDO(
                'mysql:host='.$connection['host'].';',
                $connection['username'],
                $connection['password']
            );

            $Db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $sql = "DROP DATABASE ".$connection['database'];
            $Db->exec($sql);

            $sql = "CREATE DATABASE ".$connection['database'];
            $Db->exec($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
