

# xDebug for Windows \label{appendix_xdebug}

## Steps for Windows

To setup xDebug for Windows you need:

1. to download the appropriate DLL for your PHP system into `C:\php\ext` (or elsewhere if you installed PHP somewhere else on your system)

1. add/uncomment the following line at the end of your `php.ini` file:

    ```
    zend_extensions = C:\php\ext\php_xdebug-2-6-0-7.1-vc14-x86_64.dll
    ```

    NOTE: The location / name of this file will depend on your PHP installation (see Wizard steps below)

## Steps for Linux/Mac

You can quickly confirm xDebug status with the following CLI command:

```bash
    $ php -ini|grep 'xdebug support'
    xdebug support => enabled
```

If you see 'enabled' then no further work is needed. Otherwise, the simplest way to get xDebug working is to use the wizard ...

## Use the xDebug wizard!

Perhaps the easiest way to setup xDebug is to follow the steps recommended by their 'wizard' at:

- [xDebug Windows wizard: https://xdebug.org/wizard.php](https://xdebug.org/wizard.php)

Figure \ref{wizard} shows a screenshot of the xDebug wizard web page output.

![Screenshot xDebug wizard output. \label{phpinfo}](./03_figures/appendices/wizard.png){ wiudth=75% }

## PHP Function `phpinfo()`

The `phpinfo()` output is a summary (as an HTML page) of your PHP setup. Figure \ref{phpinfo} shows a screenshot of a browser showing a PHP info page.

![Screenshot phpinfo in browser. \label{phpinfo}](./03_figures/appendices/phpinfo_sm.png){ wiudth=75% }

To use the 'wizard' you need to generate, copy and then paste the text output of `print phpinfo()` into the web page form.

To get the output from `phpinfo()` you can do one of these:

- at the CLI type
    ```
    `php -r 'print phpinfo();' > info.html
    ```

    and then get the contents of file `info.html`

- create a temporary directory, containing PHP file `index.php` that contains

    ```php
        <?php
        print phpinfo();
     ```

     run your webserver and visit the directory. Then copy and paste the contents of your browser window

- in Symfony you could create a temporary controller method that outputs a Reponse containing the outut of `phpinfo()`, e.g.

    ```php
        /**
         * @Route("/info")
         */
        public function infoAction()
        {
            return new Response( phpinfo() );
        }
    ```

## More information

For more information follow the steps at:

- [xDebug Windows wizard](https://xdebug.org/wizard.php)

- [xDebug project home page](https://xdebug.org/)
