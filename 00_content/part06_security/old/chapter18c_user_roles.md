
# User roles and role hierarchies


## UPDATE STATUS
NOTE - these chapters are currently being updated for Symfony 4

The status of this chapter is:

- out of date (old Symfony 3)   <<<<<<<< current status
- updated and okay for now
- very up to date



## Allowing different user roles (`recipe02`)

You may have noticed that no roles were stored as part of our previous `User` entity. The `getRoles()` method was hard-coded to always return an array containing a single role string `ROLE_USER`:

```php
    // src/AppBundle/Entity/User.php
    class User implements UserInterface, \Serializable
    {
        ...

        public function getRoles()
        {
            return array('ROLE_USER');
        }

        ...
```

Let's update our Entity code to allow different roles to be defined and stored/retrieved.

1. First let's change the private property (allowing Doctrine to save an array into a database text field - so a single user record can store multiple roles):

    ```php
        /**
         * @ORM\Column(type="json_array")
         */
        private $roles = [];
    ```

1. Next, let's ensure that the array returned by `getRoles()` alwaays includes `ROLE_USER` (since any user from a provided means they've managed to log in successfully):

    ```php
        public function getRoles()
        {
            $roles = $this->roles;

            // ensure always contains ROLE_USER
            $roles[] = 'ROLE_USER';

            return array_unique($roles);
        }
    ```

1. Finally, we have a simple setter (which we can write, or use the console to generate for us)

    ```php
        /**
         * Set roles
         *
         * @param array $roles
         *
         * @return User
         */
        public function setRoles($roles)
        {
            $this->roles = $roles;

            return $this;
        }
    ```

## Adding fixture logic to setup roles

If using fixtures to insert some user records into our database, we need to update our fixture code to also set appropriate roles. Here we also add a third user, with role `ROLE_SUPER_ADMIN`:

    ```php
        public function load(ObjectManager $manager)
        {
            // create objects
            $userSuperAdmin = $this->createActiveUser('super', 'super@admin.com', 'super',
            ['ROLE_SUPER_ADMIN']);

            $userAdmin = $this->createActiveUser('admin', 'admin@admin.com', 'admin', ['ROLE_ADMIN']);
            $userMatt = $this->createActiveUser('matt', 'matt@matt.com', 'smith');
            // store to DB
            $manager->persist($userSuperAdmin);
            $manager->persist($userAdmin);
            $manager->persist($userMatt);
            $manager->flush();
        }

        // note default role s ROLE_USER
        private function createActiveUser($username, $email, $plainPassword, $roles = ['ROLE_USER']):User
        {
            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setRoles($roles);
            $user->setIsActive(true);
            // password - and encoding
            $encodedPassword = $this->encodePassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
            return $user;
        }


        private function encodePassword($user, $plainPassword):string
        {
            $encoder = $this->container->get('security.password_encoder');
            $encodedPassword = $encoder->encodePassword($user, $plainPassword);
            return $encodedPassword;
        }
    ```

Don't forget to update your database schema and reload your fixtures...

## Simplifying roles with a hierarchy

Let's avoid repeating roles (if `ROLE_USER` or `ROLE_ADMIN`) by creating a hierarchy, so we can give `ROLE_ADMIN` all properties of `ROLE_USER` as well. We can easily create a role hierarchy in `/app/config/security.yml`:

```yaml
    security:
        role_hierarchy:
            ROLE_ADMIN:       ROLE_USER
            ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]

        ... rest of 'security.yml' as before ...
```

Now if we log in as a user with `ROLE_SUPER_ADMIN` we also get `ROLE_ADMIN` and `ROLE_USER` too!


Figures \ref{role_hierarchy} and  \ref{role_super}  show the interactive password encoding session for password `user`:

![Roles for `ROLE_ADMIN`. \label{role_hierarchy}](./03_figures/authentication/30_role_hierarchy_sm.png)

![Roles for `ROLE_SUPER_ADMIN`. \label{role_super}](./03_figures/authentication/31_super_admin_sm.png)

## Removing default adding of `ROLE_USER` if using a hierarchy

If we are using a hierarchy, we don't need always add `ROLE_USER` in code, so we can simplify our setter:


    ```php
        public function getRoles()
        {
            $roles = $this->roles;

            return array_unique($roles);
        }
    ```

We'll still see `ROLE_USER` for admin and super users, but in the list of **inherited** roles from the hierarchy. This is show in Figure \ref{role_inherited}.

![Super admin user inheriting `ROLE_USER`. \label{role_inherited}](./03_figures/authentication/32_inherited_sm.png)
