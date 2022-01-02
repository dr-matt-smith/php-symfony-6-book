
# Unit testing in Symfony


## Testing in Symfony

Symfony is built by an open source community. There is a lot of information about how to test Symfony in the official documentation pages:

- [Symfony testing](http://symfony.com/doc/current/testing.html)

- [Testing with user authentication tokens](http://symfony.com/doc/current/testing/simulating_authentication.html)

- [How to Simulate HTTP Authentication in a Functional Test](http://symfony.com/doc/current/testing/http_authentication.html)


## Installing Simple-PHPUnit (project `test01`)

Symfony has as special 'bridge' to work with PHPUnit. Add this to your project as follows:

```bash
    $ composer req --dev simple-phpunit
```

You should now see a `/tests` directory created. Let's create a simple test (1 + 1 = 2!) to check everything is working okay.

Create a new class `/tests/SimpleTest.php` containing the following:

```php
    <?php
    namespace App\Test;

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

- test classes end with the suffix `Test`, e.g. `SimpleTest`

- simple test classes extend the superclass `\PHPUnit\Framework\TestCase`

    -- if we add a `uses` statement `use PHPUnit\Framework\TestCase` then we can simple extend `TestCase`

- simple test classes are in namespace `App\Test`

    -- the names and namespaces of test classes testing a class in `/src` will reflect the namespace of the class being tested

    -- i.e. If we write a class to test `/src/Controller/DefaultController.php` it will be `/tests/Controller/DefaultControllerTest.php`, and it will be in namespace `App\Controller\Test`

    -- so our testing class architecture directly matches our source code architecture

## Completing the installation

The first time you run Simple-PHPUnit it will probably need to install some more files.

There is an executable file in `/vendor/bin` named `simple-phpunit`. To run it just type `vendor/bin/simple-phpunit` (or for Windows, to run the BATch file, type `vendor\bin\simple-phpunit` - with backslashes since this is a Windows file path):

```bash
    $ vendor/bin/simple-phpunit
    ./composer.json has been updated
    Loading composer repositories with package information
    Updating dependencies
    Package operations: 19 installs, 0 updates, 0 removals
      - Installing sebastian/recursion-context (2.0.0): Loading from cache
      ...
      - Installing symfony/phpunit-bridge (dev-master): Symlinking from /Users/matt/Library/Mobile Documents/com~apple~CloudDocs/11_Books/symfony/php-symfony4-book-codes/part-testing/test01_simple/vendor/symfony/phpunit-bridge
    Writing lock file
    Generating optimized autoload files
```



NOTE: Error message about missing `ext-mbstring`:

- if you get a message about "ext mbstring" required - when trying to work in Windows with Simple PHP Unit or make:

- simple solution - in your php.ini file

    -- Just as you did for pdo_mysql, remove the semi-colon in front of the statement in the php.ini file:

    -- e.g. change `;extension=mbstring` to: `extension=mbstring`


## Running Simple-PHPUnit

Let's run the tests (using the default configuration settings, in `phpunit.dist.xml`):

```bash
    $ vendor/bin/simple-phpunit
    PHPUnit 5.7.27 by Sebastian Bergmann and contributors.
    Testing Project Test Suite
    .                                                                   1 / 1 (100%)

    Time: 93 ms, Memory: 4.00MB
    OK (1 test, 1 assertion)
```

Dots are good. For each passed test you'll see a full stop. Then after all tests have run, you'll see a summary:

```bash
    1 / 1 (100%)
```

This tells us how many passed, out of how many, and what the pass percentage was. In our case, 1 out of 1 passed = 100%.

## Testing other classes  (project `test02`)

**our testing structure mirrors the code we are testing**

Let's create a very simple class `Calculator.php` in `/src/Util`^[Short for 'Utilty' - i.e. useful stuff!], and then write a class to test our class. Our simple class will be a very simple calculator:

- method `add(...)` accepts 2 numbers and returns the result of adding them

- method `subtract()`  accepts 2 numbers and returns the result of subtractingt the second from the first

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

We now need to write a test class to test our calculator class. Since our source code class is `/src/Util/Calculator.php` then our testing class will be `/tests/Util/Calculator.php`. And since the namespace of our source code class was `App\Util` then the namespace of our testing class will be `App\Util\Test`. Let's test making an instance-object of our class `Calculator`, and we will make 2 assertions:

- the reference to the new object is not NULL

- invoking the `add(...)` method with arguments of (1,1) and returns the correct answer (2!)

Here's the listing for our class `CalculatorTest`:

```php
    namespace App\Util\Test;

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
    $ vendor/bin/simple-phpunit
    PHPUnit 5.7.27 by Sebastian Bergmann and contributors.
    Testing Project Test Suite
    ...                                                                 3 / 3 (100%)

    Time: 64 ms, Memory: 4.00MB
    OK (3 tests, 3 assertions)
```

## Using a data provider to test with multiple datasets (project `test03`)

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

## Configuring testing reports (project `test04`)

In additional to instant reporting at the command line, PHPUnit offers several different methods of recording test output text-based files.

PHPUnit (when run with Symfony's Simple-PHPUnit) reads configuration settings from file `phpunit.dist.xml`. Most of the contents of this file (created as part of the installation of the Simple-PHPUnit package) can be left as their defaults. But we can add a range of logs by adding the following 'logging' element in this file.

Many projects follow a convention where testing output files are stored in a directory named `build`. We'll follow that convention below - but of course change the name and location of the test logs to anywhere you want.

Add the following into file  `phpunit.dist.xml`:

```xml
    <logging>
        <log type="junit" target="./build/logfile.xml"/>
        <log type="testdox-html" target="./build/testdox.html"/>
        <log type="testdox-text" target="./build/testdox.txt"/>
        <log type="tap" target="./build/logfile.tap"/>
    </logging>
```


Figure \ref{build_contents} shows a screenshot of the contents of the created `/build` directory after Simple-PHPUnit has been run.

![Contents of directory `/build`. \label{build_contents}](./03_figures/part_testing/1_build_contents.png)

The `.txt` file version of **testdox** is perhaps the simplest output - showing `[x]` next to a passed method and `[ ]` for a test that didn't pass. The text output turns the test method names into more English-like sentences:

```txt
    App\Test\Simple
     [x] One plus one equals two

    App\Util\Test\Calculator
     [x] Can create object
     [x] Add one and one
     [x] Additions with provider
```

Another easy to understand logging format is the TAP (Test-Anywhere Protocol). Although official deprecated by PHPUnit it still seems to work. What is nice about the TAP format is that repeated invocations of test methods iterating through a data-provider are enumerated, with the values. So we can see how many times, and their successes, a method was invoked with test data. This file is named (by our XML configuration above) `logfile.tap`:

```txt
    TAP version 13
    ok 1 - App\Test\SimpleTest::testOnePlusOneEqualsTwo
    ok 2 - App\Util\Test\CalculatorTest::testCanCreateObject
    ok 3 - App\Util\Test\CalculatorTest::testAddOneAndOne
    ok 4 - App\Util\Test\CalculatorTest::testAdditionsWithProvider with data set #0 (1, 1, 2)
    ok 5 - App\Util\Test\CalculatorTest::testAdditionsWithProvider with data set #1 (2, 2, 4)
    ok 6 - App\Util\Test\CalculatorTest::testAdditionsWithProvider with data set #2 (0, 1, 1)
    1..6
```


## Testing for exceptions (project `test07`)

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
    PHPUnit 5.7.27 by Sebastian Bergmann and contributors.

    Warning:       Deprecated TAP test listener used

    Testing Project Test Suite
    .........E                                                        10 / 10 (100%)

    Time: 1.21 seconds, Memory: 10.00MB

    There was 1 error:

    1) App\Util\Test\CalculatorTest::testDivideOneAndZero
    InvalidArgumentException: Divisor must be a number

    .../src/Util/Calculator.php:21
    /Users/matt/Library/Mobile Documents/com~apple~CloudDocs/11_Books/symfony/php-symfony4-book-codes/part-testing/test07_exceptions/tests/Util/CalculatorTest.php:84

    ERRORS!
    Tests: 10, Assertions: 9, Errors: 1.
```

And our logs will confirm the failure:

```
    App\Tests\Controller\DefaultController
     [x] Homepage response code okay
     [x] Homepage content contains hello world

    App\Test\Simple
     [x] One plus one equals two

    App\Util\Test\Calculator
     [x] Can create object
     [x] Add one and one
     [x] Additions with provider
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
    PHPUnit 5.7.27 by Sebastian Bergmann and contributors.

    Warning:       Deprecated TAP test listener used

    Testing Project Test Suite
    ..........                                                        10 / 10 (100%)
```


## PHPUnit annotation comment `@expectedException`

PHPUnit allows us to use an annotation comment to state that we expect an exception to be thrown during the execution of a particular test. This is a nice way to keep our test logic simple.

Since annotation comments are declared immediately **before** the method, some programmers (I do!) prefer the annotation way of declaring that we expect a test method to result in an exception being thrown:

```php
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDivideOneAndZeroAnnotation()
    {
        // Arrange
        $calculator = new Calculator();
        $num1 = 1;
        $num2 = 0;

        // Act
        $result = $calculator->divide($num1, $num2);

        // Assert - FAIL - should not get here!
        $this->fail("Expected exception {\InvalidArgumentException::class} not thrown");
    }
```

NOTE: You must ensure the exception class is fully namespaced in the annotation comment (no `::class` shortcuts!).

## Testing for custom Exception classes

While the built-in PHP Exceptions are find for simple projects, it is very useful to create custom exception classes for each project you create. Working with, and testing for, objects of custom Exception classes is very simple in Symfony:

1. Create your custom Exception class in `/src/Exception`, in the namespace `App\Exception`. For example you might create a custom Exception class for an invalid Currency in a money exchange system as follows:

    ```php
        // file: /src/Exception/UnknownCurrencyException.php

        namespace App\Exception;

        use Exception;

        class UnknownCurrencyException extends Exception
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

1. Ensure your source code throws an instance of your custom Exception. For example:

    ```php
        use App\Exception\UnknownCurrencyException;

        ...

        public function euroOnlyExchange($currency)
        {
            $currency = strtolower($currency);
            if('euro' != $currency)){
                throw new UnknownCurrencyException();
            }
    ```

1. In your tests your must check for the expected custom Exception class. E.g. using the annotation approach:

    ```php
        /**
         * @expectedException App\Exception\UnknownCurrencyException
         */
        public function testInvalidCurrencyException()
        {
            ... code here to trigger exception to be thrown ...

            // Assert - FAIL - should not get here!
            $this->fail("Expected exception {\Exception} not thrown");
        }
    ```


**NOTE**: You have to provide the full namespace in the annotation comment, i.e. `App\Exception\UnknownCurrencyException`. Having a `use` statement above will not work properly

## Checking Types with assertions

Sometimes we need to check the **type** of a variable. We can do this using the `assertInternalType(...)` method.

For example:

```php
    $result = 1 + 2;

    // check result is an interger
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

    // check result is an interger
    $this->assertSame($expectedResult, $result);
```
