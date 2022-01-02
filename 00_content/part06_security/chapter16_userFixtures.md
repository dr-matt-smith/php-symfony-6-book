

# Security users from database

## Improving UserFixtures with a `createUser(...)` method (project `security03`)

Since making users in our `UserFixtures` class is very important, let's add a **helper** method to make it very clear what the properties of each new `User` object will be. See how clear the following is, if we have an exrta method `createUser(...)`:


We need a `load(...)` method, that gets invoked when we are loading fixtures from the CLI. This method creates objects for the entities we want in our database, and the saves (persists) them to the database:
```php
    public function load(ObjectManager $manager)
    {
        // create objects
        $userUser = $this->createUser('user@user.com', 'user');
        $userAdmin = $this->createUser('admin@admin.com', 'admin', ['ROLE_ADMIN']);
        $userMatt = $this->createUser('matt.smith@smith.com', 'smith', ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);

        // add to DB queue
        $manager->persist($userUser);
        $manager->persist($userAdmin);
        $manager->persist($userMatt);

        // send query to DB
        $manager->flush();
    }
```

Rather than put all the work in the `load(...)` method, we can create a helper method to create each new object. Method `createUser(...)` creates and returns a reference to a new `User` object given some parameters:

```php
    private function createUser($username, $plainPassword, $roles = ['ROLE_USER']):User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setRoles($roles);

        // password - and encoding
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        return $user;
    }
```

NOTE: The default role is `ROLE_USER` if none is provided.


## Loading the fixtures

Loading fixtures involves deleting all existing database contents and then creating the data from the fixture classes - so you'll get a warning when loading fixtures. At the CLI type:

```bash
    php bin/console doctrine:fixtures:load
```

That's it!

You should now be able to access `/admin` with either the `matt.smith@smith.com/smith` or `admin@admin.com/admin` users. You will get an Access Denied exception if you login with `user@user.com/user`, since that only has `ROLE_USER` privileges, and `ROLE_ADMIN` is required to visit `/admin`.

See Figure \ref{denied_exception} to see the default Symfony (dev mode) Access Denied exception page.

![Screenshot of Default Symfony access denied page. \label{denied_exception}](./03_figures/part06_security/8_access_denied.png)

The next chapter will show you how to deal with (and log) access denied exceptions ...

## Using SQL from CLI to see users in DB

To double check your fixtures have been created correctly in the database, you could run an SQL query from the CLI:

```bash
    $ php bin/console doctrine:query:sql "SELECT * FROM user"
    Cannot load Xdebug - it was already loaded
    
    /php-symfony-5-book-codes-security-03-create-user/vendor/doctrine/dbal/lib/Doctrine/DBAL/Tools/Dumper.php:71:
    array (size=3)
      0 => 
        array (size=4)
          'id' => string '2' (length=1)
          'email' => string 'user@user.com' (length=13)
          'roles' => string '["ROLE_USER"]' (length=13)
          'password' => string '$2y$13$yfMogZlZfDQ3cJeib6Q2kOqXemYBs.4/AnyK/RbAFp69.360N60ai' (length=60)
      1 => 
        array (size=4)
          'id' => string '3' (length=1)
          'email' => string 'admin@admin.com' (length=15)
          'roles' => string '["ROLE_ADMIN"]' (length=14)
          'password' => string '$2y$13$9UyVwrOluOkxLaH57IJM7uPF/NN7iKdBby.z9im2vx4531elfT80a' (length=60)
      2 => 
        array (size=4)
          'id' => string '4' (length=1)
          'email' => string 'matt.smith@smith.com' (length=14)
          'roles' => string '["ROLE_ADMIN", "ROLE_SUPER_ADMIN"]' (length=34)
          'password' => string '$2y$13$4/yo6pKgUgECygZHbawemOSeANK78Cu6bGtKKbSgByFLFxASSlC3u' (length=60)
```