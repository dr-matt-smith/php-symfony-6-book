# How to the free 'helper' Doctrine methods work?

## Custom database queries (project `findby01`)
PHP offers a runtime code reflection (or interpreter pre-processing if you prefer), that can intercept calls to non-existent methods of a class. We use the special **magic** PHP method `__call(...)` which expects 2 parameters, one for the non-existent method name, and one as an array of argument values passed to the non-existent method:

```php
    public function __call($methodName, $arguments)
    {
        ... do something with $methodName and $arguments
    }
```

## Preparation - new project with fixtures package

Create a new project:

```bash
     symfony new --webapp findby01
```

Now `cd` into the project in a terminal, and use `make:controller` to create a new controller class `DefaultController`:

```bash
     symfony console make:controller Default
```

Test the server, and if necessary, remove the `encore_entry_` lines inside the `stylesheets` and `javascripts` blocks in the base template (`/templates/base.html.twig`).


## Our example repository
Here is a simple class (`/src/Util/ExampleRepository.php`) that demonstrates how Doctrine uses `__call' to identify which Entity property we are trying to query by:

```php
    <?php
    namespace App\Util;
    
    /*
     * class to demonstrate how __call can be used by Doctrine repositories ...
     */
    class ExampleRepository
    {
        public function findAll()
        {
            return 'you called method findAll()';
        }
    
        public function __call($methodName, $arguments)
        {
            $html = '';
            $argsString = implode(', ', $arguments) . "\n";
    
            $html .= "you called method $methodName\n";
            $html .= "with arguments: $argsString\n";
    
            $result = $this->startsWithFindBy($methodName);
            if($result){
                $html .= "since the method called started with 'findBy'"
                . "\n it looks like you were searching by property '$result'\n";
            }
    
            return $html;
        }
    
        private function startsWithFindBy($name)
        {
            $needle = 'findBy';
            $pos = strpos($name, $needle);
    
            // since 0 would evaluate to FALSE, must use !== not simply !=
            if (($pos !== false) && ($pos == 0)){
                return substr($name, strlen($needle)); // text AFTER findBy
            }
    
            return false;
        }
    }
```
## Testing repopsitory from a controller

Add a new method `call()` to the `DefaultController` class to see this in action as follows:

```php
    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    Use App\Util\ExampleRepository;

    class DefaultController extends AbstractController
    {
        #[Route('/call', name: 'findByTest')]
        {
            // illustrate how __call works
            $exampleRepository = new ExampleRepository();

            $html = "<pre>";
            $html .=  "----- calling findAll() -----\n";
            $html .= $exampleRepository->findAll();

            $html .=  "\n\n----- calling findAllByProperty() -----\n";
            $html .= $exampleRepository->findByName('matt', 'smith');

            $html .=  "\n----- calling findAllByProperty() -----\n";
            $html .= $exampleRepository->findByProperty99('needle in haystack');

            $html .=  "\n----- calling badMethodName() -----\n";
            $html .= $exampleRepository->badMethodName('matt', 'smith');

            return new Response($html);
        }
        ...
```


See Figure \ref{query2} shows the `ExampleRepository` output you should visiting the `/call` route. We can see that:

- a call to `findAll()` works fine, since that is a defined public method of the class

- a call to `findByName(...)` would work fine, since we can use `__call(...)` to identify that this was a call to a helper `findBy<property>(...)` method

    - and we could add logic to check that this is a property of the Entity class and build an appropriate query from the arguments
    
- a call to `findByProperty99(...)` would work fine, since we can use `__call(...)` to identify that this was a call to a helper `findBy<property>(...)` method

    - and we could add logic to check that this is a property of the Entity class and build an appropriate query from the arguments

- a call to `badMethodName(...)` is caught by `__call(...)`, but fails our test for starting with `findBy`, and so we can ignore it

    - or log error or throw Exception or whatever our program spec says to do in these cases...

![Output from our ExampleRepository `__call` demo. \label{query2}](./03_figures/part03/q2_callExample.png)
