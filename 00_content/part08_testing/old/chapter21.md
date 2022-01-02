
# Codeception Symfony testing


## UPDATE STATUS
NOTE - these chapters are currently being updated for Symfony 4

The status of this chapter is:

- out of date (old Symfony 3)   <<<<<<<< current status
- updated and okay for now
- very up to date



## Codeception (`project23`)

Codeception is a BDD approach to TDD. TDD is Test-Driven Development, where we wr§ite tests then write code to pass the tests. BDD is Behaviour-Driven Development, where the tests are expressed in terms of 'user actions'.

Learn more about Codeception at their websiteL

- [Codeception website](http://codeception.com/)

## Setting up Codeception for a Symfony project

First create a new Symfony project, e.g.:

```bash
    $ symfony new project20
```

or using Composer:

```bash
    $ composer create-project symfony/framework-standard-edition project20
```

Now download the Codeception PHAR (PHp ARchive) from the Codeception download page:

- [Codeception download/install page](http://codeception.com/install)

Copy this PHAR to the root of your new project - e.g. to `/project20/`.

Now **bootstrap** codeception:

```bash
    php codecept.phar bootstrap
```

This should create the following:

```
    /tests
    codeception.yml
```

Now generate a **Welcome** acceptance test:

```
    php codecept.phar generate:cept acceptance Welcome
```

This should create file `WelcomeCept.php` in directory `/tests/acceptance/`. Codeception tests are called 'Cepts', so our Welcome test fort the home page has been named `WelcomeCept`. The default contents of a Cept are as follows, note Cepts are written in PHP, although with special class names to make it read in the form of a pseudocode use-case:

```php
    <?php
    $I = new AcceptanceTester($scenario);
    $I->wantTo('perform actions and see result');
```


Let's change the contents of this to the following (since our Symfony home page displays text such as 'Welcome to Symfony 3.2.4. Your application is now ready.':

```php
    $I = new AcceptanceTester($scenario);
    $I->wantTo('ensure that frontpage works');
    $I->amOnPage('/');
    $I->see('Welcome to');
    $I->see('Symfony');
```

This test attempts to request URL "/" from the web host (we'll define that next). This test also attempts to see text 'Welcome to' and 'Symfony' in the HTML page content (which it should on the home page).

We need to tell Codeception the host and port to look at when simulating user interactions with the website. We do this by editing the vales in `/tests/acceptance.suite.yml/`. I'm running my webserer on port 8000, so I set my `acceptance.suite.yml` to the following:

```yaml
    class_name: AcceptanceTester
    modules:
        enabled:
            - PhpBrowser:
                url: http://localhost:8000
            - \Helper\Acceptance
```

We now run our Symfony application server, and test our Codeception acceptance test with the followng command line statement:

```bash
    php codecept.phar run
```

Figure \ref{welcome_cept} shows the CLI output when we run (this hopefully passing) test.

**NOTE** If you are **not** running the web server,  then you'll just get an error!

![WelcomeCept test passing. \label{welcome_cept}](./03_figures/testing/02_codecept_sm.png)



