
# Publishing your Symfony website


## Requirements for Symfony publishing

You need the following to run Symfony on an internet host:

- an up to date version of PHP (7.2.5+ at present)

- a MySQL database (or another DB type supported by Symfony)

- a way to setup your project code (e.g. Composer)

- a way to setup your database (e.g. SSH terminal to run fixtures or MySQL dumps or client connection)

Traditional hosting companies, that don't offer SSH terminals, may require you to use FTP and online MySQL clients (such as PHPMyAdmin) to setup your project. Once setup, they'll run fine, but there can be a bunch of fiddly steps with online Control Panels and so on. 

Having setup several PHP and Symfony projects for companies and organisations with traditional hosting companies in the past, I can say from experience that unless you are doing it every day, it's a fiddly business, especially when you want to be spending your time adding and testing features to the website project rather than administering the site.

## Simplest ways to host Symfony projects

There are 2 easy ways to host Symfony websites, both supporting **CD (Continuous Deoployment)** whereby commits pushed to the **master** branch of a Github (or similar) cloud repository are pulled down and the app restarted automatically, triggered by web "hooks" - event messages to the hosting servers each time a new commit is pushed:

- Symfony Cloud, from Sensio Labs, the creators of Symfony. See Figure \ref{sfCloud}.

    ![Symfony Cloud - from the creators of Symfony.\label{sfCloud}](./03_figures/part11/1_cloud.png)

- PAAS - PHP-As-A-Service hosting companies

    - these companies specialise in PHP projects, and provide PHP environment variables, MySQL integrations, Github hooks and so on. Fore example see Figure \ref{paas} to see the site for [Fortrabbit.com](Fortrabbit.com).

    ![Fortrabbit.com - PHP-As-A-Service hosting.\label{paas}](./03_figures/part11/2_fortrabbit.png)


Since it's cheaper, and still very straightforward, we'll go through the steps for publishing with **Fortrabbit**.
