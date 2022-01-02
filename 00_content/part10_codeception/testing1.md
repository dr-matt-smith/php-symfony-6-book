
# Unit testing in Symfony with Codeception


## Codeception Open Source BDD project

Codeception is a BDD (Behaviour Driver Design) open source project, to support acceptance (end-to-end, and unit testing for PHP project. The community supports its close integration with Symfony:

- Github
    
    - [https://github.com/codeception/codeception](https://github.com/codeception/codeception)

- Project home page

    - [https://codeception.com/](https://codeception.com/)
    
- Codeception Symfony docs

    - [https://codeception.com/docs/modules/Symfony](https://codeception.com/docs/modules/Symfony)

## Adding Codeception to an existing project (project `codeception01`)

For these examples we'll start with an existing project;

- form queries 3

    - [https://github.com/dr-matt-smith/php-symfony-5-book-codes-security-08-twig-login-nav-links](https://github.com/dr-matt-smith/php-symfony-5-book-codes-security-08-twig-login-nav-links)


So do the following:

1. open the Symfony project you are going to add testing to

    - if it's an older project, then it's always a good idea to run `composer update` to ensure your project has the most up-to-date libraries in the `/vendor` folder

1. since we are now in a **TESTING** mode, immediately change the `.env` file adding the suffix `test` to the database name - we don't test on a project's **production** database !

    ```
        DB_USER=root
        DB_PASSWORD=passpass
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_NAME=security5test
        DATABASE_URL=mysql://${DB_USER}:${DB_PASSWORD}@${DB_HOST}:${DB_PORT}/${DB_NAME}
    ```
    
    - delete any existing `/src/Migrations` folder
    
    - then create the new database with `doctrine:database:create`, and make and run a migrationb, and run your fixtures 
    
    - run the webserver, and you should have the project you are about to test up and running ...

1. Add Codeception via composer

    ```bash
        $ composer req codeception/codeception --dev
    
        Using version ^4.4 for codeception/codeception
        ./composer.json has been updated
        Loading composer repositories with package information
        Updating dependencies (including require-dev)
        Package operations: 118 installs, 0 updates, 0 removals
        
          - Installing .... packages ...
          
        Writing lock file
        Generating autoload files
        Symfony operations: 2 recipes (d7cfe8af216b9f6fdaf106b4fa7854e0)
          - Configuring phpunit/phpunit (>=4.7): From github.com/symfony/recipes:master          
    ```
    
    - you'll get a **WARNING** that you are not installing an 'official' Symfony package, but one contributed by the open source community:
    
        ```bash

          Symfony operations: 2 recipes (2b0042df2af2afd11fad98fcd4c2368b)
            - Configuring phpunit/phpunit (>=4.7): From github.com/symfony/recipes:master
            -  WARNING  codeception/codeception (>=2.3): From github.com/symfony/recipes-contrib:master
              The recipe for this package comes from the "contrib" repository, which is open to community contributions.
              Review the recipe at https://github.com/symfony/recipes-contrib/tree/master/codeception/codeception/2.3
          
              Do you want to execute this recipe?
              [y] Yes
              [n] No
              [a] Yes for all packages, only for the current installation session
              [p] Yes permanently, never ask again for this project
              (defaults to n): y

        ```

1. answer 'y' (YES!):

    ```bash
          - Configuring codeception/codeception (>=4.1): From github.com/symfony/recipes-contrib:master
        ocramius/package-versions:  Generating version class...
        ocramius/package-versions: ...done generating version class
        Executing script cache:clear [OK]
        Executing script assets:install public [OK]
        
        Some files may have been created or updated to configure your new packages.
        Please review, edit and commit them: these files are yours.
                                                                                                     
                                                                                             
         Adding phpunit/phpunit as a dependency is discouraged in favor of Symfony's PHPUnit Bridge. 
                                                                                                     
        
          * Instead:
            1. Remove it now: composer remove --dev phpunit/phpunit
            2. Use Symfony's bridge: composer require --dev phpunit
    ```
    
    - we see the 'bootstrapping' process for Codeception, so you don't need to bootstrap it yourself (which may be required if using Codeception for a non-Symfony project):
    
        ```bash
             Bootstrapping Codeception 
            File codeception.yaml created       <- global configuration
            tests/unit created                  <- unit tests
            tests/unit.suite.yaml written       <- unit tests suite configuration
            tests/functional created            <- functional tests
            tests/functional.suite.yaml written <- functional tests suite configuration
            tests/acceptance created            <- acceptance tests
            tests/acceptance.suite.yaml written <- acceptance tests suite configuration
            Codeception is installed for acceptance, functional, and unit testing
            Next steps:
            1. Edit tests/acceptance.suite.yaml to set url of your application. Change PhpBrowser to WebDriver to enable browser testing
            2. Edit tests/functional.suite.yaml to enable a Doctrine module if needs.
            3. Create your first acceptance tests using vendor/bin/codecept g:cest acceptance First
            4. Write first test in tests/acceptance/FirstCest.php
            5. Run tests using: vendor/bin/codecept run
      
        ```
        

1. Notice the message from Symfony about `phpunit/phpunit` bneing **discouraged**, saying we should use the Symfony PHPUnit **Bridge**:

    ```bash
         Adding phpunit/phpunit as a dependency is discouraged in favor of Symfony's PHPUnit Bridge. 
        
          * Instead:
            1. Remove it now: composer remove --dev phpunit/phpunit
            2. Use Symfony's bridge: composer require --dev phpunit    
    ```
    
    - it's always a good idea to do what is recommended, so first run the `composer remove ...` command:
    
        ```bash
            $ composer remove --dev phpunit/phpunit
            
            Nothing to install or update

        ```
        
    - then run the `composer require ...` command to add the Symfony bridge:
    
        ```bash
            $ composer require --dev phpunit  
            
            Nothing to install or update

            ...
        ```
      
    - however, it looks like PHPUnit wasn't installed as part of Codeception (it used to be), so this seems to be an out of date warning, still we did what was recommended ..

## Installing the required modules

If you test your setup by running Codeception now with `/vendor/bin/codecept run`, you'll get some error messages about missing modules. So let's avoid that by adding those modules before trying to run Codeception. 

We need 3 extra Codeception modules to use Codeception with Symfony:

- asserts:

    ```bash
    composer req --dev codeception/module-asserts 
    ```

- phpbrowser
    
    ```bash
    composer req --dev codeception/module-phpbrowser
    ```

- Symfony
    
    ```bash
    composer req --dev codeception/module-symfony
    ```

    
## What Codeception has added to our project

You should now have added to your project the following:

- file `codeception.yaml` in main project folder file:

    ```yaml
        namespace: App\Tests
        paths:
            tests: tests
            output: tests/_output
            data: tests/_data
            support: tests/_support
            envs: tests/_envs
        actor_suffix: Tester
        extensions:
            enabled:
                - Codeception\Extension\RunFailed
        params:
            - .env
    ```

    - NOTE: the default URL for testing is: `localhost:8000` - you may need to change this if using a web server other than the Symfony one ...
	
- new folder `/tests` - you'll create your tests in here

- new folder `vendor/bin/` containing executable: `codecept`

    - you run command line Codeception commands with:

        ```bash
            vendor/bin/codecept <command>
        ```

- you may also have a PHPUnit configuration file `phpunit.xml.dist`

- inside the `/tests` folder you'll find a configuration file for Acceptance tests `` containing the following. The default URL for testing is: `localhost:8000` - you may need to change this if using a web server other than the Symfony one ...

    ```yaml
    # Codeception Test Suite Configuration
    #
    # Suite for acceptance tests.
    # Perform tests in browser using the WebDriver or PhpBrowser.
    # If you need both WebDriver and PHPBrowser tests - create a separate suite.
    
    actor: AcceptanceTester
    modules:
        enabled:
            - PhpBrowser:
                url: http://localhost:8000
            - \App\Tests\Helper\Acceptance
    ```

  
  
