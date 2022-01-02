
# Check Codeception is working


## Run Codeception (with no tests!)

Let's check Codeception is working (even though we haven't created any tests yet):

```bash
    $ vendor/bin/codecept run
    
    Codeception PHP Testing Framework v2.5.4
    Powered by PHPUnit 7.5.6 by Sebastian Bergmann and contributors.
    Running with seed: 
    
    
    App\Tests.acceptance Tests (0) 
    --------------------------------------------------------------------------------------------------------
    
    App\Tests.functional Tests (0) 
    --------------------------------------------------------------------------------------------------------
        
    App\Tests.unit Tests (0) 
    --------------------------------------------------------------------------------------------------------
    
    
    Time: 1.69 seconds, Memory: 38.25 MB
    
    No tests executed!
```

## Test with a simple Unit test

Let's create a simple Unit test. Codeception is built on top of PHPUnit, so for Unit Testing our classes are very similar to those we'd write for a non-Symfony PHP project:

1. Use the `vendor/bin/codecept` executable to generate a skeleton Unit Test for us:

    ```bash
        $ vendor/bin/codecept g:test unit FirstUnitTest
            Test was created in /cept1/tests/unit/FirstUnitTest.php
    ```
    
1. You should now have a new class `tests/unit/FirstUnitTest.php`:

    ```php
       namespace App\Tests;
       
       class FirstUnitTest extends \Codeception\Test\Unit
       {
           /**
            * @var \App\Tests\UnitTester
            */
           protected $tester;
           
           protected function _before()
           {
           }
       
           protected function _after()
           {
           }
       
           // tests
           public function testSomeFeature()
           {
       
           }
       }
    ```
    
1. Let's replace this class content to test the simple assertion that `1 + 1 = 2`:

    ```php
        namespace App\Tests;
        
        use Codeception\Test\Unit;
        
        class FirstUnitTest extends Unit
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

1. Run Codeception at the command line, and hopefully our test passes:

    ```bash
        $ vendor/bin/codecept run
        Codeception PHP Testing Framework v2.5.4
        Powered by PHPUnit 7.5.6 by Sebastian Bergmann and contributors.
        Running with seed: 
        
        
        App\Tests.acceptance Tests (0) 
        --------------------------------------------------------------------------------------------------------
                
        App\Tests.functional Tests (0) 
        --------------------------------------------------------------------------------------------------------
               
        App\Tests.unit Tests (1) 
        --------------------------------------------------------------------------------------------------------
             Testing App\Tests.unit
        TICK FirstUnitTest: One plus one equals two (0.01s)
        --------------------------------------------------------------------------------------------------------
               
        Time: 198 ms, Memory: 18.00 MB
        
        OK (1 test, 1 assertion)
    ```


Note the following:

- unit test classes are located in directory `/tests/unit`

- test classes end with the suffix `Test`, e.g. `SimpleTest`

- simple test classes extend the superclass `\Codeception\Test\Unit`

    -- if we add a `use` statement `Codeception\Test\Unit` then we can simple extend `Unit`

- simple test classes are in namespace `App\Tests`

    -- the names and namespaces of test classes testing a class in `/src` will reflect the namespace of the class being tested

    -- i.e. If we write a class to test `/src/Util/Calculator.php` it will be `/tests/Util/CalculatorTest.php`, and it will be in namespace `App\Util\Test`

    -- so our testing class architecture directly matches our source code architecture

##  Fixing error message about missing `ext-mbstring`:

If you get a message about "ext mbstring" required - when trying to work in Windows with Simple PHP Unit or make, then you need to enable this extension in your `php.ini` file:

- Just as you did for pdo_mysql, remove the semi-colon in front of the statement in the php.ini file:

- e.g. change `;extension=mbstring` to: `extension=mbstring`


## Testing other classes (project `codeception02`)

**our testing structure mirrors the code we are testing**

Let's create a very simple class `Calculator.php` in `/src/Util`^[Short for 'Utility' - i.e. useful stuff!], and then write a class to test our class. Our simple class will be a very simple calculator:

- method `add(...)` accepts 2 numbers and returns the result of adding them

- method `subtract()`  accepts 2 numbers and returns the result of subtractingt the second from the first

So our `/src/Util/Calculator.php` class is as follows:

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
    }
```

## The class to test our calculator


We now need to write a test class to test our calculator class. Since our source code class is `/src/Util/Calculator.php` then our testing class will be `/tests/Util/Calculator.php`. 

Let's generate a new Unit Test class to test our `Calculator` class:

```bash
    $ vendor/bin/codecept g:test unit CalculatorTest

        Test was created in /cept2/tests/unit/CalculatorTest.php
```


Since the namespace of our source code class was `App\Util` then the namespace of our testing class will be `App\Util\Test`. Let's test making an instance-object of our class `Calculator`, and we will make 2 assertions:


- the reference to the new object is not NULL

- invoking the `add(...)` method with arguments of (1,1) and returns the correct answer (2!)

Here's the listing for our edited class `CalculatorTest`:

```php
    namespace App\Util\Tests;
    
    use Codeception\Test\Unit;
    use App\Util\Calculator;
    
    class CalculatorTest extends Unit
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

- we had to add `use` statements for the class we are testing (`App\Util\Calculator`) and the PHP Unit TestCase class we are extending (`\Codeception\Test\Unit`)


Run the tests - if all goes well we should see 3 out of 3 tests passing:

```bash
    $ vendor/bin/codecept run
    Codeception PHP Testing Framework v2.5.4
    Powered by PHPUnit 7.5.6 by Sebastian Bergmann and contributors.
    Running with seed: 
    
    
    App\Tests.acceptance Tests (0) 
    --------------------------------------------------------------------------------------------------------
       
    App\Tests.functional Tests (0) 
    --------------------------------------------------------------------------------------------------------
    
    App\Tests.unit Tests (3) 
    --------------------------------------------------------------------------------------------------------
         Testing App\Tests.unit
    TICK CalculatorTest: Can create object (0.01s)
    TICK CalculatorTest: Add one and one (0.00s)
    TICK FirstUnitTest: One plus one equals two (0.00s)
    --------------------------------------------------------------------------------------------------------
    
    Time: 166 ms, Memory: 18.00 MB
    
    OK (3 tests, 3 assertions)

```

