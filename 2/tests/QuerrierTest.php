<?php
/**
 *  @author brian.mosigisi
 *  Test the database querrying class.
 */

namespace Burayan\PotatoORM\Tests;

use Burayan\PotatoORM\Querrier;

class QuerrierTest extends \PHPUnit_Framework_TestCase
{
    protected $connection = [];

    public function setUp()
    {
        $this->connection = [
            'username' => 'andela',
            'password' => 'andela',
            'database' => 'test_burayan_orm',
            'host' => 'localhost',
        ];
        Querrier::$connection = $this->connection;
        Querrier::connect();

        // Seed our database with some data for testing
        DBTestSetup::populate($this->connection);
    }

    public function tearDown()
    {
        DBTestSetup::drop($this->connection);
        Querrier::disconnect();
    }

    public function testInstancesAreRetreived()
    {
        $row_array = Querrier::findOne(
            'users',
            ['email' => 'test@test.com']
        );
        $expected = [
            'id' => 1,
            'username' => 'tester',
            'email' => 'test@test.com'
        ];
        $this->assertEquals($row_array, $expected);

        $row_array = Querrier::findById('users', 1);
        $this->assertEquals($row_array, $expected);
    }

    public function testRowsAreAdded()
    {
        $users = Querrier::selectAll('users');
        $count = count($users);

        $new_user = [
            'username' => 'new_user',
            'email' => 'new_user@test.test'
        ];
        $new_user_id = Querrier::insertOne('users', $new_user);

        $new_user['id'] = $new_user_id;

        $users = Querrier::selectAll('users');
        $new_count = count($users);

        $this->assertEquals($count + 1, $new_count);
        $this->assertContains($new_user, $users);

        $new_users = [
            [
                'username' => 'new_user',
                'email' => 'new_user@test.test'
            ],
            [
                'username' => 'another_new',
                'email' => 'another_new@test.test'
            ]
        ];
        Querrier::insertMany('users', $new_users);
        $users = Querrier::selectAll('users');

        $this->assertEquals($new_count + 2, count($users));
    }

    public function testRowsAreUpdated()
    {
        $updated_user = [
            'username' => 'tester_updated',
            'email' => 'test@test.com'
        ];
        Querrier::updateRowById('users', 1, $updated_user);
        $users = Querrier::selectAll('users');
        $updated_user['id'] = 1;

        $this->assertContains($updated_user, $users);
    }

    public function testRowsAreDeleted()
    {
        Querrier::deleteRowById('users', 1);
        $users = Querrier::selectAll('users');
        $expected = [
            'id' => 1,
            'username' => 'tester',
            'email' => 'test@test.com'
        ];

        $this->assertNotContains($expected, $users);
    }
}
