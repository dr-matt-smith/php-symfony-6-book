
# Custom login page

## A D.I.Y. (customisable) login form  (project `security04`)

When we created the Authenticator it created a login form Twig template for us:

```bash
    $ php bin/console make:auth

    ... 

    created: src/Controller/SecurityController.php
    created: templates/security/login.html.twig
```

This is just a Twig template, and we should feel free to look inside and edit it ourselves ...

## Simplifying the generated login Twig template

The generated Twig login page is fine - but you should become confident in making it your own. 

Start by replacing it with this simple, standard HTML login form:

```twig
    <form method="post">
        <h1>Login</h1>

        Username:
        <input value="{{ last_username }}" name="email" id="inputEmail" autofocus>

        <p>
        Password:
        <input type="password" name="password" id="inputPassword">

        <input type="submit" value="Login">

    </form>
```

The form is shown when the `/login` URL is visited, or Symfony is redirected to internal route `app_login`, with the HTTP `GET` method. There is not `action` attribute for the `<form>` element, so the form is submitted to the same router, but using the `post` method.

Two name/value form variables are submitted:

- `email` - the email address being used as the unique username

- `password` - the password

## CSRF (Cross Site Request Forgery) protection

Although this Twig template will present a login form to the user, it will **not** be accepted by the Symfony security system, due to an esxposre to CSRF security vulnerability. 

NOTE: For any public **production** site you should always implement CSRF protection. This is implemented using CSRF 'tokens' created on the server and exchanged with the web client and form submissions. CSRF tokens help protect web applications against cross-site scripting request forgery attacks and forged login attacks.

Symfony expects forms to submit a special form variable `_csrf_token`. In Symfony this token can be generated using Twig function `csrf_token('authenticate')`. So we need to add this as a hidden form variable for our D.I.Y. form to work:

```twig
    <form method="post">
        <input type="hidden" name="_csrf_token" value="{{ csrf_token('authenticate') }}">

        ... as before
    </form>
```

Learn more about CSRF threats and security:

- [Symfony CSRF protection](https://symfony.com/doc/current/security/csrf.html)
- [Wikipedia](https://en.wikipedia.org/wiki/Cross-site_request_forgery#Forging_login_requests)

- [article on DIY CSRF for PHP](https://www.netsparker.com/blog/web-security/protecting-website-using-anti-csrf-token/)
- [Stack Overlow about PHP CSRF](https://stackoverflow.com/questions/6287903/how-to-properly-add-cross-site-request-forgery-csrf-token-using-php)


When using the Symfony generated login form (as we created in this chapter) the CSRF token protection is built-in automatically.


## Display any errors

We are only missing one more important set of data from Symfony - any errors to be displayed due to a previous invalid form submission. We should always check for an `error` object, and if present display its `messageData` values as follows (here I've added some CSS to add some padding and a pink background colour):

```twig
    <form method="post">
        <input type="hidden" name="_csrf_token" value="{{ csrf_token('authenticate') }}">

        {% if error %}
            <div style="background-color: pink; padding: 1rem;">
                {{ error.messageKey|trans(error.messageData, 'security') }}
            </div>
        {% endif %}

       <h1>Login</h1>

        Username:
        <input value="{{ last_username }}" name="email" id="inputEmail" autofocus>

        <p>
        Password:
        <input type="password" name="password" id="inputPassword">

        <input type="submit" value="Login">
    </form>
```


Above we can see the following in our Login Twig template:
    
    - the HTML `<form>` open tag, which we see submits via HTTP `POST` method
    
        - no action is given, so the form will submit to the same URL as displayed the form (`/login`), but with a `POST` method
    
    - add the security CSRF token as a hidden form variable    
    
    
    - display of any Twig `error` variable received
    
    - the `username` label and text input field
    
        - with 'sticky' form last username value (`last_username`) if any found in the Twig variables
    
    - the `password` label and password input field
    
    - the submit button named `Login`

## Custom login form when attempting to access `/admin`

See Figure \ref{custom_login_form} to see our custom login form in action.

![Screenshot of custom login form. \label{custom_login_form}](./03_figures/part06_security/5b_custom_login.png){ width=75% }


## Path for successful login

If the user visits the path `/login` directly in the browser, Symfony needs to know where to direct the user if login is successful. This is defined in method `onAuthenticationSuccess` in class `Security/LoginFormAuthenticator`. If no redirect is defined, then the `TODO` Exception will be thrown:

```php
        throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
```

Since we have a secure **admin** page, then let's redirect to route `admin`:

```php
    public function onAuthenticationSuccess(Request $reques§t, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('admin'));
    }
```

If you want to redirect to different pages, depending on the **role** of the newly logged-in user, then do the following:

- get the array of string roles from `$token` with `$token->getRoles()`

- add `IF`-statement(s) returning a different named route depending on their role, e.g. something like:

    ```php
        if(in_array('ROLE_ADMIN', $roles){        
            return new RedirectResponse($this->urlGenerator->generate('index_admin'));
        }        
        
        // else direct to basic staff homne page - or whatever ...
        return new RedirectResponse($this->urlGenerator->generate('index_staff'));
    ```


