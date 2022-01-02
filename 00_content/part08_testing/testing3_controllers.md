
# Web testing

## Testing controllers with `WebTestCase` (project `test05`)

Symfony provides a package for simulating web clients so we can (functionally) test the contents of HTTP Responses output by our controllers.

First we need to add 2 packages to the project development environment:

```bash
    composer req --dev browser-kit css-selector
```

Note - these next steps assume your project has Twig, annotations and the Symfony maker packages available, so you may need to add these to your project as well:

```bash
    composer req twig annotations make
```

Let's make a new `DefaultController` class:

```bash
    php bin/console make:controller Default
```

Let's edit the generated template to include the message `Hello World`. Edit `/templates/default/index.html.twig`:

```twig
    {% extends 'base.html.twig' %}

    {% block body %}
    <h1>Welcome</h1>

    Hello World from the default controller
    {% endblock %}
```

Let's also set the URL to simply `/` for this route in `/src/Controller/DefaultController.php`:

```php
    class DefaultController extends Controller
    {
        /**
         * @Route("/", name="default")
         */
        public function index()
        {
            return $this->render('default/index.html.twig', [
                'controller_name' => 'DefaultController',
            ]);
        }
    }

```

If we run a web server and visit the home page we should see our 'hello world' message in a browser - see Figure \ref{homepage}.

![Contents of directory `/build`. \label{homepage}](./03_figures/part_testing/2_homepage.png)

## Automating a test for the home page contents

Let's write a test class for our `DefaultController` class. So we create a new test class `/tests/Controller/DefaultControllerTest.php`. We'll write 2 tests, one to check that we get a 200 OK HTTP success code when we try to request `/`, and secondly that the content received in the HTTP Reponse contains the text `Hello World`:

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

Our method to test for a 200 OK Reponse code is as follows:

```php
    public function testHomepageResponseCodeOkay()
    {
        // Arrange
        $url = '/';
        $httpMethod = 'GET';
        $client = static::createClient();

        // Assert
        $client->request($httpMethod, $url);

        // Assert
        $this->assertSame(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );
    }
```

We see how a web client object `$client` is created and makes a GET requerst to `/`. We see how we can interrogate the contents of the HTTP Response recevied using the `getResponse()` method, and within that we can extract the status code, and compare with the class constant `HTTP_OK` (200).

Here is our method to test for a 200 OK Reponse code is as follows:

```php
    public function testHomepageContentContainsHelloWorld()
    {
        // Arrange
        $url = '/';
        $httpMethod = 'GET';
        $client = static::createClient();
        $searchText = 'Hello World';

        // Act
        $client->request($httpMethod, $url);

        // Assert
        $this->assertContains(
            $searchText,
            $client->getResponse()->getContent()
        );
    }
```

We see how we can use the `assertContains` string method to search for the string `Hello World` in the content of the HTTP Response.

When we run Simple-PHPUnit we can see success both from the full-stops at the CLI, and in our log files, e.g.:

```txt
    App\Tests\Controller\DefaultController
     [x] Homepage response code okay
     [x] Homepage content contains hello world

    ...
```

## Normalise content to lowercase (project `test06`)

I lost 30 minutes thinking my web app wasn't working! This was due to the difference between `Hello world` and `Hello World` (`w` vs `W`).

This kind of problem can be avoided if we **normalise** the content from the Response, e.g. making all letters **lower-case**. This only makes sense if you are happy (at this stage) to not worry about the case of text content in your pages (you could always write some specific spelling / grammar checker tests for that ...)

The solution is to use the built-in PHP function `strtolower()`:

```php
    public function testHomepageContentContainsHelloWorld()
    {
        // Arrange
        $url = '/';
        $httpMethod = 'GET';
        $client = static::createClient();
        $searchText = 'Hello World';

        // Act
        $client->request($httpMethod, $url);
        $content = $client->getResponse()->getContent();

        // to lower case
        $searchTextLowerCase = strtolower($searchText);
        $contentLowerCase = strtolower($content);

        // Assert
        $this->assertContains(
            $searchTextLowerCase,
            $contentLowerCase
        );
    }
```

## Test multiple pages with a data provider

Avoid duplicating code when only the values change, by writing a testing method fed by arrays of test input / expected values from a data provider method:

```php

    /**
     * @dataProvider basicPagesTextProvider
     */
    public function testPublicPagesContainBasicText($url, $exepctedLowercaseText)
    {
        // Arrange
        $httpMethod = 'GET';
        $client = static::createClient();

        // Act
        $client->request($httpMethod, $url);
        $content = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();

        // to lower case
        $contentLowerCase = strtolower($content);

        // Assert - status code 200
        $this->assertSame(Response::HTTP_OK, $statusCode);

        // Assert - expected content
        $this->assertContains(
            $exepctedLowercaseText,
            $contentLowerCase
        );
    }

    public function basicPagesTextProvider()
    {
        return [
            ['/', 'home page'],
            ['/about', 'about'],
        ];
    }
```


## Testing links (project `test08`)

We can test links with our web crawler as follows:

- get reference to crawler object when you make the initial request

    ```
    $httpMethod = 'GET';
    $url = '/about';
    $crawler = $client->request($httpMethod, $url);
    ```

- select a link with:

    ```
    $linkText = 'login';
    $link = $crawler->selectLink($linkText)->link();
    ```

- click the link with:

    ```
    $client->click($link);
    ```

- then check the content of the new request

    ```
    $content = $client->getResponse()->getContent();

    // set $expectedText to what should in page when link has been followed ...
    $this->assertContains(
        $exepctedText,
        $content
    );
    ```

For example, if we create a new 'about' page Twig template `/templates/default/about.html.twig':

```twig
    {% extends 'base.html.twig' %}

    {% block body %}
    <h1>About page</h1>

        <p>
            About this great website!
        </p>

    {% endblock %}
```

and a `DefaultController` method to display this page when the route matches `/about`:

```php
    /**
     * @Route("/about", name="about")
     */
    public function aboutAction()
    {
        $template = 'default/about.html.twig';
        $args = [];
        return $this->render($template, $args);
    }
```

If we add to our base Twig template links to the homepage and the about, in template `/templates/base.html.twig`:

```twig
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>{% block title %}Welcome!{% endblock %}</title>
            {% block stylesheets %}{% endblock %}
        </head>
        <body>

        <nav>
            <ul>
                <li>
                    <a href="{{ url('homepage') }}">home</a>
                </li>
                <li>
                    <a href="{{ url('about') }}">about</a>
                </li>
            </ul>
        </nav>

            {% block body %}{% endblock %}
            {% block javascripts %}{% endblock %}
        </body>
    </html>
```

We can now write a test method to:

- request the homepage `/`

- select and click the `about` link

- test that the content of the new response is the 'about' page if it contains 'about page'

Here is our test method:

```php
    public function testHomePageLinkToAboutWorks()
    {
        // Arrange
        $url = '/';
        $httpMethod = 'GET';
        $client = static::createClient();
        $searchText = 'about page';
        $linkText = 'about';

        // Act
        $crawler = $client->request($httpMethod, $url);
        $link = $crawler->selectLink($linkText)->link();
        $client->click($link);
        $content = $client->getResponse()->getContent();

        // to lower case
        $searchTextLowerCase = strtolower($searchText);
        $contentLowerCase = strtolower($content);

        // Assert
        $this->assertContains($searchTextLowerCase, $contentLowerCase);
    }
```

## Issue with routes that end with a forward slash `/`

Often we write (or generate) a controller that adds URL and route name **prefixes** - by writing a route annotation commend immediately before the class declaration. For example, look at the first 2 routes of this simple calculator class:

```php
    /**
     * controller for calculator functions
     *
     * @Route("/calc", name="calc_")
     */
    class CalcController extends Controller
    {
        /**
         * home page for calc pages
         * @Route("/", name="home")
         */
        public function index()
        {
            $template = 'calc/index.html.twig';
            $args = [];
            return $this->render($template, $args);
        }

        /**
         * process the calc stuff
         *
         * @Route("/process", name="process")
         *
         * @param Request $request
         * @return \Symfony\Component\HttpFoundation\Response
         */
        public function processAction(Request $request)
        {
            ...
```

However, a consequence of this is that often the index route for this controller will be defined as having a trailing forward slash `/`. For example look at this route list:

```
    $ php bin/console debug:router
     ------------------ -------- -------- ------ --------------------------
      Name               Method   Scheme   Host   Path
     ------------------ -------- -------- ------ --------------------------
      calc_home          ANY      ANY      ANY    /calc/
      calc_process       ANY      ANY      ANY    /calc/process
```

As we can see the home calculator page URL route is '/calc/'. When running the site with a server and visiting this page with a web browser client, the server will usually simply redirect `/calc/` to `/calc` if the initial request doesn't match any routes. However, when **controller testing** such controllers if there is not a complete match between the URL being tested and the route pattern, the test will fail.

For example, a test in this form will **FAIL** for the route `/calc/` as defined above:

```php
    public function testExchangePage()
    {
        $httpMethod = 'GET';
        $url = '/calc';
        $client = static::createClient();
        $client->request($httpMethod, $url);
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
```

### Solution 1: Ensure url pattern in test method exactly matches router url pattern

One solution to this problem is to ensure the URL in our test method exactly matches the URL in the applications router. So for our calculator home page we need to ensure the URL passed to the client ends with this trailing forward slash `/` character":

```php
    public function testExchangePage()
    {
        $httpMethod = 'GET';
        $url = '/calc/'; // <<<<<<<<<<<<<<<<<<<<< /calc/
        $client = static::createClient();
        $client->request($httpMethod, $url);
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
```

### Solution 2: Instruct client to 'follow redirects'

An alternative solution is to instruct our tester's web crawler client to **follow redirects**, so that the request is re-processed if the request with no trailing forward slash `/` failed:

```php
    public function testExchangePage()
    {
        $httpMethod = 'GET';
        $url = '/calc';
        $client = static::createClient();
        $client->followRedirects(true); // <<<<<<<<<<<<<<<< follow redirects
        $client->request($httpMethod, $url);
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
```


