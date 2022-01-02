
# User roles and role hierarchies

## Simplifying roles with a hierarchy (project `security07`)

Let's avoid repeating roles in our program logic (e.g. IF `ROLE_USER` OR `ROLE_ADMIN`) by creating a hierarchy, so we can give `ROLE_ADMIN` all properties of `ROLE_USER` as well. We can easily create a role hierarchy in `/config/packages/security.yml`:

```yaml
    security:
        role_hierarchy:
            ROLE_ADMIN:       ROLE_USER

        ... rest of 'security.yml' as before ...
```

In fact let's go one further - let's create a 3rd user role (`ROLE_SUPER_ADMIN`) and define that as having all `ROLE_ADMIN` privileges plus the `ROLE_USER` privileges that were inherited by `ROLE_ADMIN`:

```yaml
    security:
        role_hierarchy:
            ROLE_ADMIN:       ROLE_USER
            ROLE_SUPER_ADMIN: ROLE_ADMIN

        ... rest of 'security.yml' as before ...
```


Now if we log in as a user with `ROLE_SUPER_ADMIN` we also get `ROLE_ADMIN` and `ROLE_USER` too!


## Modify fixtures

Now we can modify our fixtures to make user `matt` have just `ROLE_SUPER_ADMIN` - the other roles should be inherited through the hierarchy:

Change `/src/DataFixtures/UserFixtures.php` as follows:

```php
        public function load(ObjectManager $manager)
        {
            ...

            $userMatt = $this->createUser('matt.smith@smith.com', 'smith', ['ROLE_SUPER_ADMIN']);

            ...
```

## Removing default adding of `ROLE_USER` if using a hierarchy

If we are using a hierarchy, we don't need always add `ROLE_USER` in code, so we can simplify our getter in our `User` Entity in `/src/Entity/User.php`:


    ```php
        public function getRoles()
        {
            return $this->roles;
        }
    ```

We'll still see `ROLE_USER` for admin and super users, but in the list of **inherited** roles from the hierarchy. This is show in Figure \ref{role_inherited}.

![Super admin user inheriting `ROLE_USER`. \label{role_inherited}](./03_figures/part06_security/12_inherited_roles.png)

Learn about user role hierarchies at:

- [Symfony hierarchical roles](https://symfony.com/doc/current/security.html#hierarchical-roles)

## Allowing easy switching of users when debugging

If you wish to speed up testing, you can allow easy switching between users just by adding a but at the end of your request URL, **if** you add the following to your firewall:

```yaml
    switch_user: true
```

Now you can switch users bu adding the following at the end of the URL:

```
    ?_switch_user=<username>
```

You stop impersonating users by adding `?_switch_user=_exit` to the end of a URL.

For example to visit the home page as user `user` you would write this URL:

```
    http://localhost:8000/?_switch_user=user
```

In your Twig you can allow this user to see special content (e.g. a link to exit impersonation) by testing for the special (automatically created role) `ROLE_PREVIOUS_ADMIN`:

```twig
    {% if is_granted('ROLE_PREVIOUS_ADMIN') %}
        <a href="{{ path('admin_index', {'_switch_user': '_exit'}) }}">Exit impersonation & return to admin home</a>
    {% endif %}
```

Learn more at:

- [Impersonating users](https://symfony.com/doc/current/security/impersonating_user.html)