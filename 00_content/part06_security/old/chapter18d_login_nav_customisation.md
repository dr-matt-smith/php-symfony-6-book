
# Customising view based on logged-in user


## UPDATE STATUS
NOTE - these chapters are currently being updated for Symfony 4

The status of this chapter is:

- out of date (old Symfony 3)   <<<<<<<< current status
- updated and okay for now
- very up to date



## Twig nav links when logged in (`recipe03`)

The [Symfony security docs](http://symfony.com/doc/current/security.html#retrieving-the-user-in-a-template) give us the Twig code for a conditional statement for when the current user has logged in:

```html
    {% if is_granted('IS_AUTHENTICATED_FULLY') %}
        <p>Username: {{ app.user.username }}</p>
    {% endif %}
```

We can also test for which **role** a user may have granted when logged-in, e.g.:

```html
    {% if is_granted('ROLE_ADMIN') %}
          Welcome to the Admin home page ...
    {% endif %}
```

We can use such conditionals in 2 useful and common ways:

1. Confirm the login username and offer a `logout` link for users who are logged in

1. Have navbar links revealed only for logged-in users (of particular roles)

So let's add some code to our `base.html.twig` master template (in `/app/Resources/views`):

```html
```


