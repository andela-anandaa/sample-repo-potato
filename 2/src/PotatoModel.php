<?php
/**
 * @author brian.mosigisi
 * This class is the main model class which is extended.
 * Your subclass properties should be the same as your
 * database table name.
 * Make sure you override the table property.
 */

namespace Burayan\PotatoORM;

class PotatoModel
{
    /**
     *  Overridden by subclasses.
     *  @var string $table
     */
    protected static $table;

    /**
     *  Stores the default connection parameters.
     *  @var array
     */
    protected static $connection =
    [
        'username' => 'andela',
        'password' => 'andela',
        'database' => 'test_burayan_orm',
        'host' => 'localhost'
    ];

    /**
     *  Retrieves all instances of a particular model.
     *  @return array $instances
     */
    public static function getAll()
    {
        self::openConnection();
        $rows = Querrier::selectAll(static::$table);
        $models = [];

        // convert rows array to an array of model instances.
        foreach ($rows as $value) {
            $potato = new static();
            $models[] = self::populate($value, $potato);
        }

        self::closeConnection();

        return $models;
    }

    /**
     *  Finds a particular record in the table.
     *  @return PotatoModel $model
     */
    public static function find($match_parameter)
    {
        self::openConnection();
        if (gettype($match_parameter) === 'integer') {
            $row = Querrier::findById(
                static::$table,
                $match_parameter
            );
            $potato = new static();

            return $row ? self::populate($row, $potato) : $row;
        } elseif (gettype($match_parameter) === 'array') {
            $row = Querrier::findOne(
                static::$table,
                $match_parameter
            );
            $potato = new static();

            return $row ? self::populate($row, $potato) : $row;
        }
        self::closeConnection();

        return false;
    }

    /**
     *  Deletes records from the table.
     *  @param $id
     *  @return boolean
     */
    public static function destroy($id)
    {
        self::openConnection();
        $prompt = Querrier::deleteRowById(static::$table, $id);
        self::closeConnection();

        return $prompt;
    }

    /**
     *  Takes the model instance and persists it to DB.
     *  @return boolean
     */
    public function save()
    {
        // If model has id field, then it needs to be updated.
        if (isset($this->id)) {
            $id = $this->id;
            unset($this->id);
            Querrier::updateRowById(static::$table, $id, (array)$this);
            return true;
        }
        
        Querrier::insertOne(static::$table, (array)$this);
        return true;
    }

    /**
     *  Opens connection to DB using querrier.
     */
    public static function openConnection()
    {
        Querrier::$connection = self::$connection;
        Querrier::connect();
    }

    /**
     *  Closes connection to DB.
     */
    public static function closeConnection()
    {
        Querrier::disconnect();
    }

    /**
     *  Get the model table of the extended class.
     *  @return string
     */
    public static function getTable()
    {
        return static::$table;
    }

    /**
     *  Takes an associative array and populates a model instance.
     *  @param array $properties_array
     *  @return PotatoModel $potato
     */
    private static function populate($properties_array, $potato)
    {
        foreach ($properties_array as $key => $value) {
            $potato->{$key} = $value;
        }

        return $potato;
    }
}
