

# Testing roles by creating and testing new users

## Automatic User Crud (project `codeception08`)

Since there seems to be an issue with Codeption fixtures, in this chapter we'll automate `User` CRUD by logging in as a a `ROLE_ADMIN` user, and filling in forms to create users with different roles, which can then be tested.

Key to this will be knowing the IDs of the form fields for creating a new user. See Figure \ref{userFormIds}.

![IDs for CRUD form for new user. \label{userFormIds}](./03_figures/testing/13_newUserIds.png)

From this we can write a `private` helper method to login with a `ROLE_ADMIN` user, fill in the form to create a DB user:

```php
    private function createUser(AcceptanceTester $I, $email, $password, $role)
    {
        $this->loginWithRoleAdmin($I);

        $I->amOnPage('/user/new');
        $I->fillField('#user_email', $email);
        $I->fillField('#user_password', $password);
        $I->fillField('#user_role', $role);
        $I->click('Save');
    }
```

This uses another useful `private` helper method, to login as a `ROLE_ADMIN` user:

```php
    private function loginWithRoleAdmin(AcceptanceTester $I)
    {
        $email = 'admin@admin.com';
        $password = 'admin';

        $I->amOnPage('/login');
        $I->expect('to successfully login as a ROLE_ADMIN user');
        $I->fillField('#inputEmail', $email);
        $I->fillField('#inputPassword', $password);
        $I->click('Login');

        // successful login
        $I->dontSee('Email could not be found.');
        $I->dontSee('Invalid credentials.');
    }
```

## Testing number of users in DB repository increases by 1 after creating

We can now write a test to check there is 1 more user in the DB repository after creating a new user:

```php
    /**
     * @example(email="test1@test1.com", password="test1", role="ROLE_USER")
     * @example(email="test2@chesecake.ie", password="cheesecake", role="ROLE_ADMIN")
     */
    public function canCreateUserInDb(AcceptanceTester $I, Example $example)
    {
        $email = $example['email'];
        $password = $example['password'];
        $role = $example['role'];

        // count BEFORE user created
        $users = $I->grabEntitiesFromRepository('App\Entity\User');
        $numUsersBeforeCreate = count($users);

        // ACT - create user
        $this->createUser($I, $email, $password, $role);

        // count AFTER user created
        $users = $I->grabEntitiesFromRepository('App\Entity\User');
        $numUsersAfterCreate = count($users);

        // ASSERT
        $I->expect('there to be 1 more user in DB after CRUD creation');
        $I->assertEquals($numUsersAfterCreate, 1 + $numUsersBeforeCreate);
    }
```

## Test we can only see new user in DB Repository AFTER creating it

We can also use `cantSeeInRepository(...)` and `seeInRepository(...)` to confirm the user only only exists **after** being creatred through the CRUD forms.

```php
    /**
     * @example(email="test1@test1.com", password="test1", role="ROLE_USER")
     * @example(email="test2@chesecake.ie", password="cheesecake", role="ROLE_ADMIN")
     */
    public function canCreateUserInDbAndSeeInRepository(AcceptanceTester $I, Example $example)
    {
        $email = $example['email'];
        $password = $example['password'];
        $role = $example['role'];

        // cannot seein DB BEFORE create user
        $I->cantSeeInRepository('App\Entity\User', [
            'email' => $email,
            'role' => $role
        ]);

        // ACT - create user
        $this->createUser($I, $email, $password, $role);

        // see in DB AFTER create user
        $I->seeInRepository('App\Entity\User', [
            'email' => $email,
            'role' => $role
        ]);
    }
```

## Testing we can login with created users

We can now write a test to check we can login with the newly created users:

```php
    /**
     * @example(email="test1@test1.com", password="test1", role="ROLE_USER")
     * @example(email="test2@chesecake.ie", password="cheesecake", role="ROLE_ADMIN")
     */
    public function canLoginWithCreatedUser(AcceptanceTester $I, Example $example)
    {
        $email = $example['email'];
        $password = $example['password'];
        $role = $example['role'];

        // should NOT be able to login before creating user
        $I->expect('cannot login before creating user');
        $this->cannotLogin($I, $email, $password);

        // ACT - create user
        $this->createUser($I, $email, $password, $role);

        // ASSERT
        $I->expect('successful login after user crated');
        $this->login($I, $email, $password);
    }
```

## Test new user with `ROLE_ADMIN` can visit seecured admin home page

Adn we can test that a newly created user with `ROLE_ADMIN` can visit the secure admin home page:

```php
    /**
     * @example(email="test2@chesecake.ie", password="cheesecake", role="ROLE_ADMIN")
     */
    public function newlyCreatedAdminRoleUserCanVisitAdminHomePage(AcceptanceTester $I, Example $example)
    {
        $email = $example['email'];
        $password = $example['password'];
        $role = $example['role'];

        // ACT - create user
        $this->createUser($I, $email, $password, $role);

        // login
        $this->login($I, $email, $password);

        // ASSERT: can access  secure pages
        $this->clickAdminHomeLinkAndSeeSecrets($I);
    }
```

See Figure \ref{newUserTests} for the passing test results.

![Tests passing for creating and testing new users. \label{newUserTests}](./03_figures/testing/14_newUserTests.png)
