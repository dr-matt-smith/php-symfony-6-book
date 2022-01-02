

# Publish via Fortrabbit (PHP as a service)\label{appendix_fortrabbit}

## SSH key

Ensure your computer has an SSH key setup, since you'll need this for secure communication with Fortrabbit.

### Windows SSH key setup

A guide to generate SSH keys on Windows can be found at:

- [http://guides.beanstalkapp.com/version-control/git-on-windows.html](http://guides.beanstalkapp.com/version-control/git-on-windows.html)

### Mac SSH key setup

A simple guide to generating SSH keys for the Mac can be found at:

- [https://secure.vexxhost.com/billing/knowledgebase/171/How-can-I-generate-SSH-keys-on-Mac-OS-X.html](https://secure.vexxhost.com/billing/knowledgebase/171/How-can-I-generate-SSH-keys-on-Mac-OS-X.html)

### Linux SSH key setup

A guide to generating SSH keys for the Mac can be found at:

- [https://www.ssh.com/ssh/keygen/](https://www.ssh.com/ssh/keygen/)

### Fortrabbit

Do the following:

1. On your Fortrabbit account page click to add a new SSH key. See Figure \ref{ssh1}.

    ![Screenshot - click to add new SSH key. \label{ssh1}](./03_figures/appendices/99_ssh1.png)

1. Paste in SSH key. See Figure \ref{ssh2}.

    - or import from your Githiub account ...


    ![Screenshot - paste in SSH key. \label{ssh2}](./03_figures/appendices/99_ssh2.png)



## Creating a new web App

Do the following:

1. Go to your account Dashboard, and click to create a new web App. See  Figure \ref{app1}.

    ![Screenshot - click to create new app. \label{app1}](./03_figures/appendices/99_app1.png)

1. Enter an app name, e.g. `myproject`.  See  Figure \ref{app2}.

    ![Screenshot - enter new app name. \label{app2}](./03_figures/appendices/99_app2.png)

1. Choose `Symfony` project framework from the `Choose a sofware` page.   See  Figure \ref{app3}.

    ![Screenshot - choose Symfony project type. \label{app3}](./03_figures/appendices/99_app3.png)

1. Choose EU (Ireland) Data Center. See  Figure \ref{app4}.

    ![Screenshot - choose EU (Ireland) Data Center. \label{app4}](./03_figures/appendices/99_app4.png)

1. Choose €5 Light Universal Stack.  See  Figure \ref{app5}.

    ![Screenshot - choose cheapest (Light Universal) Stack . \label{app5}](./03_figures/appendices/99_app5.png)

Your new Web App should now have been created!.

## Cloning and populating your Git repo

Fortrabbit has now created a unique `git` repo on its servers. Do the following to make your deploy your existing Symfony project with this repo:

1. Click the `How to deploy git` link. See  Figure \ref{git1}.

    ![Screenshot - click `how to deploy git` link. \label{git1}](./03_figures/appendices/99_git1.png)

1. You'll now see a page with Git commands, customised to your repo/App name - so you can copy-and-paste them into a CLI Terminal.  See  Figure \ref{git2}.

    ![Screenshot - Git customized command page. \label{git2}](./03_figures/appendices/99_git2.png)

1. Git `clone` the Fortrabbit repo to your local machine, then `cd` into it (you'll need to use your SSH password ...).  See  Figure \ref{git3}.

    ![Screenshot - clone repo to local machine. \label{git3}](./03_figures/appendices/99_git3.png)

1. Copy into your local folder the files for your Symfony project.

    - NOTE: do the following for a clean database setup on your local machine (with migrations ready to use on remote deployment database...)
    
        - Change the database name in `.env` to a new database
        
        - delete any exiting `src/Migrations` directory
        
        - create the database schema with `php bin/console doctrine:database:create`
        
        - create migrations class with `php bin/console doctrine:migrations:diff`
        
        - execute the migrations to create the table schema with `php bin/console doctrine:migrations:migrate`
        
        - load the fixtures fixtures into the local database with `php bin/console doctrine:fixtures:load`
        
        - test the fixtures with an **SQL** command, e.g. to list users from table `user` you could execute: `php bin/console doctrine:query:sql "select * from user"`




## Fixing the Fixtures issue

For first-time database setup of a deployed project you'll usually wish to create the fixtures in the database. However, the default setting of the **environment** for a Fortrabbit project is **production**.  See  Figure \ref{env1}.

![Screenshot - Fortrabbit `PROD` environment setting. \label{env1}](./03_figures/appendices/99_env1.png)

However, the settings for when the Doctrine Fixtures 'bundle' should be included are only for development and test environments. So if we want to be able to run Fixture insertion into the database, then we have to add a statement that Fixtures should be available for all environments.

Add this line to file `/config/bundles.php`, before the end of the array `]`:

```pho
    Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => ['all' => true]
```

So your listing for `/config/bundles.php` should now looks something like this:

```php
    <?php
    
    return [
        Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
        Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle::class => ['all' => true],
        ... etc. lots more packages listed ...
        Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
        Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => ['all' => true]
    ];    
```

## Fixing the Apache `.htaccess` issue

Fortrabbit servers PHP projects using the Apache Open Source web server. For the URL patterns to be correct parsed it needs special URL-rewrite rules in a file `.htaccess` in the directory `//public`. To create this file you can simply use Composer to require the dedicated `symfony/apache-pack` package:

```bash
    composer req symfony/apache-pack
```

NOTE: You may be asked to say `yes` to install this package since it's been created by the community (and isn't an offical package at the time of writing ...)

## Adding, committing and pushing the project files to the repo

We are now ready to upload our production-ready project files to the Fortrabbit repo.

1. Add the new/changed files for staging with `git add .`:

    ```bash
    matt$ git status
    
    On branch master
    
    No commits yet
    
    Untracked files:
      (use "git add <file>..." to include in what will be committed)
    
        .env
        .gitignore
        README.md
        bin/
        composer.json
        composer.lock
        config/
        public/
        src/
        symfony.lock
        templates/
    
    nothing added to commit but untracked files present (use "git add" to track)
    
    matt$ git add .
    ```

1. Commit with first commit message with `git commit -m "<msg>"`:
    
    ```bash
        matt$ git commit -m "first commit"
            [master (root-commit) 77946cc] first commit
            59 files changed, 6819 insertions(+)
            create mode 100755 .env
            create mode 100755 .gitignore
            create mode 100755 README.md
            ... etc. for lots of files
            create mode 100755 templates/security/success.html.twig
            create mode 100755 templates/student/index.html.twig
    ```

1. Push the committed files to the Fortrabbit repo:


    ```bash
        matt$ git push -u origin master
        Enter passphrase for key '/Users/matt/.ssh/id_rsa': 
        Counting objects: 80, done.
        Delta compression using up to 12 threads.
        Compressing objects: 100% (75/75), done.
        Writing objects: 100% (80/80), 38.49 KiB | 3.21 MiB/s, done.
        Total 80 (delta 7), reused 0 (delta 0)
        
        Commit received, starting build of branch master
    ```

1. Automatically (since we have a new commit to the `master` branch) a new build on the Fortrabbit server will be triggered:

    ```bash
        –––––––––––––––––––––––  *ƒ  –––––––––––––––––––––––
        B U I L D
        
        Checksum:
          77946cc9c25fc7259e93080e89de48fc635c8a1e
        
        Composer:
          - - -
          Loading composer repositories with package information
          Installing dependencies (including require-dev) from lock file
          Package operations: 80 installs, 0 updates, 0 removals
            - Installing ocramius/package-versions (1.4.0): Downloading (100%)
            - Installing symfony/flex (v1.2.0): Downloading (100%)
          
          Prefetching 78 packages
            - Downloading (100%)
          
            - Installing symfony/polyfill-mbstring (v1.10.0): Loading from cache
                ... etc. Composer will install lots of files ...
            - Installing symfony/process (v4.2.3): Loading from cache
            - Installing symfony/web-server-bundle (v4.2.3): Loading from cache
          Generating autoload files
          
          ocramius/package-versions:  Generating version class...
          ocramius/package-versions: ...done generating version class
          
          Executing script cache:clear [OK]
          Executing script assets:install public [OK]
          
          - - -
          6s 268ms
        
        R E L E A S E
        
        Size:
          6.7 MB
        
        Uploading:
          207ms
        
        Build & release done in 7s 51ms, now queued for final distribution.
        
        –––––––––––––––––––––––  *ƒ  –––––––––––––––––––––––    
    ``` 

Note, from now on, we can simply use `git push` for any later committed changes to our source code.

## SHH CLI Termial to migrate and install DB fixtures

The final step is to migrate the database (using the migrations from our source code), and load any fixtures we require. We need to do this via an SSH secure terminal connection to Fortrabbit.

Do the following:

1. Use SSH to connect to the Fortrtabbit virtual Linux machine.  See Figure \ref{db1}.

    ![Screenshot - SSH connent to remote Fortrabbit system. \label{db1}](./03_figures/appendices/99_db1.png)

1. Migrate the database schema with `doctrine:migrations:migrate (and say 'yes' when asked). See Figure \ref{db2}.

    ![Screenshot - migrate the DB schema. \label{db2}](./03_figures/appendices/99_db2.png)

1. Load the fixtures with `doctrine:fixtures:load` (and say 'yes' when asked). See Figure \ref{db3}.

    ![Screenshot - load the fixtures into the DB. \label{db3}](./03_figures/appendices/99_db3.png)

1. Test the DB contents by listing users via SQL with `doctrine:query:sql "select * from user"`.  See Figure \ref{db4}.

    ![Screenshot - testing fixtures by selecting all users via SQL query. \label{db4}](./03_figures/appendices/99_db4.png)

## Symfony project should now by fully deployed

Your Symfony project should now be fully deployed and working, with DB fixtures and secure logins etc.  See Figure \ref{deployed}.

![Screenshot - deployed working Symfony project \label{deployed}](./03_figures/appendices/99_deployed.png)

