
# Web testing

## Testing controllers with `WebTestCase`

Symfony provides a package for simulating web clients so we can (functionally) test the contents of HTTP Responses output by our controllers.

## Creating a new project for web testing (project `test04`)

Do the following to setup a new project for web testing:

```bash
    // create and 'cd' into project
    $ symfony new --webapp test4_webTesting
    $ cd test4_webTesting

    // add testing package
    $ composer req --dev symfony/test-pack
```


## Create home page with a Default controller

Make a new `DefaultController` class:

```bash
    symfony console make:controller Default
```

Let's edit the generated template to include the message `Hello World`. Edit `/templates/default/index.html.twig`:

```twig
    {% extends 'base.html.twig' %}

    {% block body %}
    <h1>Hello World</h1>

    Hello World from the default controller
    {% endblock %}
```

Let's also set the URL to simply `/`, and the route name to `defalt` for this route in `/src/Controller/DefaultController.php`:

```php
    class DefaultController extends AbstractController
    {
        #[Route('/', name: 'default')]
        public function index(): Response
        {
            $template = 'default/index.html.twig';
            $args = [];
            return $this->render($template, $args);
        }
    }
```

If we run a web server and visit the home page we should see our 'hello world' message in a browser - see Figure \ref{homepage}.

![Home page. \label{homepage}](./03_figures/part_testing/2_homepage.png)


## Using the 'make' tool to create a web test

The Symfony console 'make' tool can be used to create web test classes:

```bash
    $ symfony console make:test

    // choose 'WebTestCase' when asked:

     Which test type would you like?:
      [TestCase       ] basic PHPUnit tests
      [KernelTestCase ] basic tests that have access to Symfony services
      [WebTestCase    ] to run browser-like scenarios, but that don't execute JavaScript code
      [ApiTestCase    ] to run API-oriented scenarios
      [PantherTestCase] to run e2e scenarios, using a real-browser or HTTP client and a real web server
     > WebTestCase

    // name the new class 'HomePageTest' when asked:

    Choose a class name for your test, like:
     * UtilTest (to create tests/UtilTest.php)
     * Service\UtilTest (to create tests/Service/UtilTest.php)
     * \App\Tests\Service\UtilTest (to create tests/Service/UtilTest.php)

     The name of the test class (e.g. BlogPostTest):
     > HomePageTest

     created: tests/HomePageTest.php

      Success!

     Next: Open your new test class and start customizing it.
     Find the documentation at https://symfony.com/doc/current/testing.html#functional-tests


```

## Automating a test for the home page contents

Let's write a test class for our `DefaultController` class. So we create a new test class `/tests/Controller/DefaultControllerTest.php`. We'll write 2 tests, one to check that we get a 200 OK HTTP success code when we try to request `/`, and secondly that the content received in the HTTP Response contains the text `Hello World`:

```php
    namespace App\Tests\Controller;

    use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
    use Symfony\Component\HttpFoundation\Response;

    class DefaultControllerTest extends WebTestCase
    {
        // methods go here
    }
```

We see our class must extend `WebTestCase` from package `Symfony\Bundle\FrameworkBundle\Test\`, and also makes use of the Symfony Foundation `Response` class.

Our method to test for a 200 OK Response code is as follows:

```php

    public function testHomepageResponseCodeOkay()
    {
        // Arrange
        $url = '/';
        $httpMethod = 'GET';
        $client = static::createClient();

        // Assert
        $client->request($httpMethod, $url);
        $statusCode = $client->getResponse()->getStatusCode();

        // Assert
        $this->assertSame(Response::HTTP_OK, $statusCode);
    }
```

NOTE: Another way to check the Response status code is to use the `$this->assertResponseStatusCodeSame(<code>)` method. For example:

```php
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);
```

We see how a web client object `$client` is created and makes a GET request to `/`. We see how we can interrogate the contents of the HTTP Response received using the `getResponse()` method, and within that we can extract the status code, and compare with the class constant `HTTP_OK` (200).

Here is our method to test for a Level 1 heading containing **exactly** `Hello World` (case sensitive):

```php
    public function testHomepageContentContainsHelloWorld(): void
    {
        // Arrange
        $url = '/';
        $httpMethod = 'GET';
        $client = static::createClient();
        $searchText = 'Hello World';
        $cssSelector = 'h1';

        // Act
        $crawler = $client->request($httpMethod, $url);
        $content = $client->getResponse()->getContent();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains($cssSelector, $searchText);
    }
```

We see how we can use the `assertSelectorTextContains` string method to search for the string `Hello World` in the content of the HTTP Response.

When we run PHPUnit we can see success both from the full-stops at the CLI, and in our log files. For example we case see the human-friendly `teestdox` report in `/build/testdox.txt` as follows:

```txt
    Default Controller (App\Tests\Controller\DefaultController)
     [x] Homepage response code okay
     [x] Homepage content contains hello world
```

## Testing text case-insensitively

I lost 30 minutes thinking my web app wasn't working! This was due to the difference between `Hello world` and `Hello World`: [w]orld vs [W]orld.

Luckily there is a specific assertion for testing text that ignores case:

- `assertStringContainsStringIgnoringCase(<needle>, <haystack>)`


Here's a full test method for case insensitively:

```php
    public function testHomepageContentContainsHelloWorldIgnoreCase()
    {
        // Arrange
        $url = '/';
        $httpMethod = 'GET';
        $client = static::createClient();
        $searchText = 'heLLo worLD';
        $cssSelector = 'body';

        // Act
        $crawler = $client->request($httpMethod, $url);
        $content = $client->getResponse()->getContent();

        // Assert
        $this->assertStringContainsStringIgnoringCase($searchText, $content);
    }
```

## Test multiple pages with a data provider

Avoid duplicating code when only the URL and search text changes, by writing a testing method fed by arrays of test input / expected values from a data provider method.

Here is a method with a provider, testing for `hello world` in the home page (route `/`), and `about` for the `/about` route:

```php
    /**
     * @dataProvider basicPagesTextProvider
     */
    public function testPublicPagesContainBasicText(string $url, string $searchText)
    {
        // Arrange
        $httpMethod = 'GET';
        $client = static::createClient();

        // Act
        $crawler = $client->request($httpMethod, $url);
        $content = $client->getResponse()->getContent();

        // Assert
        $this->assertStringContainsStringIgnoringCase($searchText, $content);
    }

    public function basicPagesTextProvider(): array
    {
        return [
            ['/', 'hello WORLD'],
            ['/about', 'about'],
        ];
    }
```

## Count the number of elements

We can use the `filter(...)` method of the crawler object to retrieve an array of elements matching a CSS selector.

For example, this statement asserts that there should be exactly 4 elements with CSS class `comment`:

```php
    $this->assertCount(4, $crawler->filter('.comment'));
```


## Testing different content returned by the Response

Our test classes are subclasses of the Symfony `WebTestCase` class, which provides a number of methods for testing the contents of the HTTP Response.

So there are several useful assertions we can make based on CSS element selectors, including:

-  `$this->assertSelectorExists($cssSelector)`

    - this assets that a given element is present in the Response content (such as `#formHeading`, `h1`, `title`, `body` etc.)

-  `$this->assertSelectorNOTExists($cssSelector)`

    - as above but that the selector is NOT present in the Response

-  `$this->assertSelectorTextContains($cssSelector, $searchText)`

    - this assets that a given element (such as `h1`, `title`, `body` etc.) contains some given text

-  `$this->assertSelectorTextNotContains($cssSelector, $searchText)`

    - as above but that the search text is NOT present in selected element of the Response

-  `$this->assertSelectorTextSame($cssSelector, $searchText)`

    - this assets that a given element (such as `h1`, `title`, `body` etc.) contains some given text


Here we see a test with many of these assertions demonstrated:

```php
    public function testHomepageBodyContentContainsHelloAndNotDinasaur(): void
    {
        // Arrange
        $url = '/';
        $httpMethod = 'GET';
        $client = static::createClient();
        $searchText = 'Hello World';
        $textNotInPage = 'Dinosaur';
        $cssSelector = 'body';

        // Act
        $crawler = $client->request($httpMethod, $url);
        $content = $client->getResponse()->getContent();

        // Assert
        $this->assertResponseIsSuccessful();

        // has a 'body' element
        $this->assertSelectorExists($cssSelector);

        // does NOT have a 'footer' element
        $this->assertSelectorNotExists('footer');

        // 'body' contains 'Hello'
        $this->assertSelectorTextContains($cssSelector, $searchText);

        // 'body' dopes NOT contains 'Dinosaur'
        $this->assertSelectorTextNotContains($cssSelector, $textNotInPage);

        // 'h1' exact text of 'Hello World'
        $this->assertSelectorTextSame('h1', $searchText);
    }
```

## Testing route followed after link clicked or form submission

Another useful assertion is `$this->assertRouteSame(<routeName>)` which asserts that the Response now received is from a link or redirect followed, corresponding to the given route name.

For example, the following test checks that after clicking the `/about` link, the Response is from the `/about` route (more about links in the next section):

```php
    public function testHomepageResponseProperties(): void
    {
        // Arrange
        $url = '/';
        $httpMethod = 'GET';
        $client = static::createClient();
        $client->followRedirects();
        $homeRoute = 'home';
        $aboutRoute = 'about';

        // Act
        $crawler = $client->request($httpMethod, $url);

        // click ABOUT link
        $linkText = 'about';
        $link = $crawler->selectLink($linkText)->link();
        $client->click($link);

        // now on page for about route
        $this->assertRouteSame($aboutRoute);
    }
```

