
# Unit testing in Symfony


## Testing in Symfony

Symfony is built by an open source community. There is a lot of information about how to test Symfony in the official documentation pages:

- [Symfony testing](http://symfony.com/doc/current/testing.html)

- [Testing with user authentication tokens](http://symfony.com/doc/current/testing/simulating_authentication.html)

- [How to Simulate HTTP Authentication in a Functional Test](http://symfony.com/doc/current/testing/http_authentication.html)


## Installing Simple-PHPUnit (project `test01`)

Symfony has as special 'testpack' that works with PHPUnit. Add this to your project as follows:

```bash
    $ composer require --dev symfony/test-pack
```

You should now see a `/tests` directory created.

Run our tests - we have none, so should get a message telling us no tests were executed:


```bash
    $ bin/phpunit

    PHPUnit 9.5.19

    No tests executed!
```


## Creating a test class

Let's create a simple test (1 + 1 = 2!) to check everything is working okay.

Create a new class `/tests/SimpleTest.php` containing the following:

```php
    <?php
    namespace App\Tests;

    use PHPUnit\Framework\TestCase;

    class SimpleTest extends TestCase
    {
        public function testOnePlusOneEqualsTwo()
        {
            // Arrange
            $num1 = 1;
            $num2 = 1;
            $expectedResult = 2;

            // Act
            $result = $num1 + $num2;

            // Assert
            $this->assertEquals($expectedResult, $result);
        }
    }
```

Note the following:

- test classes are located in directory `/tests`

    - or a suitably named sub-directory, matching the `/src` namespaced folder they are testing

- test classes end with the suffix `Test`, e.g. `SimpleTest`

- simple test classes extend the superclass `\PHPUnit\Framework\TestCase`

    -- if we add a `uses` statement `use PHPUnit\Framework\TestCase` then we can simple extend `TestCase`

- simple test classes are in namespace `App\Tests`

    -- the names and namespaces of test classes testing a class in `/src` will reflect the namespace of the class being tested

    -- i.e. If we write a class to test `/src/Controller/DefaultController.php` it will be `/tests/Controller/DefaultControllerTest.php`, and it will be in namespace `App\Tests\Controller`

    -- so our testing class architecture directly matches our source code architecture

## Running our tests

Run our tests - we have 1 now, so should get a message telling us our test ran and passed:


```bash
    $ bin/phpunit

    Testing
    .                                                                   1 / 1 (100%)

    Time: 00:00.022, Memory: 10.00 MB

    OK (1 test, 1 assertion)
```


Dots are good. For each passed test you'll see a full stop. Then after all tests have run, you'll see a summary:

```bash
    1 / 1 (100%)
```

This tells us how many passed, out of how many, and what the pass percentage was. In our case, 1 out of 1 passed = 100%.

## Testing other classes  (project `test02`)

**our testing structure mirrors the code we are testing**

Let's create a very simple class `Calculator.php` in `/src/Util`^[Short for 'Utility' - i.e. useful stuff!], and then write a class to test our class. Our simple class will be a very simple calculator:

- method `add(...)` accepts 2 numbers and returns the result of adding them

- method `subtract()`  accepts 2 numbers and returns the result of subtracting the second from the first

so our `Calculator` class is as follows:

```php
    <?php
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
    }
```

## The class to test our calculator

We now need to write a test class to test our calculator class. Since our source code class is `/src/Util/Calculator.php` then our testing class will be `/tests/Util/CalculatorTest.php`. And since the namespace of our source code class was `App\Util` then the namespace of our testing class will be `App\Tests\Util`. Let's test making an instance-object of our class `Calculator`, and we will make 2 assertions:

- the reference to the new object is not NULL

- invoking the `add(...)` method with arguments of (1,1) and returns the correct answer (2!)

Here's the listing for our new test class `/tests/Util/CalculatorTest.php`:

```php
    namespace App\Tests\Util;

    use App\Util\Calculator;
    use PHPUnit\Framework\TestCase;

    class CalculatorTest extends TestCase
    {
        public function testCanCreateObject()
        {
            // Arrange
            $calculator = new Calculator();

            // Act

            // Assert
            $this->assertNotNull($calculator);
        }

        public function testAddOneAndOne()
        {
            // Arrange
            $calculator = new Calculator();
            $num1 = 1;
            $num2 = 1;
            $expectedResult = 2;

            // Act
            $result = $calculator->add($num1, $num2);

            // Assert
            $this->assertEquals($expectedResult, $result);
        }
    }

```

Note:

- we had to add `use` statements for the class we are testing (`App\Util\Calculator`) and the PHP Unit TestCase class we are extending (`use PHPUnit\Framework\TestCase`)


Run the tests - if all goes well we should see 3 out of 3 tests passing:

```bash
    $ bin/phpunit

    Testing
    ...                                                                 3 / 3 (100%)

    Time: 00:00.014, Memory: 10.00 MB

    OK (3 tests, 3 assertions)
```


## Using a data provider to test with multiple datasets

Rather than writing lots of methods to test different additions, let's use a **data provider** (via an annotation comment), to provide a single method with many sets of input and expected output values:

Here is our testing method:

```php
    /**
     * @dataProvider additionProvider
     */
    public function testAdditionsWithProvider($num1, $num2, $expectedResult)
    {
        // Arrange
        $calculator = new Calculator();

        // Act
        $result = $calculator->add($num1, $num2);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }
```

and here is the data provider (an array of arrays, with the right number of values for the parameters of `testAdditionsWithProvider(...)`:

```php
    public function additionProvider()
    {
        return [
            [1, 1, 2],
            [2, 2, 4],
            [0, 1, 1],
        ];
    }
```

Take special note of the annotation comment immediately before method `testAdditionsWithProvider(...)`:

```php
    /**
     * @dataProvider additionProvider
     */
```

The special comment starts with `/**`, and declares an annotation `@dataProvider`, followed by the name (identifier) of the method. Note especially that there are no parentheses `()` after the method name.

When we run Simple-PHPUnit now we see lots of tests being executed, repeatedly invoking `testAdditionsWithProvider(...)` with different arguments from the provider:

```bash
    $ vendor/bin/simple-phpunit
    PHPUnit 5.7.27 by Sebastian Bergmann and contributors.

    Testing Project Test Suite
    ......                                                              6 / 6 (100%)

    Time: 65 ms, Memory: 4.00MB

    OK (6 tests, 6 assertions)
```

## Configuring testing reports

In additional to instant reporting at the command line, PHPUnit offers several different methods of recording test output text-based files.

PHPUnit (when run with Symfony's Simple-PHPUnit) reads configuration settings from file `phpunit.dist.xml`. Most of the contents of this file (created as part of the installation of the Simple-PHPUnit package) can be left as their defaults. But we can add a range of logs by adding the following 'logging' element in this file.

Many projects follow a convention where testing output files are stored in a directory named `build`. We'll follow that convention below - but of course change the name and location of the test logs to anywhere you want.

Add the following into file  `phpunit.dist.xml`:

```xml
    <logging>
        <junit outputFile="junit.xml"/>
        <teamcity outputFile="./build/teamcity.txt"/>
        <testdoxHtml outputFile="./build/testdox.html"/>
        <testdoxText outputFile="./build/testdox.txt"/>
        <testdoxXml outputFile="./build/testdox.xml"/>
        <text outputFile="./build/logfile.txt"/>
    </logging>
```


Figure \ref{build_contents} shows a screenshot of the contents of the created `/build` directory after Simple-PHPUnit has been run.

![Contents of directory `/build`. \label{build_contents}](./03_figures/part_testing/1_build_contents.png)

The `.txt` file version of  **test dox** (**testdoxText**) is perhaps the simplest output - showing `[x]` next to a passed method and `[ ]` for a test that didn't pass. The text output turns the test method names into more English-like sentences:

```txt
     Simple (App\Tests\Simple)
      [x] One plus one equals two

     Calculator (App\Tests\Util\Calculator)
      [x] Can create object
      [x] Add one and one
      [x] Additions with provider with data set #0
      [x] Additions with provider with data set #1
      [x] Additions with provider with data set #2
```


## Testing for exceptions (project `test03`)

If our code throws an **Exception** while a test is being executed, and it was not caught, then we'll get an **Error** when we run our test.

For example, let's add a `divide(...)` method to our utility `Calculator` class:

```php
    public function divide($n, $divisor)
    {
        if(empty($divisor)){
            throw new \InvalidArgumentException("Divisor must be a number");
        }

        return $n / $divisor;
    }
```

In the code above we are throwing an `\InvalidArgumentException` when our `$divisor` argument is empty (0, null etc.).

Let's write a valid test (1/1 = 1) in class `CalculatorTest`:

```php
    public function testDivideOneAndOne()
    {
        // Arrange
        $calculator = new Calculator();
        $num1 = 1;
        $num2 = 1;
        $expectedResult = 1;

        // Act
        $result = $calculator->divide($num1, $num2);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }
```

This should pass.

Now let's try to write a test for 1 divided by zero. Not knowing how to deal with exceptions we might write something with a `fail(...)` instead of an `assert...`:

```php
    public function testDivideOneAndZero()
    {
        // Arrange
        $calculator = new Calculator();
        $num1 = 1;
        $num2 = 0;
        $expectedResult = 1;

        // Act
        $result = $calculator->divide($num1, $num2);

        // Assert - FAIL - should not get here!
        $this->fail('should not have got here - divide by zero not permitted');
    }
```

But when we run simple-phpunit we'll get an error since the (uncaught) Exceptions is thrown before our `fail(...)` statement is reached:

```bash
    $ vendor/bin/simple-phpunit
    PHPUnit 9.5.19

    Testing
    .......E                                                            8 / 8 (100%)

    Time: 00:00.028, Memory: 10.00 MB

    There was 1 error:

    1) App\Tests\Util\CalculatorTest::testDivideOneAndZero
    InvalidArgumentException: Divisor must be a number

    /Users/matt/test03/src/Util/Calculator.php:18
    /Users/matt/test03/tests/Util/CalculatorTest.php:82

    ERRORS!
    Tests: 8, Assertions: 7, Errors: 1.

```

And our logs will confirm the failure:

```
    Simple (App\Tests\Simple)
     [x] One plus one equals two

    Calculator (App\Tests\Util\Calculator)
     [x] Can create object
     [x] Add one and one
     [x] Additions with provider with data set #0
     [x] Additions with provider with data set #1
     [x] Additions with provider with data set #2
     [x] Divide one and one
     [ ] Divide one and zero
```

## PHPUnit `expectException(...)`
PHPUnit allows us to declare that we expect an exception - but we must declare this **before** we invoke the method that will throw the exception.

Here is our improved method, with `expectException(...)` and a better `fail(...)` statement, that tells us which exception was expected and not thrown:

```php
    public function testDivideOneAndZero()
    {
        // Arrange
        $calculator = new Calculator();
        $num1 = 1;
        $num2 = 0;
        $expectedResult = 1;

        // Expect exception - BEFORE you Act!
        $this->expectException(\InvalidArgumentException::class);

        // Act
        $result = $calculator->divide($num1, $num2);

        // Assert - FAIL - should not get here!
        $this->fail("Expected exception {\InvalidArgumentException::class} not thrown");
    }
```

Now all our tests pass:

```bash
    $ vendor/bin/simple-phpunit
    PHPUnit 9.5.19

    Testing
    ........                                                            8 / 8 (100%)

    Time: 00:00.026, Memory: 10.00 MB

    OK (8 tests, 8 assertions)
```



## Testing for custom Exception classes

While the built-in PHP Exceptions are find for simple projects, it is very useful to create custom exception classes for each project you create. Working with, and testing for, objects of custom Exception classes is very simple in Symfony:

1. Create your custom Exception class in `/src/Exception`, in the namespace `App\Exception`. For example you might create a custom Exception class for an invalid Currency in a money exchange system as follows:

    ```php
        // file: /src/Exception/UnknownCurrencyException.php
        <?php

        namespace App\Exception;

        class UnknownCurrencyException extends \Exception
        {
            public function __construct($message = null)
            {
                if(empty($message)) {
                    $message = 'Unknown currency';
                }
                parent::__construct($message);
            }
        }
    ```

1. Ensure your `/src/Util/Calculator.php` source code throws an instance of your custom Exception. For example:

    ```php
        use App\Exception\UnknownCurrencyException;

        ...

        public function euroOnlyExchange(string $currency)
        {
            $currency = strtolower($currency);
            if('euro' != $currency){
                throw new UnknownCurrencyException();
            }

            // other logic here ...
        }
    ```

1. In your tests your must check for the expected custom Exception class. E.g. using the annotation approach:

    ```php
        use App\Exception\UnknownCurrencyException;

        ...

        public function testInvalidCurrencyException()
        {
            // Arrange
            $calculator = new Calculator();
            $currency = 'I am not euro';

            // Expect exception - BEFORE you Act!
            $this->expectException(UnknownCurrencyException::class);

            // Act
            // ... code here to trigger exception to be thrown ...
            $calculator->euroOnlyExchange($currency);

            // Assert - FAIL - should not get here!
            $this->fail("Expected exception {\Exception} not thrown");
        }
    ```


## Checking Types with assertions

Sometimes we need to check the **type** of a variable. We can do this using the `assertInternalType(...)` method.

For example:

```php
    $result = 1 + 2;

    // check result is an integer
    $this->assertInternalType('int', $result);
```

Learn more in the PHPUnit documentation:

- [https://phpunit.de/manual/6.5/en/appendixes.assertions.html#appendixes.assertions.assertInternalType](https://phpunit.de/manual/6.5/en/appendixes.assertions.html#appendixes.assertions.assertInternalType)

## Same vs. Equals

There are 2 similar assertions in PHPUnit:

- `assertSame(...)`:  works like the `===` identity operator in PHP
- `assertEquals(...)`: works like the `==` comparison

When we want to know if the values inside (or referred to) by two variables or expressions are equivalent, we use the weaker `==` or `assertEquals(...)`. For example, do two variables refer to object-instances that contain the same property values, but may be different objects in memory.

When we want to know if the values inside (or referred to) by two variables are exactly the same, we use the stronger `===` or `assertSame(...)`. For example, do two variables both refer to the same object in memory.

The use of  `assertSame(...)` is useful in unit testing to check the types of values - since the value returned by a function must refer to the same numeric or string (or whatever) literal. So we could write another way to test that a function returns an integer result as follows:

```php
    $expectedResult = 3;
    $result = 1 + 2;

    // check result is an integer
    $this->assertSame($expectedResult, $result);
```
