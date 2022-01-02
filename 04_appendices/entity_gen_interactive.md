

# Transcript of interactive entity generation \label{appendix_entity_gen}

The following is a transcript of an interactive session in the terminal CLI to create an `Item` entity class (and related `ItemRepository` class) with thse properties:

- title (string)
- price (float)

You start this interactive entity generation dialogue with the following console command:
```
    php bin/console doctrine:generate:entity
```

Here is the full transcript (note all entites are automatically given an 'id' property):

```
    $ php bin/console doctrine:generate:entity

      Welcome to the Doctrine2 entity generator

    This command helps you generate Doctrine2 entities.

    First, you need to give the entity name you want to generate.
    You must use the shortcut notation like AcmeBlogBundle:Post.

    The Entity shortcut name: AppBundle:Product/Item

    Determine the format to use for the mapping information.

    Configuration format (yml, xml, php, or annotation) [annotation]:

    Instead of starting with a blank entity, you can add some fields now.
    Note that the primary key will be added automatically (named id).

    Available types: array, simple_array, json_array, object,
    boolean, integer, smallint, bigint, string, text, datetime, datetimetz,
    date, time, decimal, float, binary, blob, guid.

    New field name (press <return> to stop adding fields): description
    Field type [string]:
    Field length [255]:
    Is nullable [false]:
    Unique [false]:

    New field name (press <return> to stop adding fields): price
    Field type [string]: float
    Is nullable [false]:
    Unique [false]:

    New field name (press <return> to stop adding fields):

      Entity generation

      created ./src/AppBundle/Entity/Product/
      created ./src/AppBundle/Entity/Product/Item.php
    > Generating entity class src/AppBundle/Entity/Product/Item.php: OK!
    > Generating repository class src/AppBundle/Repository/Product/ItemRepository.php: OK!

      Everything is OK! Now get to work :).

    $
```