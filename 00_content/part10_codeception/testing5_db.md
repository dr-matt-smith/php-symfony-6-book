

# Codeception Symfony DB testing

## Adding Symfony and Doctrine to the settings (project `codeception05`)

Let's configure Codeception to use Doctrine for DB work, and to recognise Symfony controllers.

Note - by stating `cleanup:true` for the Doctrine2 module, we are asking Codeception to create a database **transaction** before each test runs, and then to roll-back the translation after the test runs. So the database should be in the same state **after** each test as it was **before**. Thus, our tests are not linked and do not rely on running in a set sequence.

We need to edit file `/tests/acceptance.yml.suite` to add the following:

```yaml
    - Symfony:
        app_path: 'src'
        environment: 'test'
    - Doctrine2:
        depends: Symfony
        cleanup: true
    - Asserts
```

So it should now contain:

```yaml
    actor: AcceptanceTester
    modules:
        enabled:
            - PhpBrowser:
                url: http://localhost:8000
            - \App\Tests\Helper\Acceptance
            - Symfony:
                app_path: 'src'
                environment: 'test'
            - Doctrine2:
                depends: Symfony
                cleanup: true
            - Asserts
```

## Test Users in DB from Fixtures

To work with databases we need to add the Doctrine Codeception module:

```bash
    composer req codeception/module-doctrine2 --dev 
```

Generate a new Acceptance test `UserCest`:

```bash
codecept g:cest acceptance UserCest
```

Edit this new file `/tests/acceptance/UserCest.php` to contain the following:

```php
    <?php namespace App\Tests;
    use App\Tests\AcceptanceTester;
    
    class UserCest
    {
        public function testUsersInDb(AcceptanceTester $I)
        {
            $I->seeInRepository('App\Entity\User', [
                'email' => 'user@user.com'
            ]);
            $I->seeInRepository('App\Entity\User', [
                'email' => 'admin@admin.com'
            ]);
            $I->seeInRepository('App\Entity\User', [
                'email' => 'matt.smith@smith.com'
            ]);
        }
    }
```

NOTE: If we had created a Security `User` entity using `username` for login rather than `email` we'd write something like this to test for them in the DB:

```php
    public function testUsersInDb(AcceptanceTester $I)
    {
        $I->seeInRepository('App\Entity\User', [
            'username' => 'user'
        ]);
        $I->seeInRepository('App\Entity\User', [
            'username' => 'admin'
        ]);
        $I->seeInRepository('App\Entity\User', [
            'username' => 'matt'
        ]);
```

When you run the test should pass (assuming these users are in the DB from the Fixtures setup).

And, of course, we could tidy this up using data providers as in a previous chapter ...

## Check DB reset after each test

The `AcceptanceTester` method `haveInRepository(...)` INSERTS an object into the database for testing. This is to be distinguished from the very similarly named `seeInRepository(...)` which tests whether an object already EXISTS in the database.

Add 2 more test methods, one to create a new user, and test it exists, and then one to check that that new user was removed when its test finished running:

```php
    
    public function testAddToDatabase(AcceptanceTester $I)
    {
        // INSERT new user `userTemp@temp.com` into the User table 
        $I->haveInRepository('App\Entity\User', [
            'email' => 'userTemp@temp.com',
            'password' => 'simplepassword',
            'roles' => ['ROLE_USER']
        ]);

        // test whether user `userTemp@temp.com`  can be FOUND in the table
        $I->seeInRepository('App\Entity\User', [
            'email' => 'userTemp@temp.com',
        ]);
    }

    public function testTEMPNoLongerInDatabase(AcceptanceTester $I)
    {
        // since we are RESETTING db after each test, the temporary user should NOT still be in the repository...
        $I->dontSeeInRepository('App\Entity\User', [
            'email' => 'userTemp@temp.com',
        ]);
    }
```

## Counting items retrieved from DB (project `codeception06`)

Let's add a new method to our `/tests/acceptance/RecipeCest.php` Recipe test class, one that counts the Recipe objects retrieved from the database, which should be ZERO, since at present we have no Fixtures for Recipes:

```php
    public function countRecipesBeforeChanges(AcceptanceTester $I)
    {
        // --- expect 0 in DB from fixtures
        // arrange
        $expectedCount = 0;
    
        // act
        $recipes = $I->grabEntitiesFromRepository('App\Entity\Recipe');
        $numRecipes = count($recipes);
    
        // assert
        $I->assertEquals($expectedCount, $numRecipes);
    }
```

This will succeed when we run Codeception. The key to DB counting is the AcceptanceTester method `grabEntitiesFromRepository(<EntityClass>)`. This method returns an array of Entity objects, just as we'd get from invoking `findAll()` with a reference to a Repository object in a normal controller method.

## Create a Recipe fixture

Now let's ensure there is one recipe in the data through a fixture:

```php
    namespace App\DataFixtures;
    
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Common\Persistence\ObjectManager;
    
    use App\Entity\Recipe;
    
    class RecipeFixtures extends Fixture
    {
        public function load(ObjectManager $manager)
        {
             $recipe = new Recipe();
             $recipe->setTitle('Boston Cheesecake');
             $recipe->setSteps('open package - follow instructions');
             $recipe->setTime(120);
    
             $manager->persist($recipe);
    
            $manager->flush();
        }
    }
```

And load the fixtures via the Symfony console Doctrine command.

NOTE:

- if your Fixture class does not load, clear the cache (e.g. delete folder `/var/cache` and try again ...)

We would now have to refactor our DB counting test to expect 1 Recipe.

## Comparing Repository counts BEFORE and AFTER DB actions

Now we can add to our Recipe form-filling acceptance test to count 1 recipe initially, then 2 after the new one has been created by filling in the form. However, rather than hard code these numbers, we can retrieve the count **before** the insert actions, and the count **after** the action, and **assert** that the count after is expected to be 1 + the count before:

```php
    public function countOneMoreAfterCreate(AcceptanceTester $I)
    {
        $recipes = $I->grabEntitiesFromRepository('App\Entity\Recipe');
        $countbeforeInsert = count($recipes);

        $I->amOnPage('/recipe/new');
        $I->fillField('#recipe_title', 'Boston Cheesecake');
        $I->fillField('#recipe_steps', 'buy packet - follow instructions');
        $I->fillField('#recipe_time', 60);

        $I->click('Save');

        $recipes = $I->grabEntitiesFromRepository('App\Entity\Recipe');
        $countAfterInsert = count($recipes);

        $I->assertEquals($countAfterInsert, (1 + $countbeforeInsert));
    }
```

## Testing properties of items added to the database 

In additional to counting DB rows, we can also use the `seeInRepository(...)` method to confirm that an object exists in the DB matching the values entered in the form. Rather than hardcoding such values (since we should not guess what values any Fixtures have), we can create a more unique value to test by concatenating random numbers to values.

So below we create a random number from 1..00 (`$randomNumber = rand(1,100)`), and append this to the end of the String `Boston Cheesecake`:

```php
    // --- create new recipe via FORM
    // title suffix RANDOM numbner: 'Boston Cheesecake<randNum>'
    $randomNumber = rand(1,100);
    $recipeTitle = 'Boston Cheesecake' . $randomNumber;
```

We can then test for an item in the DB by writing the assertion:

```php
    // ---- check added to repository
    $I->seeInRepository('App\Entity\Recipe', [
        'title' => $recipeTitle
    ]);
```

```php
    public function testRandomInsertedStringAddedToDb(AcceptanceTester $I)
    {
        // (1) Arrange
        // --- create new recipe via FORM
        // title suffix RANDOM numbner: 'Boston Cheesecake<randNum>'
        $randomNumber = rand(1,100);
        $recipeTitleRandom = 'Boston Cheesecake' . $randomNumber;

        // (2) Act
        $I->amOnPage('/recipe/new');
        $I->fillField('#recipe_title', $recipeTitleRandom);
        $I->fillField('#recipe_steps', 'buy packet - follow instructions');
        $I->fillField('#recipe_time', 60);
        $I->click('Save');

        // (3) Assert
        $I->seeInRepository('App\Entity\Recipe', [
            'title' => $recipeTitleRandom
        ]);
    }
```
