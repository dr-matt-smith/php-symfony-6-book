# Add some login security

## Visit the user admin pages

Visit `localhost:8000/user` - you'll see our 2 users for the system. See Figure \ref{user_index}.

    - matt (password: smith), an **admin** user
    
    - john (password: doe), a normal user

![Screenshot of phone make CRUD pages.\label{user_index}](./03_figures/app_crud/user_list.png){width=75%}

These users were setup when we loaded **fixtures** (initial data) into the database tables. These objects are created and then **persisted** into rows in the database tables when the classes in the `src/DataFixtures` directory are executed.

Since we're using several Foundry **factories**, we can create all our test data in a few lines of code in a single class `src/DataFixtures/AppFixtures`.

We can see the users, with their password and roles being set in the `load(...)` method of cass class `src/DataFixtures/AppFixtures`:

```php
    class AppFixtures extends Fixture
    {
        public function load(ObjectManager $manager): void
        {
            UserFactory::createOne([
                'username' => 'matt',
                'password' => 'smith',
                'role' => 'ROLE_ADMIN'
            ]);
    
            UserFactory::createOne([
                'username' => 'john',
                'password' => 'doe',
                'role' => 'ROLE_USER'
            ]);
    
            MakeFactory::createOne(['name' => 'Apple']);
            MakeFactory::createOne(['name' => 'Samsung']);
            MakeFactory::createOne(['name' => 'Sony']);
    
            PhoneFactory::createOne([
                'model' => 'iPhone X',
                'memory' => '128',
                'manufacturer' => MakeFactory::find(['name' => 'Apple']),
            ]);
    
            PhoneFactory::createOne([
                'model' => 'Galaxy 21',
                'memory' => '256',
                'manufacturer' => MakeFactory::find(['name' => 'Samsung']),
            ]);
```

## Secure the user admin behind a firewall

Let's only allow logged in ROLE_ADMIN users to access the user CRUD pages.

We do this by adding a requirement that a user must be logged in and have the ROLE_ADMIN user role. This can all be achieved by adding a `use` statement for the `IsGranted` class, and single PHP 8 atribute line immediately before the declaration of class file `src/Controller/UserController.php`:

![IsGranted security requirement added to the `UserController` class.](./03_figures/app_crud/is_granted.png){ width=100% }

## Login

If you visit `localhost:8000/user` again you'll now be asked to login. See Figure \ref{login}. Symfony has seen the security requirement that only users with `ROLE_ADMIN` security are permitted to access the `User` CRUD controller methods, and so has automatically redirected to the login form page.

![Login form. \label{login}](./03_figures/app_crud/login_form.png){ width=50% }

If successfully logged in as an admin (`ROLE_ADMIN`) user, you can now visit the `User` CRUD pages. See Figure \ref{user_matt}.
                                                                                  
![User pages logged in as `matt`. \label{user_matt}](./03_figures/app_crud/user_matt.png){ width=100% }

Clicking the user in the debug profiler web page footer gives details about the role(s) of the logged in user. See Figure \ref{user_matt_details}.

![Details of logged-ion user `matt`. \label{user_matt_details}](./03_figures/app_crud/user_matt_details.png){ width=75% }
                                                                                                                                                      
                                                                                                                                                      