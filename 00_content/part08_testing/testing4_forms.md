
# Testing web forms

## Testing forms (project `test09`)

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
    /**
     * @Route("/calc", name="calc_home")
     */
    public function indexAction()
    {
        return $this->render('calc/index.html.twig', []);
    }
```

and a 'process' controller method to recevied the form data (n1, n2, operator) and process it:
There is a calculator home page that displays the form Twig template at `/templates/calc/index.html.twig`:


```php
    /**
     * @Route("/calc/process", name="calc_process")
     */
    public function processAction(Request $request)
    {
        // extract name values from POST data
        $n1 = $request->request->get('num1');
        $n2 = $request->request->get('num2');
        $operator = $request->request->get('operator');

        $calc = new Calculator();
        $answer = $calc->process($n1, $n2, $operator);

        return $this->render(
            'calc/result.html.twig',
            [
                'n1' => $n1,
                'n2' => $n2,
                'operator' => $operator,
                'answer' => $answer
            ]
        );
    }
```

The Twig template to display our form looks as follows `/templates/calc/index.html.twig`:

```twig
    {% extends 'base.html.twig' %}

    {% block body %}
    <h1>Calculator home</h1>

        <form method="post" action="{{ url('calc_process') }}">
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

## Test we can get a reference to the form

Let's test that can see the form page

```php
    public function testHomepageResponseCodeOkay()
    {
        // Arrange
        $url = '/calc';
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
        $url = '/calc';
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
        $url = '/calc';
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

        // get content from next Response & make lower case
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
        $url = '/calc';
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
        $contentLowerCase = strtolower($content);

        // Assert
        $this->assertContains(
            '1',
            $contentLowerCase
        );
        $this->assertContains(
            '2',
            $contentLowerCase
        );
        $this->assertContains(
            'add',
            $contentLowerCase
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
        $this->assertContains($expectedResultString, $content);
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
        $this->assertContains($expectedResultString, $content);
    }
```
