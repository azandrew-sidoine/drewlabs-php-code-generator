<?php

require __DIR__ . '/vendor/autoload.php';

use Drewlabs\CodeGenerator\CommentModelFactory;
use Drewlabs\CodeGenerator\Contracts\Stringable;
use Drewlabs\CodeGenerator\Models\MethodParam;
use Drewlabs\CodeGenerator\Models\PHPClass;
use Drewlabs\CodeGenerator\Models\PHPClassMethod;
use Drewlabs\CodeGenerator\Models\PHPClassProperty;

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

function create_class_method()
{
    $method = (new PHPClassMethod('__construct', [
        new MethodParam('name', 'string'),
        new MethodParam('params', MethodParam::class)
    ], Stringable::class, 'protected', null))->throws([
        RuntimeException::class
    ])->withConents(
        <<<EOT
\$this->name_ = \$name;
\$this->params_ = \$params;
\$this->description_ = \$description;
\$this->accessModifier_ = \$modifier;
\$this->returns_ = \$returns;
EOT
    );
    return $method->__toString();
}

function create_php_class()
{
    $class_ = new PHPClass("Person", [
        "\\App\\Contracts\\PersonInterface",
    ], [
        (new PHPClassMethod('__construct', [
            new MethodParam('firstname', 'string'),
            new MethodParam('lastname', 'string')
        ], null, 'public', 'Class initializer')),
        (new PHPClassMethod('setFirstName', [
            new MethodParam('firstname', 'string')
        ], "self", 'public', 'firstname property setter')),
        (new PHPClassMethod('getFirstName', [], "string", 'public', 'firstname property getter')),
    ], [
        new PHPClassProperty('firstname', 'string', 'private', null, 'Person first name'),
        new PHPClassProperty('lastname', 'string', 'private', null, 'Person last name')
    ]);

    return $class_->__toString();
}

// echo create_comments([
//     "List of grocery items",
//     "@var string[]"
// ], true) . PHP_EOL;

// echo create_comments("This is a single line comment", false) . PHP_EOL;

// echo create_php_class_property('name', 'string', 'private', null, 'Person first name') . PHP_EOL;
// echo create_php_class_property('address', '\stdClass', 'public', "new \stdClass()", 'Person address') . PHP_EOL;
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