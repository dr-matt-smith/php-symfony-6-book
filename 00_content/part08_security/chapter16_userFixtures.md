

# Better fixtures with `UserFactory`

## Improving UserFixtures with a Foundry `UserFactory` (project `security02`)

As usual, Foundry will make creating and working with fixtures easier and more flexible.


Use the Symfony maker feature to make a `UserFactory`:

```bash
    $ symfony console make:factory

     Entity class to create a factory for:
      [0] App\Entity\User

     > 0
```

## Refactor `UserFactory` to hash passwords

As we did with simple fixtures last chapter, we need to add a property and constructor action to provide us with a password hasher object.

From the template class generated for us, the first thing we need to do is add a `use` statement, to allow us to make use of the `UserPasswordEncoderInterface` class.

Add the following to `/src/Factory/UserFactory.php`:

```php
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
```

Next, to make it easy to encode passwords we'll add a new private instance variable `$passwordHasher`, and a constructor method to initialise this object:

```php
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
```

We can now add post-initialisation logic in the `initialize()` method, to hash the password of the `User` object before it is persisted in the database:

```php
    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function(User $user) {
                $plainPassword = $user->getPassword();
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            });
    }
```

While we are at it, we can add better defaults for random users being created with this factor, in the `getDefaults()` method:

```php
    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->unique()->safeEmail(),
            'roles' => [],
            'password' => 'password'
        ];
    }
```

## Refactor `AppFixtures.php` to use the factory

We can update our `/src/DataFixtures/AppFixtures.php` class to create several users with the factory:

```php
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'email' => 'matt.smith@smith.com',
            'password' => 'smith',
            'roles' => [
                'ROLE_ADMIN',
                'ROLE_TEACHER'
                ]
        ]);

        UserFactory::createOne([
            'email' => 'user@user.com',
            'password' => 'user',
            'roles' => ['ROLE_USER']
        ]);

        UserFactory::createOne([
            'email' => 'admin@admin.com',
            'password' => 'admin',
            'roles' => ['ROLE_ADMIN']
        ]);
    }
```



## Loading the fixtures

Loading fixtures involves deleting all existing database contents and then creating the data from the fixture classes - so you'll get a warning when loading fixtures. At the CLI type:

```bash
    symfony console doctrine:fixtures:load
```

That's it!


You should now be able to access `/admin` with either the `matt.smith@smith.com/smith` or `admin@admin.com/admin` users. You will get an Access Denied exception if you login with `user@user.com/user`, since that only has `ROLE_USER` privileges, and `ROLE_ADMIN` is required to visit `/admin`.

See Figure \ref{denied_exception} to see the default Symfony (dev mode) Access Denied exception page.

![Screenshot of Default Symfony access denied page. \label{denied_exception}](./03_figures/part06_security/8_access_denied.png)

The next chapter will show you how to deal with (and log) access denied exceptions ...

## Using SQL from CLI to see users in DB


We can now run an SQL query from the Symfony console to confirm our 3 user fixtures have been created and persisted to the database.

```bash
    symfony console doctrine:query:sql "select * from user"

 ---- ---------------------- -------------------------------- --------------------------------------------------------------
  id   email                  roles                            password
 ---- ---------------------- -------------------------------- --------------------------------------------------------------
  4    matt.smith@smith.com   ["ROLE_ADMIN", "ROLE_TEACHER"]   $2y$13$5mUbdUYvuqTeObrUDLOueeehJcBiuvK8VuvjNtLQyze0/I2lqisxu
  5    user@user.com          ["ROLE_USER"]                    $2y$13$fHDtd2iETUhzauOP5D5T3uJaU0x82qu8RWUu1oM.GXwFV1NqpOrOa
  6    admin@admin.com        ["ROLE_ADMIN"]                   $2y$13$ryXek36va8XOZ5UiqV3tSOuqwQJcJ3t0B2nfBtw8K4kjUh0RSQdpu
 ---- ---------------------- -------------------------------- --------------------------------------------------------------
```
