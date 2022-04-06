


## Testing links (project `test05`)

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

    // set $expectedText to what should in page when link has been followed ...
    // Assert
    $content = $client->getResponse()->getContent();
    $this->assertStringContainsString($expectedText, $content);

    $this->assertSelectorTextContains($cssSelector, $expectedText);
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
    #[Route('/about', name: 'about')]
    public function about(): Response
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
        $client->followRedirects();
        $searchText = 'About';
        $linkText = 'about';
        $cssSelector = 'body';

        // Act
        $crawler = $client->request($httpMethod, $url);
        $link = $crawler->selectLink($linkText)->link();
        $client->click($link);
        $content = $client->getResponse()->getContent();

        // Assert
        $this->assertStringContainsString($searchText, $content);

        $this->assertSelectorTextContains($cssSelector, $searchText);
    }
```


### Instruct client to 'follow redirects'

In most cases we want our testing web-crawler client to follow redirects. So we need to add the `$client->followRedirects(true)` statement immediately after creating the client object.

```php
    public function testExchangePage()
    {
        $httpMethod = 'GET';
        $url = '/calc';
        $client = static::createClient();
        $client->followRedirects(); // <<<<<<<<<<<<<<<< default is 'true'
        $client->request($httpMethod, $url);
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
```


