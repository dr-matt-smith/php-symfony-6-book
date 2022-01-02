
# Acceptance Tests


## Test for `home page` text at `/` (project `codeception02`)

The project we started with (`security09`) has a simple public home page template:

```twig
    {% extends 'base.html.twig' %}
    
    {% block body %}
        welcome to the home page
    {% endblock %}
```

So let's generate an **acceptance** test to simulate a user visiting `/` and seeing text `home page`:

1. generate a new acceptance test, note these classes end with `Cest`, and you use the `g:cest` parameter, which automatically give us access to `AcceptanceTester $I`, meaning we can write PHP code that is close to English-pseudocode, e.g. `$I->see('home page')`

```bash
    $ vendor/bin/codecept g:cest acceptance HomePageCest

    Test was created in /cept3/tests/acceptance/HomePageCest.php
```

1. We are given a skeleton Acceptance testing class:

    ```php
       namespace App\Tests;

       use App\Tests\AcceptanceTester;
       
       class HomePageCest
       {
           public function _before(AcceptanceTester $I)
           {
           }
       
           // tests
           public function tryToTest(AcceptanceTester $I)
           {
           }
       }
    ```

1. Replace the contents of this class with one to test a home page visit:

    ```php
       class HomePageCest
       {
           public function homePageContent(AcceptanceTester $I)
           {
               $I->amOnPage('/');
               $I->see('welcome to the home page');
           }
       }
    ```
    
There are 2 steps to our home page acceptance test;

1. Make the browser open URL `/`

1. Assert that somewhere in the text contents of the HTTP Response from the server is the text `welcome to the home page`

## Run the test (fail - server not running)

Run the tests - we'll see a failure of our Acceptance test, since the server isn't running:

```bash
    $ vendor/bin/codecept run
    
    ...
    
    App\Tests.acceptance Tests (1) 
    
      Testing App\Tests.acceptance
    E HomePageCest: Home page content (0.02s)

    ... 
    
    There was 1 error:
    
    ---------
    1) HomePageCest: Home page content
     Test  tests/acceptance/HomePageCest.php:homePageContent
                             
      [GuzzleHttp\Exception\ConnectException] cURL error 7: Failed to connect to localhost port 8000: Connection refused (see http://curl.haxx.se/libcurl/c/libcurl-errors.html)  
```

The error details tell us that the connection was refuse to localhost port 8000.

## Run the test (pass, when server running)

Now run the Symfony server in a CLI terminal window with `symfony serve`, and in a second CLI window run the Codeception tests again. This time it should pass:

```bash
          Testing App\Tests.acceptance
    TICK HomePageCest: Home page content (0.36s)
```

## From red to green

Let's add an `about` page in this project:

- in our DefaultController class `/src/Controller/DefaultController.php`  we can define this route:

    ```php
          /**
           * @Route("/about", name="about")
           */
          public function about()
          {
              $template = 'default/about.html.twig';
              $args = [];
      
              return $this->render($template, $args);
          }
    ```
    
- and we can create Twig template `/templates/default/about.html.twig`:

    ```twig
      {% extends 'base.html.twig' %}
            
      {% block body %}
      <h1>about page</h1>
      <p>
      Welcome to the about page
      </p>
      {% endblock %}
    ```
    
However, at present there isn't any `HREF` link from the home page to the about page. Let's test this:

```php
    class HomePageCest
    {
        public function homePageContent(AcceptanceTester $I)
        {
            $I->amOnPage('/');
            $I->see('home page');
        }
    
        public function homePageLinkToAbout(AcceptanceTester $I)
        {
            $I->amOnPage('/');
            $I->click('about');
            $I->seeInCurrentUrl('/about');
            $I->see('about');
        }
    }
```

If we run Codeception again 

```bash
    App\Tests.acceptance Tests (2) 
          Testing App\Tests.acceptance
    TICK  HomePageCest: Home page content (0.20s)
    CROSS HomePageCest: Home page link to about (0.16s)
    
    ...
    
    1) HomePageCest: Home page link to about
     Test  tests/acceptance/HomePageCest.php:homePageLinkToAbout
     Step  Click "about"
     Fail  Link or Button by name or CSS or XPath element with 'about' was not found.
```

The details of the failure tells us that no link for text `about` could be found on the home page.

## Make green - add link to `about` page in base Twig template

Add a link to the about page in the base Twig template `templates/base.html.twig`, just below the existing navigation link to the home page:

```twig
    <nav>
        <ul>
            <li>
                <a href="{{ url('homepage') }}">home</a>
            </li>
            <li>
                <a href="{{ url('about') }}">about</a>
            </li>
            {% if is_granted('ROLE_ADMIN') %}
                <li>
                    <a href="{{ url('admin') }}">admin home</a>
                </li>
            {% endif %}
        </ul>
    </nav>
```

Clear the **cache** - e.g. just delete folder `/var/cache`, and run Codeception again.

Now all tests should pass when we run Codeception.

## Annotation style data provider to test multiple data (project `codeception03`)

Let's use the Codeception Doctrine-style Annotation data provider, to test:

1. routes to home page and about page

1. our base navigation links, from home page to home page and about page


Create a new `Cest` acceptance test

```bash
    $ vendor/bin/codecept g:cest acceptance NavbarCest
```

Replace the skeleton with the following:

```php
    namespace App\Tests;

    use App\Tests\AcceptanceTester;
    use Codeception\Example;
    
    class NavbarCest
    {
        /**
         * @example(url="/", heading="Home page")
         * @example(url="/about", heading="About page")
         */
        public function staticPageContent(AcceptanceTester $I, Example $example)
        {
            $I->amOnPage($example['url']);
            $I->see($example['heading'], 'h1');
        }
    
        /**
         * @example(url="/", link="home")
         * @example(url="/about", link="about")
         */
        public function staticPageLinks(AcceptanceTester $I, Example $example)
        {
            $I->amOnPage('/');
            $I->click($example['link']);
            $I->seeCurrentUrlEquals($example['url']); // full URL
            $I->seeInCurrentUrl($example['url']); // part of URL
        }    
    }
```

We see that we are using the `Codeception\Example` class, which allows us to write `@example` annotations (note lower case `e`):

```php
    * @example(url="/", heading="Home page")
    * @example(url="/about", heading="About page")
```

Method `staticPageContent()` makes use of our custom example values `url` and `heading` (we can name them what we want, and have as many as we wish). The example data is provided to the testing method as an associated map array. Each URL is visited, then an assertion is made that we see the provided level 1 heading text on the visited page `$I->see($example['hading'], 'h1')`.


Method `staticPageLinks()` makes use of our custom example values `url` and `link`. We start on the home page, attempt to click a link wrapped around the text of the provided link `$I->click($example['link'])`, and then check the the provided URL is in the browser address bar having clicked the link.

```php
     * @example(url="/", link="home")
     * @example(url="/about", link="about")
```

Notice, for this example, we test both that the provided `url` matches the complete URL `$I->seeCurrentUrlEquals($example['url'])`, and also (a weaker test) is part of the URL string `$I->seeInCurrentUrl($example['url'])`.

## Traditional Data Provider syntax

If you prefer the more traditional Data Provided syntax, you can lean about that at the Codeception documentation pages:

- [https://codeception.com/docs/07-AdvancedUsage#DataProvider-Annotations](https://codeception.com/docs/07-AdvancedUsage#DataProvider-Annotations)

## Common assertions for Acceptance tests

Here are the more common Codeception assertions for Acceptance tests:

- see / not see text in page (anywhere in HTML received by browser - could be in the `<head>` so not see by user)

    - `$I->see(<text in page>)`

    - `$I->dontsee(<text in page>)` - text is **NOT** present (e.g. not see 'invalid login')

- see / not see text in page in a specific CSS selector:

    - `$I->see(<text in page>, '<selector>)`

    - `$I->dontsee(<text in page>, '<selector>')` - text is **NOT** present (e.g. not see paragraph after valid login: `dontsee('invalid login', 'p')`


- see this text as a link

    - `$I->seeLink('login');`

- click a text link

    - `$I->click('login');`

- part of URL

    - $I->seeInCurrentUrl('/blog');
    
- match of complete URL

    - $I->seeCurrentUrlEquals('/about');
    
- see / don't see in HTML `<title>`:

    - `$I->seeInTitle('Login')`

    - `$I->dontSeeInTitle('Register')`
    

More details of what you can do with `$I` can be found in the docs for the `PhpBrowser` module:

- [https://codeception.com/docs/modules/PhpBrowser](https://codeception.com/docs/modules/PhpBrowser)

