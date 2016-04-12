<?php
/**
 *  @author brian.mosigisi
 *  Contains static functions for executing querries.
 */

namespace Burayan\PotatoORM;

class Querrier
{

    /**
     *  Stores the connection parameters.
     *  @var array
     */
    public static $connection = [];

    /**
     *  Holds the PDO object created from the connection.
     *  @var PDO db instance
     */
    private static $db;

    /**
     *  Takes the connection object and instantiates PDO.
     *  @return null
     */
    public static function connect()
    {
        if (empty(self::$connection)) {
            throw new \Exception('Cannot connect without parameters');
        }

        try {
            self::$db = new \PDO(
                'mysql:host='.self::$connection['host'].';'.
                'dbname='.self::$connection['database'].'',
                self::$connection['username'],
                self::$connection['password']
            );

            self::$db->setAttribute(
                \PDO::ATTR_ERRMODE,
                \PDO::ERRMODE_EXCEPTION
            );
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     *  Destroys the PDO instance.
     *  @return null
     */
    public static function disconnect()
    {
        self::$db = null;
    }

    /**
     *  Returns all the rows of a particular table.
     *  @param string $table
     *  @return array
     */
    public static function selectAll($table)
    {
        $stmt = self::$db->prepare("SELECT * FROM $table");
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     *  Returns a complete row from a table, according to the parameters.
     *  @param string $table table name
     *  @param array $match_parameter where matcher
     *  @return array
     */
    public static function findOne($table, $match_parameter)
    {
        $stmt = self::$db->query(
            "SELECT * FROM ". $table .
            " WHERE ". key($match_parameter) . "= '" .
            reset($match_parameter) . "'"
        );
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);

        return $stmt->fetch();
    }

    /**
     *  Returns a row, using the id field to match.
     *  @param string $table table name
     *  @param int $id
     *  @return array
     */
    public static function findById($table, $id)
    {
        return self::findOne($table, ['id' => $id]);
    }

    /**
     *  Insert one row into a table.
     *  @param string $table
     *  @param array $new_record
     *  @return int last insert id
     */
    public static function insertOne($table, $new_record)
    {
        $fields = [];
        $placeholders = [];
        foreach ($new_record as $key => $value) {
            $fields[] = $key;
        }

        foreach ($fields as $value) {
            $placeholders[] = ':' . $value;
        }

        $placeholders = implode(', ', $placeholders);
        $fields = implode(', ', $fields);

        $stmt = self::$db->prepare(
            "INSERT INTO $table ($fields)".
            " VALUES ($placeholders)"
        );

        $stmt->execute($new_record);
        return self::$db->lastInsertId();
    }

    /**
     *  Insert several rows into a table.
     *  @param string $table
     *  @param array $new_records
     *  @return int $count number of rows affected
     */
    public static function insertMany($table, $new_records)
    {
        $count = 0;
        foreach ($new_records as $value) {
            $count += self::insertOne($table, $value);
        }
        return $count;
    }

    /**
     *  Update a particular row in the table.
     *  @param string $table
     *  @param int $id
     *  @param array new_record
     *  @return boolean
     */
    public static function updateRowById($table, $id, $record)
    {
        $stmt = self::$db->prepare(
            "UPDATE $table ".
            "SET ". self::generateSetStatement($record) .
            " WHERE id = '$id'"
        );
        $stmt->execute($record);
        return true;
    }

    /**
     *  Delete a particular row in a table.
     *  @param string $table
     *  @param int $id
     *  @return boolean
     */
    public static function deleteRowById($table, $id)
    {
        $stmt = self::$db->prepare(
            "DELETE FROM $table
            WHERE id = '$id'"
        );
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     *  Specifically for SQL update statement.
     *  Takes an array which represents an SQL row and returns
     *  a key value pair string usable in SQL.
     *  @return string
     */
    public static function generateSetStatement($record)
    {
        $finalSet = [];
        foreach ($record as $key => $value) {
            $finalSet[] = $key . '=' . ':' . $key;
        }
        return implode(', ', $finalSet);
    }
}
