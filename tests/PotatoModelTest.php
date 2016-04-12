<?php
/**
 *  @author brian.mosigisi
 *  Test the base model class.
 */

namespace Burayan\PotatoORM\Tests;

use Burayan\PotatoORM\PotatoModel;
use Burayan\PotatoORM\Querrier;

class PotatoModelTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->connection = [
            'username' => 'andela',
            'password' => 'andela',
            'database' => 'test_burayan_orm',
            'host' => 'localhost',
        ];
        DBTestSetup::populate($this->connection);
    }

    public function tearDown()
    {
        DBTestSetup::drop($this->connection);
    }

    public function testModelIsInstantiated()
    {
        $user = new User();
        $this->assertInstanceOf(User::class, $user);
    }

    public function testModelsAreRetrieved()
    {
        $users = User::getAll();
        $this->assertInstanceOf(PotatoModel::class, $users[0]);

        $user = User::find(1);
        $this->assertInstanceOf(PotatoModel::class, $user);
        $this->assertSame($user->email, 'test@test.com');

        // Returns a model instance using a match parameter
        $user = User::find(['email' => 'test@test.com']);
        $this->assertInstanceOf(PotatoModel::class, $users[0]);
        $this->assertSame($user->email, 'test@test.com');
    }

    public function testModelsAreSaved()
    {
        $user = new User();
        $user->username = 'brian';
        $user->email = 'brian@brian.com';

        $user->save();
        Querrier::$connection = $this->connection;
        Querrier::connect();

        $saved_row = Querrier::findOne(
            User::getTable(),
            ['email' => 'brian@brian.com']
        );
        Querrier::disconnect();

        $this->assertSame($user->email, $saved_row['email']);
    }

    public function testModelsAreUpdated()
    {
        $user = User::find(1);

        $user->email = 'brian@brian.com';
        $user->save();
        $this->assertTrue(true);

        Querrier::$connection = $this->connection;
        Querrier::connect();

        $row = Querrier::findById('users', 1);

        $this->assertSame('brian@brian.com', $row['email']);
        Querrier::disconnect();
    }

    public function testRowsAreDeleted()
    {
        $this->assertTrue(User::destroy(1));
        $this->assertFalse(User::destroy(30));
    }
}
