
# Customising view based on logged-in user


## Twig nav links when logged in  (project `security08`)

The [Symfony security docs](https://symfony.com/doc/current/security.html#fetch-the-user-in-a-template) give us the Twig code for a conditional statement for when the current user has logged in:

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

So let's add such code to our `base.html.twig` master template (in `/templates`).


First, let's add a `<header>` element to either show the username and a logout link, or a link to login if the user is not logged-in yet:

```twig
    <header>
        {% if is_granted('IS_AUTHENTICATED_FULLY') %}
            Username:
            <strong>{{ app.user.username }}</strong>
            <br>
            <a href="{{ url('app_logout') }}">logout</a>
        {% else %}
            <a href="{{ url('app_login') }}">login</a>
        {% endif %}
    </header>
```

We can right align it and have a black bottom border with a little style in the `<head>`:

```css
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>{% block title %}Welcome!{% endblock %}</title>

            <style>
                header {
                    text-align: right;
                    border-bottom: 0.5rem solid black;
                    padding: 1rem;
                }
            </style>
```

Next, let's define a `<nav>` element, so that **all** users see a link to the homepage on every page on the website (at least those that extend `base.html.twig`). We will also add a conditional navigation link - to that users logged-in with `ROLE_ADMIN` can also see a link to the admin home page:

```twig
    <nav>
        <ul>
            <li>
                <a href="{{ url('homepage') }}">home</a>
            </li>

            {% if is_granted('ROLE_ADMIN') %}
                <li>
                    <a href="{{ url('admin') }}">admin home</a>
                </li>
            {% endif %}
        </ul>
    </nav>
```

So when a user first visits our website homepage, they are not logged-in, so will see a `login` link in the header, and the navigation bar will only show a link to this homepage. See Figure \ref{homepage_login_link}.

![Screenshot of homepage before logging-in. \label{homepage_login_link}](./03_figures/part06_security/13_homepage_login.png){ width=60% }

If the user has successfully logged-in with a `ROLE_ADMIN` privilege account, they will now see their userame and a `logout` link in the header, and they will also see revealed a link to the admin home page.  See Figure \ref{admin_user_homepage}.

![Screenshot of homepage after `ROLE_ADMIN` has logged-in.\label{admin_user_homepage}](./03_figures/part06_security/14_admin_with_username.png){ width=60% }

## Getting reference to the current user in a Controller

in PHP (e.g. a controller) you can get the user object as follows:

```php
    $user = $this->getUser();
```

or  you can type-hint in a controller method declaration, and the param converter will provide the $security object for your to interrogate:

```php
    use Symfony\Component\Security\Core\Security;

    public function indexAction(Security $security)
    {
        $user = $security->getUser();
    }
```

see:

- [https://symfony.com/doc/current/security.html#a-fetching-the-user-object](https://symfony.com/doc/current/security.html#a-fetching-the-user-object)