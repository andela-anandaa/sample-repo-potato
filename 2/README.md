PotatoORM
==========

This is a minimal Object Relational Mapper for MySQL, that allows you to do CRUD operations on a database table, by extending a model class with little configuration.

## Install

- Clone the repository.
- Run:

    `composer install`

## Usage

- Navigate to /src/PotatoModel.php and update the connection array, to your database details.
- Create a Model class which extends PotatoModel, as below.
- Define a $table for the class.

```php
    use Burayan\PotatoORM\PotatoModel;
    
    class User extends PotatoModel
    {
        public static $table = 'users';
    }

    // Static methods
    User::find(1);
    User::getAll();
    User::destroy(1);

    // Instance methods
    $user = new User();
    $user->username = "username";
    $user->email = "username@name.com";
    $user->save();
```

## Testing

- Create a database in mysql, database 'test_burayan_orm'

    `CREATE DATABASE test_burayan_orm;`
- Create user 'andela' password 'andela' and grant priviledges.

    `CREATE USER 'andela'@'localhost' IDENTIFIED BY 'andela';`

    `GRANT ALL PRIVILEGES ON * . * TO 'andela'@'localhost';`
- Run the tests

    `composer test`
