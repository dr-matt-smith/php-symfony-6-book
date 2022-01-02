
# Publishing with Fortrabbit.com


## Main steps for PAAS publishing with Fortrabbit

Having setup our Symfony project (with `.htaccess` and renamed `.env` MySQL variables and a fresh DB migration), we need to be able to the following to get our Symfony project published with Fortrabbit:

- create a Fortrabbit account, and setup SSH security keys so our local computer can securely communicate with the Fortrabbit servers 

    - follow the steps in the [Fortrabbit SHH keys documentation](https://help.fortrabbit.com/ssh-keys)
    
    - see Figure \ref{sshkeys}

![Fortrabbit ssh setup documentation.\label{sshkeys}](./03_figures/part11/8_ssh_setup.png)

- link a local project to a Fortrabbit Github repository (seee steps in next section)

    - so we can **push** our code to the Fortrabbit repo to trigger a rebuild of the project
    
- work in an SSH terminal to run migrations & fixtures etc. (see later this chapter)


## Create a new Fortrabbit Symfony project

It's very straightforward to setup a new Symfony project on Fortrabbit:

1. Create a new PHP app in Fortrabbit. See Figure \ref{newProject}.

1. Choose Symfony project type.  See Figure \ref{sfType}.

1. Choose European data centre.  See Figure \ref{eu}.

![Create new app.\label{newProject}](./03_figures/part11/4_createApp.png)

![Choose Symfony project type.\label{sfType}](./03_figures/part11/5_chooseSymfony.png)

![Choose EU data centre.\label{eu}](./03_figures/part11/6_eu.png)


## Temporarily set project environment to `dev` so we can load DB  fixtures

There is an issue in that Fortrabbit sets the Symfony environment as `prod`, for production, i.e. a running live website. This excludes things like the Symfony profiler, tests and so on. However, it also stops us from being able to load **fixtures** via Doctrine. This makes sense, since we don't want to reset the database for a live system.

While there are several ways to allow us to load fixtures, the simplest is to temporarily change the Fortrabbit project to the `dev` environnent. We do this by editing the projects **Custome ENV variable** `APP_ENV` from `prod` to `dev`. See Figures \ref{editEnv} and \ref{devEnvironment}.

![Edit ENV vars for Fortrabbit project.\label{editEnv}](./03_figures/part11/15_editEnv.png)

![Changing environment from `prod` to `dev`. \label{devEnvironment}](./03_figures/part11/3_devEnvironment.png)

NOTE: Don't forget to change this back to `prod` after you have loaded fixtures ...

## Getting a linked Git project on your local computer

1. clone the repo to your local machine. See Figure \ref{cloneLocal}.

    - NOTE: This will be an **empty** repository folder, apart from the hidden `.git` folder - you'll get a message warning you about this when you clone it to your computer
    
    - you'll copy your project files **into** this empty folder, to push back up to the Fortrabbit repo

![Clone git repo.\label{cloneLocal}](./03_figures/part11/5_clone_git.png)

1. copy your project files into the newly created cloned project folder

1. add all the new files to the current snapshot:

    `git add .`

1. Create the commit snapshot with a short message (the message doesn't matter):

    `git commit -m "added files to project"`

1. Push all these new files and folders up to the Fortrabbit repository:

    `git push`

NOTE: You'll see some Fortrabbit output when it responds to the new commit to the repositories `master` branch - CD (Contiuous Deployment) in action:

```bash
    $ git push
        Enumerating objects: 92, done.
        Counting objects: 100% (92/92), done.
        Delta compression using up to 12 threads
        Compressing objects: 100% (81/81), done.
        Writing objects: 100% (92/92), 49.53 KiB | 3.10 MiB/s, done.
        Total 92 (delta 3), reused 0 (delta 0)
        
        Commit received, starting build of branch master
        
        –––––––––––––––––––––––  ƒ  –––––––––––––––––––––––
        
        B U I L D
        Checksum:
          814af6dc566dad405a8d4f13bc990a5e6dda6be7
        
        Deployment file:
          not found
        
        Pre-script:
          not found
          0ms
        
        Composer:
          - - -
          Loading composer repositories with package information
          Installing dependencies (including require-dev) from lock file
          Package operations: 109 installs, 0 updates, 0 removals
            - Installing ocramius/package-versions (1.5.1): Downloading (100%)
            - Installing symfony/flex (v1.6.2): Downloading (100%)
          
          Prefetching 107 packages 
            - Downloading (100%)
          
            - Installing doctrine/lexer (1.2.0): Loading from cache
            ... lots of installing for the first push ...
            .............................................
          
          Executing script cache:clear [OK]
          Executing script assets:install public [OK]
          - - -
          10s 839ms
        
        Post-script:
          not found
          0ms
        
        R E L E A S E
        
        Packaging:
          3s 796ms
        
        Revision:
          1585658858201186125.814af6dc566dad405a8d4f13bc990a5e6dda6be7
        
        Size:
          6.9 MB
        
        Uploading:
          217ms
        
        Build & release done in 14s 863ms, now queued for final distribution.
        
        –––––––––––––––––––––––  ƒ  –––––––––––––––––––––––
        
        To deploy.eu2.frbit.com:symfony-demo.git
         * [new branch]      master -> master 
```

## Visit site to see if published (although may be DB errors)

Now visit your website - via link at Fortrabbit. See Figure \ref{visitSite}.

- NOTE: Since the database isn't setup yet, you'll get an error if your homepage tries to list any DB data

![Visit published website link.\label{visitSite}](./03_figures/part11/6_preview.png)


## Connect command-line terminal to Fortrabbit project via SHH

Via an SSH terminal we can run the migration and load fixtures for our Fortrabbit app. Note, the database has already been created for us, so don't try to run a `database:create` Doctrine command ...

Use the provided SSH connection command to connect to Fortrabbvit projet in a terminal. See Figure \ref{sshconnect}.

![SSH connect command.\label{sshconnect}](./03_figures/part11/12_ssh.png)

## Use SSH to run DB migrations
We can now run the migration command `doctrine:migrations:migrate`. See Figure \ref{migration}.

![Running migration in SSH terminal.\label{migration}](./03_figures/part11/20_migrations.png)

## Use SSH to load fixtures
We can now run the migration command `doctrine:fixtures:load`. See Figure \ref{fixtures}.

![Loading fixtures in SSH terminal.\label{fixtures}](./03_figures/part11/22_fixtures.png)

NOTE:
- if you change fixtures, you'll need to repeat this after pushing the updated code to the Fortrabbit repo

- don't forget to change the project environment back to `prod` if you want a secure, efficient running web application

## Use Doctrine query to check DB contents

We can use the Doctrine `doctrine:query:sql "<SQL>"` command in an SSH terminal to check the contents of the database. See Figure \ref{doctrineSQL}.

![SSH terminal running SQL query via `doctrine:query:sql`.\label{doctrineSQL}](./03_figures/part11/21_ssh_sql.png)


## MySQL queries using SSH tunnel ...

You can use an SSH tunnel to use your local MySQL terminal to connect to and query the remote Fortrabbit database

- follow the MySQL help steps from the Fortrabbit App dashboard

![Fortrabbt MySQL help.](./03_figures/part11/10_mysql_help.png)

![Remote SSH MySQL terminal connection.](./03_figures/part11/9_mysql.png)

## Published website 

If all has gone well, you should now have a live published Symfony website.

NOTE: If you can see the Symfony profiler debug footer, then you've forgotton to change the envrionment back to `prod` !!!!

![screeshot of published website.](./03_figures/part11/30_published.png)
