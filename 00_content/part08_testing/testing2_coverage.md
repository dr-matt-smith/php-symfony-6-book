

# Code coverage and xDebug

## Code Coverage

It's good to know how **much** of our code we have tested, e.g. how many methods or logic paths (e.g. if-else- branches) we have and have not tested.

Code coverage reports can be text, XML or nice-looking HTML. See Figure \ref{default_page} for a screenshot of an HTML coverage report for a `Util` class with 4 methods. We can see that while `add` and `divide` have been fully (100%) covered by tests, methods `subtract` and `process` are insufficiently covered.

![Screenshot of HTML coverage report. \label{coverage_html}](./03_figures/part_testing/3_coverage_html.png)

This is known as code coverage, and easily achieved by:

1. Adding a line to the PHPUnit configuration file (`php.ini`)

1. Ensuring the **xDebug** PHP debugger is installed and activated

See Appendix \ref{appendix_xdebug} for these stesp.

## Generating Code Coverage HTML report

Add the following element as a child to the `<logging>` element in file `phpuninit.xml.dist`:

```xml
        <log type="coverage-html" target="./build/report"/>
```

So the full content of the `<logging>` element is now:

```xml
    <logging>
        <log type="coverage-html" target="./build/report"/>
        <log type="junit" target="./build/logfile.xml"/>
        <log type="testdox-html" target="./build/testdox.html"/>
        <log type="testdox-text" target="./build/testdox.txt"/>
        <log type="tap" target="./build/logfile.tap"/>
    </logging>
```

Now when you run `vendor/bin/simple-phpunit` you'll see a new directory `report` inside `/build`. Open the `index.html` file in `/build/report` and you'll see the main page of your coverage report. See Figure \ref{build_files}.

![Build files showing `index.html` in `/build/report`. \label{build_files}](./03_figures/part_testing/4_build_output.png){ width=75% }

## Tailoring the 'whitelist'

PHPUnit decides which soruces file to analyse and build coverage reports for by using a 'whitelist' - i.e. a list of just those files and/or directories that we are interested in at this point in time. The whitelist is inside the `<filter>` element in PHPUnity configuration file  'phpunit.xml.dist'.

the default whitelist is `./src` - i.e **all** files in our source directory. But, for example, this will include Kernel, which we generally don't touch. So if you want to go **GREEN** for everything in your coverage report, then you can list only those directories inside `/src` that you are interested in.

For our example above we were working with classes in `/src/Util` and `src/Controller`, so that's what we can list in our 'whitelist'. You can always 'disable' lines in XML by wrapping an XML command around them `<-- ... -->`, which we've done below to the default `./src/` white list element:

```xml
    <filter>
        <whitelist>
            <!--
                // ignore this element for now ...
                <directory>./src/</directory>
            -->
            <directory>./src/Controller</directory>
            <directory>./src/Util</directory>
        </whitelist>
    </filter>
```

