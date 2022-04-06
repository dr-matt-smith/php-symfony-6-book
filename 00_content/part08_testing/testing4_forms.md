
# Testing web forms

## Testing forms (project `test06`)

Testing forms is similar to testing links, in that we need to get a reference to the form  (via its submit button), then insert out data, then submit the form, and examine the content of the new response received after the form submission.

Assume we have a Calculator class as follows in `/src/Util/Calculator.php`:

```php
    namespace App\Util;

    class Calculator
    {
        public function add($n1, $n2)
        {
            return $n1 + $n2;
        }

        public function subtract($n1, $n2)
        {
            return $n1 - $n2;
        }

        public function divide($n, $divisor)
        {
            if(empty($divisor)){
                throw new \InvalidArgumentException("Divisor must be a number");
            }

            return $n / $divisor;
        }

        public function process($n1, $n2, $process)
        {
            switch($process){
                case 'subtract':
                    return $this->subtract($n1, $n2);
                    break;
                case 'divide':
                    return $this->divide($n1, $n2);
                    break;
                case 'add':
                default:
                    return $this->add($n1, $n2);
            }
        }
    }
```

Assume we also have a `CalculatorController` class in `/src/Controller/`:

```php
    namespace App\Controller;

    use App\Util\Calculator;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;

    class CalcController extends Controller
    {
        ... methods go here ...
    }
```

There is a calculator home page that displays the form Twig template at `/templates/calc/index.html.twig`:


```php
    #[Route('/calculator', name: 'app_calculator_index')]
    public function index(): Response
    {
        $template = 'calculator/index.html.twig';
        $args = [];
        return $this->render($template, $args);
    }
```

and a 'process' controller method to received the form data (n1, n2, operator) and process it:
There is a calculator home page that displays the form Twig template at `/templates/calculator/index.html.twig`:


```php

    use Symfony\Component\HttpFoundation\Request;
    use App\Util\Calculator;

    ...

    #[Route('/calculator/process', name: 'app_calculator_process')]
    public function processAction(Request $request): Response
    {
        // extract name values from POST data
        $n1 = $request->request->get('num1');
        $n2 = $request->request->get('num2');
        $operator = $request->request->get('operator');

        $calc = new Calculator();
        $answer = $calc->process($n1, $n2, $operator);

        $template = 'calculator/result.html.twig';
        $args =  [
            'n1' => $n1,
            'n2' => $n2,
            'operator' => $operator,
            'answer' => $answer
        ];

        return $this->render($template, $args);
    }
```

The Twig template to display our form looks as follows `/templates/calculator/index.html.twig`:

```twig
    {% extends 'base.html.twig' %}

    {% block body %}
    <h1>Calculator home</h1>

        <form method="post" action="{{ url('app_calculator_process') }}">
            <p>
                Num 1:
                <input type="text" name="num1" value="1">
            </p>
            <p>
                Num 2:
                <input type="text" name="num2" value="1">
            </p>
            <p>
                Operation:
                <br>
                ADD
                <input type="radio" name="operator" value="add" checked>
                <br>
                SUBTRACT
                <input type="radio" name="operator" value="subtract">
                <br>
                DIVIDE
                <input type="radio" name="operator" value="divide">
            </p>

            <p>
                <input type="submit" name="calc_submit">
            </p>
        </form>

    {% endblock %}
```

and the Twig template to confirm received values, and display the answer `result.html.twig` contains:

```twig
    <h1>Calc RESULT</h1>
    <p>
        Your inputs were:
        <br>
        n1 = {{ n1 }}
        <br>
        n2 = {{ n2 }}
        <br>
        operator = {{ operator }}
    <p>
        answer = {{ answer }}
```

## Create a `CalculatorTest` class

Using the Symfony make tool, let's create a `CalculatorTest` class in `/src/Tests/`:

```bash
    $symfony console make:test

     Which test type would you like?:
      [TestCase       ] basic PHPUnit tests
      [KernelTestCase ] basic tests that have access to Symfony services
      [WebTestCase    ] to run browser-like scenarios, but that don't execute JavaScript code
      [ApiTestCase    ] to run API-oriented scenarios
      [PantherTestCase] to run e2e scenarios, using a real-browser or HTTP client and a real web server
     > WebTestCase


    Choose a class name for your test, like:
     * UtilTest (to create tests/UtilTest.php)
     * Service\UtilTest (to create tests/Service/UtilTest.php)
     * \App\Tests\Service\UtilTest (to create tests/Service/UtilTest.php)

     The name of the test class (e.g. BlogPostTest):
     > CalculatorTest

     created: tests/CalculatorTest.php

      Success!
```

## Test we can get a reference to the form

Let's test that can see the form page

```php
    public function testHomepageResponseCodeOkay()
    {
        // Arrange
        $url = '/calculator';
        $httpMethod = 'GET';
        $client = static::createClient();
        $expectedResult = Response::HTTP_OK;

        // Assert
        $client->request($httpMethod, $url);
        $statusCode = $client->getResponse()->getStatusCode();

        // Assert
        $this->assertSame($expectedResult, $statusCode);
    }
```

Let's test that we can get a reference to the form on this page, via its 'submit' button:

```php
    public function testFormReferenceNotNull()
    {
        // Arrange
        $url = '/calculator';
        $httpMethod = 'GET';
        $client = static::createClient();
        $crawler = $client->request($httpMethod, $url);
        $buttonName = 'calc_submit';

        // Act
        $buttonCrawlerNode = $crawler->selectButton($buttonName);
        $form = $buttonCrawlerNode->form();

        // Assert
        $this->assertNotNull($form);
    }
```

NOTE: We have to give each form button we wish to test either a `name` or `id` attribute. In our example we gave our calculator form the `name` attribute with value `calc_submit`:

```
    <input type="submit" name="calc_submit">
````

## Submitting the form

Assuming our form has some default values, we can test submitting the form by then checking if the content of the response after clicking the submit button contains test 'Calc RESULT':

```php
    public function testCanSubmitAndSeeResultText()
    {
        // Arrange
        $url = '/calculator';
        $httpMethod = 'GET';
        $client = static::createClient();
        $crawler = $client->request($httpMethod, $url);
        $expectedContentAfterSubmission = 'Calc RESULT';
        $expectedContentLowerCase = strtolower($expectedContentAfterSubmission);
        $buttonName = 'calc_submit';

        // Act
        $buttonCrawlerNode = $crawler->selectButton($buttonName);
        $form = $buttonCrawlerNode->form();

        // submit the form
        $client->submit($form);

        // get content from next Response
        $content = $client->getResponse()->getContent();
        $contentLowerCase = strtolower($content);

        // Assert
        $this->assertContains($expectedContentLowerCase, $contentLowerCase);
    }
```

## Entering form values then submitting

Once we have a reference to a form (`$form`) entering values is completed as array entry:

```php
    $form['num1'] = 1;
    $form['num2'] = 2;
    $form['operator'] = 'add';
```

So we can now test that we can enter some values, submit the form, and check the values in the response generated.

Let's submit 1, 2 and `add`:

```php
    public function testSubmitOneAndTwoAndValuesConfirmed()
    {
        // Arrange
        $url = '/calculator';
        $httpMethod = 'GET';
        $client = static::createClient();
        $crawler = $client->request($httpMethod, $url);
        $buttonName = 'calc_submit';


        // Act
        $buttonCrawlerNode = $crawler->selectButton($buttonName);
        $form = $buttonCrawlerNode->form();

        $form['num1'] = 1;
        $form['num2'] = 2;
        $form['operator'] = 'add';

        // submit the form & get content
        $crawler = $client->submit($form);
        $content = $client->getResponse()->getContent();

        // Assert
        $this->assertStringContainsString(
            '1',
            $content
        );
        $this->assertStringContainsString(
            '2',
            $content
        );
        $this->assertStringContainsString(
            'add',
            $content
        );
    }
```

The test above tests that after submitting the form we see the values submitted confirmed back to us.

## Testing we get the correct result via form submission

Assuming all our `Calculator`, methods have been inidividudally **unit tested**, we can now test that after submitting some values via our web form, we get the correct result returned to the user in the final response.

Let's submit 1, 2 and `add`, and look for `3` in the final response:

```php

    public function testSubmitOneAndTwoAndResultCorrect()
    {
        // Arrange
        $url = '/calculator';
        $httpMethod = 'GET';
        $client = static::createClient();
        $num1 = 1;
        $num2 = 2;
        $operator = 'add';
        $expectedResult = 3;
        // must be string for string search
        $expectedResultString = $expectedResult . '';
        $buttonName = 'calc_submit';

        // Act

        // (1) get form page
        $crawler = $client->request($httpMethod, $url);

        // (2) get reference to the form
        $buttonCrawlerNode = $crawler->selectButton($buttonName);
        $form = $buttonCrawlerNode->form();

        // (3) insert form data
        $form['num1'] = $num1;
        $form['num2'] = $num2;
        $form['operator'] = $operator;

        // (4) submit the form
        $crawler = $client->submit($form);
        $content = $client->getResponse()->getContent();

        // Assert
        $this->assertStringContainsString($expectedResultString, $content);
```

That's it - we can now select forms, enter values, submit the form and interrogate the response after the submitted form has been processed.

## Selecting form, entering values and submitting in one step

Using the **fluent** interface,, Symfony allows us to combine the steps of selecting the form, setting form values and submitting the form. E.g.:

```php
    $client->submit($client->request($httpMethod, $url)->selectButton($buttonName)->form([
        'num1'  => $num1,
        'num2'  => $num2,
        'operator'  => $operator,
    ]));
```

So we can write a test with fewer steps if we wish:

```php
    public function testSelectSetValuesSubmitInOneGo()
    {
        // Arrange
        $url = '/calc';
        $httpMethod = 'GET';
        $client = static::createClient();
        $num1 = 1;
        $num2 = 2;
        $operator = 'add';
        $expectedResult = 3;
        // must be string for string search
        $expectedResultString = $expectedResult . '';
        $buttonName = 'calc_submit';

        // Act
        $client->submit($client->request($httpMethod, $url)->selectButton($buttonName)->form([
                'num1'  => $num1,
                'num2'  => $num2,
                'operator'  => $operator,
        ]));
        $content = $client->getResponse()->getContent();

        // Assert
        $this->assertStringContainsString($expectedResultString, $content);
    }
```

## Using a Data Provider to test forms

Let's list some operations and numbers and expected answers as a Data Provider array, and have a single test method automatically loop through testing all those data sets.

```php
    public function equationsProvider(): array
    {
        return [
            [1, 1, 'add', 2],
            [1, 2, 'add', 3],
            [5, 2, 'subtract', 3],
            [5, 4, 'subtract', 1],
        ]
    }
```

We can now write a parameterized test method, with the required speical annotation comment naming the Data Provider method:

```php
    /**
     * @dataProvider equationsProvider
     */
    public function testSelectSetValuesSubmitInOneGoWithProvider(int $num1, int $num2, string $operator, int $expectedAnswer)
    {
        // Arrange
        $url = '/calculator';
        $httpMethod = 'GET';
        $client = static::createClient();

        // must be string for string search
        $buttonName = 'calc_submit';

        // Act
        $client->submit($client->request($httpMethod, $url)->selectButton($buttonName)->form([
            'num1'  => $num1,
            'num2'  => $num2,
            'operator'  => $operator,
        ]));
        $content = $client->getResponse()->getContent();

        // Assert
        $this->assertStringContainsString($expectedAnswer, $content);
    }
```

We can see our 4 data sets used in teh TextDox output file (`/build/textdox.txt`):

```text
    Calculator (App\Tests\Controller\Calculator)
     [x] Can visit calculator page okay
     [x] Form reference not null
     [x] Can submit and see result text
     [x] Submit one and two and values confirmed
     [x] Submit one and two and result correct
     [x] Select set values submit in one go
     [x] Select set values submit in one go with provider with data set #0
     [x] Select set values submit in one go with provider with data set #1
     [x] Select set values submit in one go with provider with data set #2
     [x] Select set values submit in one go with provider with data set #3
```

