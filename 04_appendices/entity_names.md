

# Avoiding issues of SQL reserved words in entity and property names

Watch out for issues when your Entity name is the same as SQL keywords.

Examples to **avoid** for your Entity names include:

- user
- group
- integer
- number
- text
- date


If you have to use certain names for Entities or their properties then you need to 'escape' them for Doctrine.

- [Doctrine identifier escaping](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/basic-mapping.html#quoting-reserved-words)

You can 'validate' your entity-db mappings with the CLI validation command:

```bash
    $ php bin/console doctrine:schema:validate
```
