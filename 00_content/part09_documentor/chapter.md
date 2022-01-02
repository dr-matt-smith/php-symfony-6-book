
# PHPDocumentor

## Why document code?

Other people will use your code. And you’ll forget things after a few weeks / months / years...
So we should create documents describing our code.

There are several good reasons to document your code:

1. It makes you **think** about the code

    You might even improve the code having thought about it

1. Writing about code **before** writing the code may lead to better code design

1. It makes you remember **your** code may be used / read by other people

1. It means you don't have to **remember** what things do or why

1. Automated tools can help check for missing documentation

1. It's a handy way to scan your code for TODO comments  .

## Self-documenting code

Perhaps the simplest way to document code is to insert special comments in the code itself - so the source code files are used to generate the documentation pages about themselves.

'Docblocks’ are special comments that **precede** the element they describe - they are used by most automated documentation tools.

The PHPDocumentor 2 is a tool to generate HTML documentation pages from these comments

- and if you use an IDE like PHPStorm to speed up writing these special comments...

There are also other PHP code documentation systems out there, incuding:

- the [Sami](https://github.com/FriendsOfPHP/sami) Friends of PHP documentation systems

- [Api Gen](https://github.com/ApiGen/ApiGen)

- and of course [Doxygen](http://www.stack.nl/~dimitri/doxygen/)

## PHPDocumentor 2

There are several automated tools for supporting PHP code documentation, one of the most popular is PHPDocumentor 2, which is the one described in this chapter.

Learn more about PHPDocumentor 2 at their website:

- [phpdoc.org](https://www.phpdoc.org/)

## Installing PHPDocumentor 2 - the PHAR

THe Composer install is a bit big, so it is recommended to just add the PHAR (PHP Archive) either to your project, or globally (somewhere in your system path).

Download the PHAR from their website and either copy into your Symfony project folder, or to some standard folder that is in your CLI execution path.

- [phpdoc.org/phpDocumentor.phar](https://www.phpdoc.org/phpDocumentor.phar)

## Installing PHPDocumentor 2 - via Composer

Install via Composer with the following:

```bash
    $ composer req --dev phpdocumentor/phpdocumentor
```


## DocBlock comments

The PHPDocumentor is driven by analysing 'DocBlock' comments in your code. A DocBlock comment looks as follows:

```php
    /**
     * This is a DocBlock.
     */
    public function indexController()
    {
    }
```

They are multi-line comments that start with a double asterisk `/**`.

## Generating the documentation

The PHPDocumentor needs to know at least 2 things:

- where is the PHP source code containing the documentation comments

- where do you want the documentation files to be output

These are specified using the `-d` (PHP source directory), and `-t` (output director) as follows.

So, for example, so analyse **all** files in directory `/src` and output to `/docs` write:

```bash
    $ php phpdoc -d src -t docs
```

To limit the code analysed to just `/src/Controller`, `/src/Util` and `/src/Entity` we would give 3 `-d` arguemnts as follows:

```bash
    php phpdoc.phar -d src/Controller -d src/Entity -d src/Util -t docs
```


## Using an XML configuration file `phpdoc.dist.xml`

The simplest way to record your PHPDocumentor configuration options is with an XML file `phpdoc.dist.xml`.

Here is a simple config file:

```xml
    <phpdoc>
        <parser>
            <target>./docs</target>
        </parser>
        <transformer>
            <target>./docs</target>
        </transformer>
        <files>
            <directory>./src</directory>
        </files>
        <transformations>
            <template name="responsive-twig"/>
        </transformations>
    </phpdoc>
```

This will output the HTML documentation pages in the `responsive-twig` theme, into directory `./docs`, for all PHP classes found in `./src`.


To limit the code analysed to just `/src/Controller`, `/src/Util` and `/src/Entity` we could use the following XML file:

```xml
    <phpdoc>
        <parser>
            <target>./docs</target>
        </parser>
        <transformer>
            <target>./docs</target>
        </transformer>
        <files>
            <directory>./src/Controller</directory>
            <directory>./src/Util</directory>
            <directory>./src/Entity</directory>
        </files>
        <transformations>
            <template name="responsive-twig"/>
        </transformations>
    </phpdoc>
```

NOTE: If there is also a file `phpdoc.xml`, any settings in this will overrided those in `phpdoc.dist.xml`. So, for example, an individual might have some particular settings they prefer defined in their `phpdoc.xml` file, but then could use the team's or organisation's default `phpdoc.dist.xml` for all other settings...

## WARNING - PHPStorm default comments

Note that the default comments for a new PHP class provided by PHPStorm will foul-up your documentatin generation:

```php
    <?php
    /**
     * Created by PhpStorm.
     * User: matt
     * Date: 20/03/2018
     * Time: 07:42
     */
```

So delete these default file header comments if you are using the PHPDocumentor.

## TODO - special treatment

PHPDocumentor can hoover up special markers, such as `TODO`, and report them for you. In fact `TODO` are so handy they get special treatment.

You can mark todo's with with:

```php
    * TODO: fix that bug for stack overflow
```

or with the `@todo` annotation:

```php
    * @todo fix that bug for stack overflow
```

