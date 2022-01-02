

# Custom AccessDeniedException handler

## Symfony documentation for 403 access denied exception

For details about this topic visit the Symfony documentation:

- [https://symfony.com/doc/current/security/access_denied_handler.html](https://symfony.com/doc/current/security/access_denied_handler.html)


## Declaring our handler  (project `security05`)

In `/config/packages/security.yml` we need to declare that the class we'll write below will handle access denied exceptions.

So we add this line to the end of our `main` firewall in `security.yml`:

```yaml
    access_denied_handler: App\Security\AccessDeniedHandler
```

So the full listing for our `security.yml` is now:

```yaml
security:
    encoders:
        App\Entity\User:
            algorithm: bcrypt

    providers:
        our_db_provider:
            entity:
                class: App\Entity\User
                property: username

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            anonymous: true
            provider: our_db_provider
            form_login:
                login_path: login
                check_path: login
            logout:
                path:   /logout
                target: /
            access_denied_handler: App\Security\AccessDeniedHandler
```

## The exception handler class

Now we needs to write our exception handler class in `/src/Security`.

Create new class `AccessDeniedHandler` in file `/src/Security/AccessDeniedHandler.php`:

```php
    namespace App\Security;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Security\Core\Exception\AccessDeniedException;
    use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

    class AccessDeniedHandler implements AccessDeniedHandlerInterface
    {
        public function handle(Request $request, AccessDeniedException $accessDeniedException)
        {
            return new Response('sorry - you have been denied access', 403);
        }
    }
```

That's it!

Now if you try to access `/admin` with `user/user` you'll see the message `sorry - you have been denied access' on screen. See Figure \ref{denied_exception}.

![Screenshot of Custom Twig access denied page. \label{denied_exception}](./03_figures/part06_security/11_denied_response.png){ width=75% }

Although it won't be generated through the Twig templating system - we'll learn how to do that next ...
