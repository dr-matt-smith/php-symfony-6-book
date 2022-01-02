https://barryvanveen.nl/blog/36-environment-configuration-in-codeception-with-dotenv

Add this to Book - for getting it to read DB params ././


Add to codeception.yml:

modules:
    config:
        Db:
            dsn: "mysql:host=%DB_HOST%:%DB_PORT%;dbname=%DB_DATABASE%"
            user: "%DB_USERNAME%"
            password: "%DB_PASSWORD%"
params:
    - .env