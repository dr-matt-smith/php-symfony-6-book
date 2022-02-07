
# The Symfony command line tool\label{symfony_clis}

## Preparation

ensure you have PHP and Composer working correcly before attemtping to install the Symfony CLI application.

## Symfony command line tool

Download / install the Symfony command line tool from:

- [https://symfony.com/download](https://symfony.com/download)

### Windows - Scoop install - the preferred method

If at all possible, install the Scoop installer on your computer from:

- [https://scoop.sh/](https://scoop.sh/)

Then follow the instructions from the Windows tab.



Figure \ref{windows_scoop} shows a screenshot of where to install the Symfony CLI via Scoop.

![PHP.net / Scoop method to install Symfony CLI tool. \label{windows_scoop}](./03_figures/app04_symfony/scoop_install.png)

### Windows installation of Symfony `exe`

Symfony provides a Windows executable (don't worry about the `amd` prefix, it works for Intel CPUs too!).

Do the following:

1. visit the Symfony download page: [https://symfony.com/download](https://symfony.com/download)

2. click the **Windows** tab

3. Choose to download the Symfony ZIP file labelled `amd64` in the **Binaries** section

4. Unzip the folder to a convenient location
    
   - it could be in Program Files, but personally I put it at `c:\symfony`

5. Add the location of the unzipped folder to the **path** System environment variable



Figure \ref{windows_symfony} shows a screenshot of where to download the ZIP folder containing the CLI executable.

![PHP.net / Windows exectuable ZIP folder download. \label{windows_symfony}](./03_figures/app04_symfony/symfony_install.png)





### Checking everything ready for running Symfony web applications

When you've installed the Symfony command-line tool, you can check all versions of required software are fine by running:

```bash
  $ symfony check:requirements
```

If you get a GREEN message then it's all good. Otherwise fix whatever it say to fix ...