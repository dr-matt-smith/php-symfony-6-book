

# About `parameters.yml` and `config.yml` \label{appendix_parameters}

## Project (and deployment) specific settings in (`/app/config/parameters.yml`)

Usually the project-specific settings are declared in this file:

```
    /app/config/parameters.yml
```

These parameters are referred to in the more generic `/app/config/config.yml`.


For example the host of a MySQL database for the project woudl be defined by the following variable in `parameters.yml`:

```
    parameters:
        database_host: 127.0.0.1
```


Note that this file (`parameters.yml`) is include in the `.gitignore`, so it is **not** archived in your Git folder. Usually we need different parameter settings for different deployments, so while on your local, development machine you'll have certain settings, you'll need different settings for your public production 'live' website. Plus you don't want to accidently publically expose your database credentials on a open source Github page :-)

If there isn't already a `parameters.yml` file, then you can copy the `parameters.yml.dist` file end edit it as appropriate.

## More general project configuration (`/app/config/config.yml`)

The file `/app/config/config.yml` is actually the one used by Symfony when it looks up project settings. So the `config.yml` file uses references to the variables declared in the `/app/config/parameters.yml` file. For example the following lines in `config.yml` make a reference to the variable `database_path` that is declared in `parameters.yml`:

```yaml
    doctrine:
        dbal:
            driver:   pdo_mysql
            host:     "%database_host%"
```

For many projects we need to make **no changes** to the contents of `config.yml`. Although, since Symfony is setup with defaults for a MySQL database, if we are using SQLIte, for example then we do need to change the configuration settings, as well as declaring appropriate variables in `parameters.yml`. This is discussed in Appendix \label{appendix_db_sqlite}, describing how to set up a Symfony project to work with SQLite.

