# PHP Class Generator

The PHP component generator provides implementations for generating PHP classes, interfaces, Traits, Methods, Properties code from configured classes.

## Usage

* Creating a class Property

```php
//...
use Drewlabs\CodeGenerator\Models\PHPClassProperty;
use Drewlabs\CodeGenerator\Types\PHPTypesModifiers;

// ...

// Creates a property providing all definition in the constructor
$property = new PHPClassProperty($name, $type, $modifier, $value, $description);

// We can also create a property and set the definitions later
$property = (new PHPClassProperty($name))
    ->setModifier($modifier ?? PHPTypesModifiers::PUBLIC)
    ->addComment([$description])
    ->value($value);

// Converting the Property to it string representation
$code = $property->__toString();
```

* Creating a const class property

```php
// Simply calling asConstant on the default PHPClassProperty class object convert the property to a constant property
$property = (new PHPClassProperty($name))
    ->asConstant()
    ->setModifier($modifier ?? PHPTypesModifiers::PUBLIC)
    ->addComment([$description, 'This is a second line comment'])
    ->value($value);

// Converting the Property to it string representation
return $property->__toString();
```

* Creating a class method

```php
// ...
use Drewlabs\CodeGenerator\Models\PHPClassMethod;
use Drewlabs\CodeGenerator\Models\PHPFunctionParameter;
use RuntimeException;

// ...
$method = (new PHPClassMethod('__construct'))->throws([
        RuntimeException::class
    ])->addParam(new PHPFunctionParameter('users', null, ["user1" => "Sandra", "user2" => "Emily"]))
        ->addParam((new PHPFunctionParameter('params', PHPFunctionParameter::class))->asOptional())
        ->addContents(
            <<<EOT
\$this->users_ = \$users;
\$this->params_ = \$params;
EOT
        )->addLine('// This is an extra line')
        ->setReturnType(Stringable::class)
        ->addComment('This is a PHP Class method');

// Method definition to string
$method->__toString();
```

* Creating an interface method

```php
$method = (new PHPClassMethod(
    'write',
    [
        new PHPFunctionParameter('name', 'string'),
        new PHPFunctionParameter('params', PHPFunctionParameter::class)
    ],
))->throws([
    RuntimeException::class
])
->asInterfaceMethod()
->setReturnType(Stringable::class)
->setModifier(PHPTypesModifiers::PUBLIC);

// Method definition to string
$method->__toString();

```

* Creating PHP class

```php
use Drewlabs\CodeGenerator\Contracts\Stringable;
use Drewlabs\CodeGenerator\Models\PHPFunctionParameter;
use Drewlabs\CodeGenerator\Models\PHPClass;
use Drewlabs\CodeGenerator\Models\PHPClassMethod;
use Drewlabs\CodeGenerator\Models\PHPClassProperty;

    $class_ = (new PHPClass("Person", [], [
        (new PHPClassMethod('__construct', [
            new PHPFunctionParameter('firstname', 'string'),
            new PHPFunctionParameter('lastname', 'string')
        ], null, 'public', 'Class initializer')),
        (new PHPClassMethod('setFirstName', [
            new PHPFunctionParameter('firstname', 'string'),
            (new PHPFunctionParameter('default', 'string', 'DEFAULT'))->asOptional()
        ], "self", 'public', 'firstname property setter')),
        (new PHPClassMethod('setParent', [
            new PHPFunctionParameter('person',  "\\App\\Person\\Contracts\\PersonInterface")
        ], "self", 'public', 'parent property setter')),
        (new PHPClassMethod('getFirstName', [], "string", 'public', 'firstname property getter')),
    ],))->setBaseClass("\\App\\Core\\PersonBase")
        ->addImplementation("\\App\\Contracts\\PersonInterface")
        ->addImplementation("\\App\\Contracts\\HumanInterface")
        ->asFinal()
        ->addProperty(new PHPClassProperty('firstname', 'string', 'private', null, 'Person first name'))
        ->addConstant(new PHPClassProperty('lastname', 'string', 'private', null, 'Person last name'))
        ->addToNamespace("App\\Models");
// Convert class definition to string
$class_->__toString();
```

* Creating PHP Trait

```php
$trait_ = (new PHPTrait(
        "HasValidatableAttributes",
        [
            (new PHPClassMethod('setFirstName', [
                new PHPFunctionParameter('firstname', 'string')
            ], "self", 'public', 'firstname property setter')),
            (new PHPClassMethod('setParent', [
                new PHPFunctionParameter('person',  "\\App\\Person\\Contracts\\PersonInterface")
            ], "self", 'public', 'parent property setter')),
            (new PHPClassMethod('getFirstName', [], "string", 'public', 'firstname property getter')),
        ]
    ))
    ->addMethod((new PHPClassMethod('__construct', [
                new PHPFunctionParameter('firstname', 'string'),
                new PHPFunctionParameter('lastname', 'string')
            ], null, 'public', 'Class initializer')))
        ->addTrait('\\App\\Person\\Traits\\PersonInterface')
        ->addToNamespace("App\\Models")
        ->addProperty(new PHPClassProperty('firstname', 'string', 'private', null, 'Person first name'))
        ->addConstant(new PHPClassProperty('lastname', 'string', 'private', null, 'Person last name'));

// Convert trait definition to string
$trait_->__toString();
```

* Creating PHP interfaces

```php
$interface_ = (new PHPInterface(
        "Writer",
        [
            (new PHPClassMethod('setWriter', [
                new PHPFunctionParameter('writer', 'App\\Contracts\\Writer')
            ], "self", 'public', 'Writer property setter')),
            (new PHPClassMethod('write', [
                new PHPFunctionParameter('buffer', 'string')
            ], null, 'public', 'Write the buffer to the console')),
        ]
    ))->addToNamespace("App\\Contracts\\Writer")
        ->setBaseInterface('App\\Contracts\\Writer\\BufferWriter');

// Converting PHP interface definition to string
$interface_->__toString();
```
