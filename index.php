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
        ->setModifier($modifier ?? PHPTypesModifiers::PUBLIC)
        ->addComment([$description, 'This is a second line comment'])
        ->asConstant()
        ->value($value);

    return $property->__toString();
}

function create_interface_method()
{
    $method = (new PHPClassMethod(
        'write',
        [
            new PHPFunctionParameter('name', 'string'),
            new PHPFunctionParameter('request', "\\Illumintae\Http\\Request"),
        ],
    ))
        ->setModifier(PHPTypesModifiers::PUBLIC)
        ->throws([
            RuntimeException::class
        ])
        ->asInterfaceMethod()
        ->setReturnType(Stringable::class);
    return $method->__toString();
}

function create_class_method()
{
    $method = (new PHPClassMethod('methodName'))->throws([
        RuntimeException::class
    ])->addParam(new PHPFunctionParameter('users', null, ["user1" => "Sandra", "user2" => "Emily"]))
        ->addParam((new PHPFunctionParameter('params', PHPFunctionParameter::class))->asOptional());
    //         ->addContents(
    //             <<<EOT
    // \$this->users_ = \$users;
    // \$this->params_ = \$params;
    // EOT
    //         )->addLine('// This is an extra line')
    $lines = [
        'try {',
        "\t// validate request inputs",
        "\t// Use your custom validation rules here",
        "\t\$validator = \$this->validator->validate([], \$request->all())",
        "\tif (\$validator->fails()) {",
        "\t\treturn \$this->response->badRequest(\$validator->errors())",
        "\t}",
        "",
        "} catch (\Exception \$e) {",
        "\t// Return failure response to request client",
        "\treturn \$this->response->error(\$e)",
        "}"
    ];
    foreach ($lines as $line) {
        # code...
        $method = $method->addLine($line);
    }
    $method->setReturnType(Stringable::class)
        ->addComment('This is a PHP Class method');
    return $method;
}

function create_php_class()
{
    $class_ = (new PHPClass("Person", [], [
        (new PHPClassMethod('__construct', [
            new PHPFunctionParameter('firstname', 'string'),
            new PHPFunctionParameter('lastname', 'string')
        ], null, 'public', 'Class initializer')),
        (new PHPClassMethod('setRequest', [
            new PHPFunctionParameter('request', 'Illuminate\\Http\\Request')
        ], "self", 'public', 'Request property setter')),
        (new PHPClassMethod('setParent', [
            new PHPFunctionParameter('person',  "App\\Person\\Contracts\\PersonInterface")
        ], "self", 'public', 'parent property setter')),
        (new PHPClassMethod('getFirstName', [], "string", 'public', 'firstname property getter')),
    ],))
    ->addClassPath("Illuminate\\Http\\Response")
    ->setBaseClass("\\App\\Core\\PersonBase")
        ->addTrait('\\App\\Person\\Traits\\PersonInterface')
        ->addImplementation("\\App\\Contracts\\PersonInterface")
        ->addImplementation("\\App\\Contracts\\HumanInterface")
        ->asFinal()
        ->addProperty(new PHPClassProperty('request', 'Illuminate\\Http\\Request', 'private', null, 'Injected request instance'))
        ->addProperty(new PHPClassProperty('fillable', 'App\\Models\\Fillable', 'private', null, 'List of addresses'))
        ->addConstant(new PHPClassProperty('parent_', 'App\\Person\\Contracts\\PersonInterface', 'private', null, 'Parent instance'))
        ->addMethod(create_class_method())
        ->addToNamespace("App\\Models");

    return $class_->__toString();
}

function create_php_traits()
{
    $class_ = (new PHPTrait(
        "HasValidatableAttributes",
        [
            (new PHPClassMethod('setFirstName', [
                new PHPFunctionParameter('firstname', 'string')
            ], "self", 'public', 'firstname property setter')),
            (new PHPClassMethod('setParent', [
                new PHPFunctionParameter('person',  "\\App\\Person\\Contracts\\PersonInterface")
            ], "self", 'public', 'parent property setter')),
            (new PHPClassMethod('getFirstName', [], "string", 'public', 'firstname property getter')),
            new PHPClassMethod('setRequest', [
                new PHPFunctionParameter('request', 'Illuminate\\Http\\Request')
            ], "self", 'public', 'Request property setter')
        ]
    ))
        ->addTrait('\\App\\Person\\Traits\\PersonInterface')
        ->addToNamespace("App\\Models")
        ->addProperty(new PHPClassProperty('request', 'Illuminate\\Http\\Request', 'private', null, 'Injected request instance'))
        ->addProperty(new PHPClassProperty('firstname', 'string', 'private', null, 'Person first name'))
        ->addMethod((new PHPClassMethod('__construct', [
            new PHPFunctionParameter('parent', '\\App\\Person\\Contracts\\PersonInterface'),
            new PHPFunctionParameter('lastname', 'string')
        ], null, 'public', 'Class initializer')));

    return $class_->__toString();
}

function create_php_interfaces()
{
    $class_ = (new PHPInterface(
        "Writer",
        [
            (new PHPClassMethod('setWriter', [
                new PHPFunctionParameter('writer', 'App\\Contracts\\Writer')
            ], "self", 'public', 'Writer property setter')),
            (new PHPClassMethod('write', [
                new PHPFunctionParameter('request', "\\Psr\Http\\ServerRequestInterface"),
            ], null, 'public', 'Write to the server request')),
            (new PHPClassMethod('read', [
                new PHPFunctionParameter('request', "\\Psr\Http\\ServerRequestInterface"),
            ], null, 'public', 'Read from the server request')),
        ]
    ))
        ->setBaseInterface('App\\Contracts\\Writer\\BufferWriter')
        ->addToNamespace("App\\Contracts\\Writer");

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

// echo create_class_method()->__toString() . PHP_EOL;


echo create_php_class() . PHP_EOL;

// echo create_interface_method() . PHP_EOL;

// echo create_php_traits() . PHP_EOL;

// echo create_php_interfaces() . PHP_EOL;
