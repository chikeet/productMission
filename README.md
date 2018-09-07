# Dixons Carphone: Test mission

## Functionality
As described in the [task](http://tech.dixons.cz/test).

## Basics
- **Minimum PHP version:** 7.1
- **Dependencies:** Only for tests. Anything else is expected to be injected through the controller constructor.
- **Tests:** Controller tests using `Nette\Tester` and `Mockery`.


## How to run tests in CLI

1. Run `composer install` in project root folder. 
2. `cd tests`
3. `php "../vendor/nette/tester/src/tester.php" -c "php.ini" "cases/"`

