<?php

require __DIR__ . '/vendor/autoload.php';

use Drewlabs\CodeGenerator\CommentModelFactory;
use Drewlabs\CodeGenerator\Contracts\Stringable;
use Drewlabs\CodeGenerator\Models\PHPFunctionParameter;
use Drewlabs\CodeGenerator\Models\PHPClass;
use Drewlabs\CodeGenerator\Models\PHPClassMethod;
use Drewlabs\CodeGenerator\Models\PHPClassProperty;
use Drewlabs\CodeGenerator\Models\PHPInterface;
use Drewlabs\CodeGenerator\Models\PHPTrait;
use Drewlabs\CodeGenerator\Types\PHPTypesModifiers;

function create_comments($params, $multiline = false)
{
    $comment = (new CommentModelFactory($multiline))->make($params);
    return $comment->__toString();
}

function create_php_class_property(string $name, $type, $modifier = 'public', $value = null, $description = '')
{
    $property = new PHPClassProperty($name, $type, $modifier, $value, $description);

    return $property->__toString();
}

function create_php_class_property_with_methods(string $name, $value, $modifier = 'public', $description = '')
{
    $property = (new PHPClassProperty($name))
        ->setModifier($modifier ?? PHPTypesModifiers::PUBLIC)
        ->addComment([$description])
        ->value($value);

    return $property->__toString();
}
function create_php_class_const_property_with_methods(string $name, $value, $modifier = 'public', $description = '')
{
    $property = (new PHPClassProperty($name))
        ->asConstant()
        ->setModifier($modifier ?? PHPTypesModifiers::PUBLIC)
        ->addComment([$description, 'This is a second line comment'])
        ->value($value);

    return $property->__toString();
}

function create_interface_method()
{
    $method = (new PHPClassMethod('write', [
        new PHPFunctionParameter('name', 'string'),
        new PHPFunctionParameter('params', PHPFunctionParameter::class)
    ], Stringable::class, 'public', null))->throws([
        RuntimeException::class
    ])->asInterfaceMethod();
    return $method->__toString();
}

function create_class_method()
{
    $method = (new PHPClassMethod('__construct', [], Stringable::class, 'protected', null))->throws([
        RuntimeException::class
    ])->addParam(new PHPFunctionParameter('users', null, ["user1" => "Sandra", "user2" => "Emily"]))
        ->addParam((new PHPFunctionParameter('params', PHPFunctionParameter::class))->asOptional())
        ->addContents(
            <<<EOT
\$this->name_ = \$users;
\$this->params_ = \$params;
\$this->description_ = \$description;
\$this->accessModifier_ = \$modifier;
\$this->returns_ = \$returns;
EOT
        )->addComment('This is a PHP Class method');
    return $method->__toString();
}

function create_php_class()
{
    $class_ = (new PHPClass("Person", [
        "\\App\\Contracts\\PersonInterface",
        "\\App\\Contracts\\HumanInterface"
    ], [
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
        ->asFinal()
        ->addProperty(new PHPClassProperty('firstname', 'string', 'private', null, 'Person first name'))
        ->addConstant(new PHPClassProperty('lastname', 'string', 'private', null, 'Person last name'))
        ->addToNamespace("App\\Models");

    return $class_->__toString();
}

function create_php_traits()
{
    $class_ = (new PHPTrait(
        "HasValidatableAttributes",
        [
            (new PHPClassMethod('__construct', [
                new PHPFunctionParameter('firstname', 'string'),
                new PHPFunctionParameter('lastname', 'string')
            ], null, 'public', 'Class initializer')),
            (new PHPClassMethod('setFirstName', [
                new PHPFunctionParameter('firstname', 'string')
            ], "self", 'public', 'firstname property setter')),
            (new PHPClassMethod('setParent', [
                new PHPFunctionParameter('person',  "\\App\\Person\\Contracts\\PersonInterface")
            ], "self", 'public', 'parent property setter')),
            (new PHPClassMethod('getFirstName', [], "string", 'public', 'firstname property getter')),
        ]
    ))
    ->addTrait('\\App\\Person\\Traits\\PersonInterface')->addToNamespace("App\\Models")
    ->addProperty(new PHPClassProperty('firstname', 'string', 'private', null, 'Person first name'))
    ->addConstant(new PHPClassProperty('lastname', 'string', 'private', null, 'Person last name'));

    return $class_->__toString();
}

function create_php_interfaces()
{
    $class_ = (new PHPInterface(
        "ConsoleWriter",
        [
            (new PHPClassMethod('setFirstName', [
                new PHPFunctionParameter('firstname', 'string')
            ], "self", 'public', 'firstname property setter')),
            (new PHPClassMethod('setParent', [
                new PHPFunctionParameter('person',  "\\App\\Person\\Contracts\\PersonInterface")
            ], "self", 'public', 'parent property setter')),
            (new PHPClassMethod('getFirstName', [], "string", 'public', 'firstname property getter')),
        ],
        [
            new PHPClassProperty('firstname', 'string', 'private', null, 'Person first name'),
            new PHPClassProperty('lastname', 'string', 'private', null, 'Person last name')
        ]
    ))->addToNamespace("App\\Models");

    return $class_->__toString();
}

// echo create_comments([
//     "List of grocery items",
//     "@var string[]"
// ], true) . PHP_EOL;

// echo create_comments("This is a single line comment", false) . PHP_EOL;

// echo create_php_class_property('name', null, 'private', null, 'Person first name') . PHP_EOL;
// echo create_php_class_property_with_methods('x', 10, PHPTypesModifiers::PRIVATE, 'X coordinates') . PHP_EOL;
// echo create_php_class_const_property_with_methods('y', 10, PHPTypesModifiers::PRIVATE, 'X coordinates') . PHP_EOL;
// echo create_php_class_property('address', null, 'public', "\\Drewlabs\\Core\\Stream::class", 'Person address') . PHP_EOL;
// echo create_php_class_property('address', 'array', 'public', [
//     "house_number" => "No 23, KEGUE",
//     "city" => "LOME",
//     "country" => "TOGO",
//     "id" => "12"
// ], null) . PHP_EOL;

// echo create_php_class_property('fillables', null, 'protected', [
//     "firstname",
//     "lastname",
//     "address"
// ], "Table fillable attributes") . PHP_EOL;

// echo create_class_method() . PHP_EOL;


echo create_php_class() . PHP_EOL;

// echo create_interface_method() . PHP_EOL;

// echo create_php_traits() . PHP_EOL;

// echo create_php_interfaces() . PHP_EOL;