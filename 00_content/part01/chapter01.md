
# Introduction

## What is Symfony?

It's a PHP 'framework' that does loads for you, if you're writing a secure, database-drive web application.

## What to I need on my computer to get started?

I recommend you install the following:

- PHP 8.1 (download/install from [php.net](https://php.net/))

   - (it will work with 8.0.2+, but 8.1 allows enumerations which is handy ...)

- a good text editor (I like [PHPStorm](https://www.jetbrains.com/phpstorm/specials/phpstorm/phpstorm.html?&gclid=CJTK_8SDrtICFWq-7Qodh98NpQ&gclsrc=aw.ds.ds&dclid=CNPY28WDrtICFQGn7QodqekBWg), but then it's free for educational users...)

- [Composer](https://getcomposer.org/) (PHP package manager - a PHP program)

- the Symfony command-line tool

    - [https://symfony.com/download](https://symfony.com/download)

The following are also a good idea:

- a MySQL database server - e.g. MySQLWorkbench Community is free and cross-platform

- Git - see [GitforWindows](https://git-scm.com/download/win)

or ... you could use something like [Cloud9](https://c9.io/dr_matt_smith), web-based IDE. You can get started on the free version and work from there ...

or Symfony's (not free) SymfonyCloud PHP-as-a-Service (PaaS):

- [https://symfony.com/cloud/](https://symfony.com/cloud/)

Learn more about the software needed for Symfony developmnent in Appendix \ref{appendix_software}. For steps in installing PHP and the other software, see Appendices \ref{appendix_php} and \ref{appendix_software_setup}.

## Check sysstem requiremnets

Once you've installed the Symfony command-line tool, check your system setup with the `symfony check:requirements` command:

```bash
$ symfony check:requirements

Symfony Requirements Checker
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

> PHP is using the following php.ini file:
/usr/local/etc/php/8.1/php.ini

> Checking Symfony requirements:

.............................

                                              
 [OK]                                         
 Your system is ready to run Symfony projects 
                                              
```

## How to I get started with a new Symfony project

In a CLI (Command Line Interface) terminal window, `cd` into the directory where you want to create your Symfony project(s). Then create a new Symfony empty web application project, named `project01` (or whatever you wish) by typing:


```bash
    $ symfony new --webapp project01
```

NOTE: If for some reason you don't have the Symfony command line tool installed, you can also create a project using Composer:

```bash
    $ composer create-project symfony/website-skeleton project01
```

You should see the following, if all is going well:


```bash
$ symfony new --webapp project01
* Creating a new Symfony project with Composer
  (running /usr/local/bin/composer create-project symfony/website-skeleton /Users/matt/project01)

* Setting up the project under Git version control
  (running git init /Users/matt/project01)
                                                                                                                        
 [OK] Your project is now ready in /Users/matt/project01    
```


Another way to get going quickly with Symfomy is to download one of the projects accompanying this book ...

## Where are the projects accompanying this book?

All the projects in this book are freely available, as public repositories on Github as follows:

- [https://github.com/dr-matt-smith/php-symfony-6-book/codes](https://github.com/dr-matt-smith/php-symfony-6-book/codes)

To retrieve and setup a sample project follow these steps:

1. download the project to your local computer (e.g. `git clone URL`)

1. change (`cd`) into the created directory

1. type `composer install` to download any required 3rd-party packages into a `/vendor` folder

    - NOTE: `composer install` installs the **same** component versions as defined in the `composer.lock` file. `composer update` will attempt to install the **most up-to-date stable** versions of the components in the `composer.json` file.

1. Then run your web server (see below) and explore via a web browser


## How to I run a Symfony webapp?

### From the CLI
At the CLI (command line terminal) ensure you are at the base level of your project (i.e. the same directory that has your `composer.json` file), and type the following to run 

```bash
    $ symfony serve
```

NOTE: This is short for `symfony server:start`

If you don't have the Symfony command line tool installed you could also use the PHP built-in web server:

```bash
    $ php -S localhost:8000 -t public
```

Then open a web browser and visit the website root at `http://localhost:8000`.

See Figure \ref{default_home_page} for a screenshot of the default Symfony 5 home page (with a message saying you've not configured a home page!).

![Screenshot default Symfony emopty new project home page. \label{default_home_page}](./03_figures/chapter02/0_default_page_sf.png)



### From a Webserver application (like Apache or XAMPP)

If you are running a webserver (or combined web and database server like XAMPP or Laragon), then point your web server root to the project's  `/public` folder - this is where public files go in Symfony projects.

## It isn't working! (Problem Solving)

If you have trouble with running Symfony, take a look at Appendix \ref{appendix_problem_solving}, which lists some common issues and how to solve them.

## Can I see a demo project with lots of Symfony features?

Yes! There is a full-featured Symfony demo project. Checkout Appendix \ref{appendix_sf_demo} for details of downloading and running the demo and its associated automated tests.

## Any free videos about SF to get me going?

Yes! Those nice people at Symfonycasts have released a bunch of free videos all about Symfony (and OO PHP in general).

So plug in your headphones and watch them, or read the transcripts below the video if you're no headphones. A good rule is to watch a video or two **before** trying it out yourself.

You'll find the video tutorials at:

- [https://symfonycasts.com/tracks/symfony](https://symfonycasts.com/tracks/symfony)

(ask Matt to ask his contacts in Symfonycasts to try to get his students a month's free access ... if your Github Education Pack free acess has expired ...)
