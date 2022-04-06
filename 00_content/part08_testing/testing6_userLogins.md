
# Testing user logins (roles)

## Testing logins (project `test08`)

Since we are using the Symfony web test case, it becomes very easy to test different users logging in.

We can get a user from the DB `UserRepository`, select a particular user (declared in our fixtures) and just tell Symfony to log them in!

Here is how we would login user with username `john`:

```php
    $userRepository = static::getContainer()->get(UserRepository::class);
    $testUser = $userRepository->findOneByUsername('john');
    $client->loginUser($testUser);
```

Here is how we would login user with email `matt.smith@tudublin.ie`:

```php
    $userRepository = static::getContainer()->get(UserRepository::class);
    $testUser = $userRepository->findOneByEmail('matt.smith@tudublin.ie');
    $client->loginUser($testUser);
```


## Adding user security to our project

First, we need to add user-authenticated security to our project.

Make a `User` class with the Symfony make tool. The `User` entity class is special, and so we can simply **accept all the defaults** to give us a suitable entity for email-based user login authentication for our project.

```bash
    $ symfony console ma:user

       The name of the security user class (e.g. User) [User]:
       >

       Do you want to store user data in the database (via Doctrine)? (yes/no) [yes]:
       >

       Enter a property name that will be the unique "display" name for the user (e.g. email, username, uuid) [email]:
       >

       Will this app need to hash/check user passwords? Choose No if passwords are not needed or will be checked/hashed by some other system (e.g. a single sign-on server).

       Does this app need to hash/check user passwords? (yes/no) [yes]:
       >

       created: src/Entity/User.php
       created: src/Repository/UserRepository.php
       updated: src/Entity/User.php
       updated: config/packages/security.yaml

        Success!
```

## Add a login form authenticator

We can now make the authentication classes and default login form for our project, using the `make:auth` command.

We need to choose the "Login form authenticator" option, and give a meaningul name such as `LoginFormAuthenticator`:

```bash
    $ symfony console make:auth

       What style of authentication do you want? [Empty authenticator]:
        [0] Empty authenticator
        [1] Login form authenticator
       > 1

       The class name of the authenticator to create (e.g. AppCustomAuthenticator):
       > LoginFormAuthenticator

       Choose a name for the controller class (e.g. SecurityController) [SecurityController]:
       >

       Do you want to generate a '/logout' URL? (yes/no) [yes]:
       >

       created: src/Security/LoginFormAuthenticator.php
       updated: config/packages/security.yaml
       created: src/Controller/SecurityController.php
       created: templates/security/login.html.twig

        Success!
```

## Make and run a migration

We've created a new entity, so we need to **migrate** the entity changes to the database schema.

Use the  `make:migration` console command to create a migration file for the 2 entities we just created; then execute the migration on our test database (`--env=test`):


```bash
    $ symfony console ma:mi
    $ symfony console do:mi:mi --env=test
```



## Creating a `UserFactory` to help with fixtures

Let's create some user fixtures, with a user factory.

Create a `UserFactory` class with the make tool option `ma:factory`, and choose the `User` entity.

NOTE:

- in April 2022 I got a funny error ending `Make sure the service exists and is tagged with "doctrine.repository_service". `

- this problem was solved by delete the `/var` folder, and re-running the make factory command.

NOTE:

- you'll need to add code to the generated `UserFactory` class so that plain text passwords are hashed before being saved into the database.

- see section \ref{userFactoryHashing} in chapter \ref{usefactorychapter} for details on how to do this.

- alternatively, just copy the `UserFactory` class from the provided code sample of this project ...

## Creating some user fixtures

If we look at the properties of the generation `User` entity we see the following:

```php
    private $id; // this is used by the DB for auto-generated primary keys
    private $email;
    private $roles = [];
    private $password;
```

So can we create some user fixtures by adding the following to our `/src/DataFixtures/AppFixtures.php` class:

```php
    ...
    use App\Factory\UserFactory;
    ...

    public function load(ObjectManager $manager): void
    {
        ...
        UserFactory::createOne([
            'email' => 'matt@matt.com',
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


We can see these users have been added to the test database by running an SQL query:

```bash
     $ symfony console d:q:sql "select * from user" --env=test
     ---- ----------------- -------------------------------- ---------------
      id   email             roles                            password
     ---- ----------------- -------------------------------- ---------------
      1    matt@matt.com     ["ROLE_ADMIN", "ROLE_TEACHER"]   $2y$04$oPU...
      2    user@user.com     ["ROLE_USER"]                    $2y$04$Fw....
      3    admin@admin.com   ["ROLE_ADMIN"]                   $2y$04$uF3...
     ---- ----------------- -------------------------------- ---------------
```

## Securing user CRUD behind authentication

Let's make CRUD for `User` entities, and have all those CRUD routes protected behind authentication.

First we need to make the CRUD for the `User` entity:

```bash
    $ symfony console make:crud User
```

Then we need to secure all the `User` CRUD routes - these are declared in the generated controller class `/src/Controller/UserController.php`.

We do this by adding the `#[IsGranted('ROLE_ADMIN')]` attribute just before the class is declared. This secures **ALL** the routes for the `UserController` class.

```php
   use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/user')]
    class UserController extends AbstractController
    {
```

## Public routes for `Module` and `Lecturer`

To differentiate between the secured `User` CRUD and other public routes, let's generate CRUD for the `Module` and `Lecturer` entities.

```bash
    $ symfony console make:crud Module
    $ symfony console make:crud Lecturer
```

## Create web test class for `User` CRUD

Create a new test class for the `User` CRUD routes, named `UserCrudTest`:

```bash
    $ symfony console make:test

       Which test type would you like?:
        [TestCase       ] basic PHPUnit tests
        [KernelTestCase ] basic tests that have access to Symfony services
        [WebTestCase    ] to run browser-like scenarios, but that don't execute JavaScript code
        [ApiTestCase    ] to run API-oriented scenarios
        [PantherTestCase] to run e2e scenarios, using a real-browser or HTTP client and a real web server
       > WebTestCase


      Choose a class name for your test, like:
       * UtilTest (to create tests/UtilTest.php)
       * Service\UtilTest (to create tests/Service/UtilTest.php)
       * \App\Tests\Service\UtilTest (to create tests/Service/UtilTest.php)

       The name of the test class (e.g. BlogPostTest):
       > UserCrudTest

       created: tests/UserCrudTest.php
```

## Write test methods

To test whether a logged-in user with a role `ROLE_ADMIN` can access the `User` CRUD routes, we can write test methods such as this:

```php
        public function testRoleAdminUserCanSeeUserList(): void
        {
            // Arrange
            $method = 'GET';
            $url = '/user';
            $userEmail = 'matt@matt.com';
            // create client that automatically follow re-directs
            $client = static::createClient();
            $client->followRedirects();

            // Act
            $userRepository = static::getContainer()->get(UserRepository::class);
            $testUser = $userRepository->findOneByEmail($userEmail);
            $client->loginUser($testUser);

            $crawler = $client->request($method, $url);

            // Assert
            $this->assertResponseIsSuccessful();

            $expectedText = 'User index';
            $contentSelector = 'body h1';
            $this->assertSelectorTextContains($contentSelector, $expectedText);
        }
```

Likewise, we can write tests to assert that a user **without** `ROLE_ADMIN` cannot access the `User` CRUD routes:

```php
    public function testRoleUserUserCanNOTSeeUserList(): void
    {
        // Arrange
        $method = 'GET';
        $url = '/user';
        $userEmail = 'user@user.com';
        $okay200Code = Response::HTTP_OK;
        $accessDeniedResponseCode403 = Response::HTTP_FORBIDDEN;

        // create client that automatically follow re-directs
        $client = static::createClient();
        $client->followRedirects();

        // Act
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail($userEmail);
        $client->loginUser($testUser);

        $crawler = $client->request($method, $url);
        $responseCode = $client->getResponse()->getStatusCode();
        $this->assertSame($accessDeniedResponseCode403, $responseCode);
    }
``

