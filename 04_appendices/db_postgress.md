

# Setting up for Postgress Database \label{appendix_db_postgress}

## NOTE regarding FIXTURES

If you are using the Doctrine Fixtures Bundle, install that first **before** changing parameters and config for SQLite. The fixtures bundle assumes MySQL, and will overwrite some of the parameters during installation.

If that does happen, you'll just have to repeat the steps in this Appdendix to set things back to SQLite after fixtures installation.

## Postgress for Symfony

Details for `app/config/parameters.yml`

```
    # Postgresl
        psql_database_driver: pdo_pgsql
        psql_database_host: 127.0.0.1
        psql_database_port: 5432
        psql_database_name: your_database_name
        psql_database_user: your_name
        psql_database_password: your_password
```


Details for `app/config/config.yml`
```
    # Doctrine Configuration
    doctrine:
        dbal:
            default_connection: pgsql
            connections:
               #Mysql
               default:
                    driver:   pdo_mysql
                    host:     "%database_host%"
                    port:     "%database_port%"
                    dbname:   "%database_name%"
                    user:     "%database_user%"
                    password: "%database_password%"
                    charset:  UTF8
               #Postgresql
               pgsql:
                    driver:   pdo_pgsql
                    host:     "%psql_database_host"
                    port:     "%psql_database_port"
                    dbname:   "%psql_database_name%"
                    user:     "%psql_database_user"
                    password: "%psql_database_password%"
                    charset:  UTF8

            #mapping_types:
                #geometry: string

        orm:
            auto_generate_proxy_classes: "%kernel.debug%"
            naming_strategy: doctrine.orm.naming_strategy.underscore
            auto_mapping: true
```


source
https://stackoverflow.com/questions/36030961/use-a-postgres-database-with-symfony3

