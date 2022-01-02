
# Encoding the user passwords


## UPDATE STATUS
NOTE - these chapters are currently being updated for Symfony 4

The status of this chapter is:

- out of date (old Symfony 3)   <<<<<<<< current status
- updated and okay for now
- very up to date



## Encoding the user passwords (`project19`)

It is **not** good practice to store user passwords as plain text, so let's change the encoder and store hashed passwords instead. The Symfony introduction to security documentation page tells us how to encode user passwords:

- (Encoding user passwords)[http://symfony.com/doc/current/security.html#c-encoding-the-user-s-password]

First, in `security.yml` we need to change the encoder from `plaintext` to BCrypt as follows:

```yaml
    encoders:
        Symfony\Component\Security\Core\User\User:
            algorithm: bcrypt
            cost: 12
```

Now we need to replace the plaintext passwords for our 3 users (`user`, `admin` and `matt`) with their BCrypted passwords. We can do this by using the Symfony command line tool that will tell us the encoded string for a given password (using the encoder specified in `security.yml`). So we'll need to run this 3 times (and copy-paste the encoded password into our `security.yml` YAML file each time:

```bash
    $ php bin/console security:encode-password
```


Figure \ref{encoding} shows the interactive password encoding session for password `user`:

![CLI password encoding (for password `user`). \label{encoding}](./03_figures/authentication/28_encoding_sm.png)

So the full listing for our `security.yml` configuration, stating the encoder and the hashed passwords looks like this:

```yaml
    security:
        encoders:
            Symfony\Component\Security\Core\User\User:
                algorithm: bcrypt
                cost: 12

        providers:
            in_memory:
                memory:
                    users:
                        user:
                            password: $2y$12$pUaaC6cwub1NkwNvSm/FnuR3rli8YgjIg1Di68hqX4J1TnGpLc2AC
                            roles: 'ROLE_USER'
                        admin:
                            password: $2y$12$ROCN/MhD6U0Rsr0xsrHT/.RETqtgm8nQmdbOsC2o4w4RyHrUhXcvS
                            roles: 'ROLE_ADMIN'
                        matt:
                            password: $2y$12$4UWrrc1pkskcCMDpcj4XzeLVsn5Tlk4zkQJAyrSaoDnOnY1wgHUH2
                            roles: 'ROLE_ADMIN'

        firewalls:
            dev:
                pattern: ^/(_(profiler|wdt)|css|images|js)/
                security: false

            default:
                anonymous: ~
                form_login:
                    login_path: login
                    check_path: login

                logout:
                    path:   /logout
                    target: /
```

## Those nice people at KnpUniversity...

If you want to go further and really learn Symfony security, with your own User entity and database storage etc. then a great place to start would be the KnpUniversity video tutorial at:

- [KnpUniversity - Symfony Security: Beautiful Authentication, Powerful Authorization](https://knpuniversity.com/screencast/symfony-security)
