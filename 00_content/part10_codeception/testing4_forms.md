
# Filling out forms

## Setup database (project `codeception04`)

Let's generate a `Recipe` Entity class, and its CRUD - with no security.

Generate a `Recipe` Entity class:

- title (string)

- steps (string)

- time (integer)

Also generate its CRUD, and migrate it to the database.

There should now be a new recipe form at URL `/recipe/new`.

## View the Twig template for a new Recipe 

Open a web browser and enter URL `/recipe/new`. Then view the page source and look at the HTML form:

```html
<form name="recipe" method="post">
    <div id="recipe"><div>
    <label for="recipe_title" class="required">Title</label>
    <input type="text" id="recipe_title" name="recipe[title]" required="required" maxlength="255" />
    </div>
    <div>
    <label for="recipe_steps" class="required">Steps</label>
    <input type="number" id="recipe_steps" name="recipe[steps]" required="required" />
    </div>
    
    <div><label for="recipe_time" class="required">Time</label>
    <input type="number" id="recipe_time" name="recipe[time]" required="required" />
    </div>
    
    <input type="hidden" id="recipe__token" name="recipe[_token]" value="Su7AJhQM_GiNSjtmEaioeT3X88SUqfWEI9phdE2Ow9Y" />
    </div>
    <button class="btn">Save</button>
</form>
```

The important values here are the **IDs** of the `<input>` elements:

- `#recipe_title`
- `#recipe_steps`
- `#recipe_time`

It's these form element IDs we need to use to create an automated test to enter data into a form.

Also, we need to know the text for the submit button, which in this example is:

- `Save`

## Cest to enter a new recipe 

Generate a new `Cest` to fill in the form for a new recipe:

```bash
    $ vendor/bin/codecept g:cest acceptance RecipeCest
```

Edit the skeleton as follows:

```php
    namespace App\Tests;    
    
    use App\Tests\AcceptanceTester;
    
    class RecipeCest
    {
        public function tryToTest(AcceptanceTester $I)
        {
            $I->amOnPage('/recipe/new');
            $I->fillField('#recipe_title', 'Boston Cheesecake');
            $I->fillField('#recipe_steps', 'buy packet - follow instructions');
            $I->fillField('#recipe_time', 60);
            $I->click('Save');
        }
    }
```

As you can see we select an HTML form input by its **id**, so `#recipe_title` is the form field for the `title` property of the new Recipe to be created etc.

When you run the server and run the acceptance tests you'll now see a new recipe in the database, based on your acceptance tests completion of the forms.

