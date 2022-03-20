<?php

require __DIR__ . '/vendor/autoload.php';

use Drewlabs\CodeGenerator\CommentModelFactory;
use Drewlabs\CodeGenerator\Contracts\Stringable;
use Drewlabs\CodeGenerator\Models\PHPFunctionParameter;
use Drewlabs\CodeGenerator\Types\PHPTypesModifiers;

use function Drewlabs\CodeGenerator\Proxy\PHPClass;
use function Drewlabs\CodeGenerator\Proxy\PHPClassMethod;
use function Drewlabs\CodeGenerator\Proxy\PHPClassProperty;
use function Drewlabs\CodeGenerator\Proxy\PHPFunctionParameter;
use function Drewlabs\CodeGenerator\Proxy\PHPInterface;
use function Drewlabs\CodeGenerator\Proxy\PHPTrait;
use function Drewlabs\CodeGenerator\Proxy\PHPVariable;

function create_comments($params, $multiline = false)
{
    $comment = (new CommentModelFactory($multiline))->make($params);
    return $comment->__toString();
}

function create_php_variable(string $name, $type, $value = null, $description = '')
{
    $property = PHPVariable($name, $type, $value, $description)->asConstant();

    return $property->__toString();
}

function create_php_class_property(string $name, $type, $modifier = 'public', $value = null, $description = '')
{
    $property = PHPClassProperty($name, $type, $modifier, $value, $description);

    return $property->__toString();
}

function create_php_class_property_with_methods(string $name, $value, $modifier = 'public', $description = '')
{
    $property = (PHPClassProperty($name))
        ->setModifier($modifier ?? PHPTypesModifiers::PUBLIC)
        ->addComment([$description])
        ->value($value);

    return $property->__toString();
}
function create_php_class_const_property_with_methods(string $name, $value, $modifier = 'public', $description = '')
{
    $property = (PHPClassProperty($name))
        ->setModifier($modifier ?? PHPTypesModifiers::PUBLIC)
        ->addComment([$description, 'This is a second line comment'])
        ->asConstant()
        ->value($value);

    return $property->__toString();
}

function create_interface_method()
{
    $method = (PHPClassMethod(
        'write',
        [
            PHPFunctionParameter('name', 'string'),
            PHPFunctionParameter('request', "\\Illumintae\Http\\Request"),
        ],
    ))
        ->setModifier(PHPTypesModifiers::PUBLIC)
        ->throws([
            RuntimeException::class
        ])
        ->asCallableSignature()
        ->setReturnType(Stringable::class);
    return $method->__toString();
}

function create_class_method()
{
    $method = (PHPClassMethod('methodName'))->throws([
        RuntimeException::class
    ])
        ->addParameter(PHPFunctionParameter('users', null, [])->asVariadic())
        ->addParameter((PHPFunctionParameter('params', PHPFunctionParameter::class))->asOptional())
        ->addParameter((PHPFunctionParameter('required', 'bool')->asReference()))
        ->addParameter((PHPFunctionParameter('optional', 'bool', false)->asOptional()));
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
    $class_ = (PHPClass("Person", [], [
        (PHPClassMethod('setRequest', [
            PHPFunctionParameter('request', 'Illuminate\\Http\\Request')
        ], "self", 'public', 'Request property setter')),
        (PHPClassMethod('setParent', [
            PHPFunctionParameter('person',  "App\\Person\\Contracts\\PersonInterface")
        ], "self", 'public', 'parent property setter')),
        (PHPClassMethod('getFirstName', [], "string", 'public', 'firstname property getter')),
    ],))
        ->addComment(
            [
                'Class defining a given person instance'
            ]
        )
        ->asInvokable()
        ->asStringable()
        ->addConstructor([
            PHPFunctionParameter('firstname', 'string'),
            PHPFunctionParameter('lastname', 'string')
        ],
        [
            '$this->firstname = $firstname',
            '$this->lastname = $lastname',
        ])
        ->addClassPath("Illuminate\\Http\\Response")
        ->addFunctionPath("\\Drewlabs\\CodeGenerator\\Proxy\\PHPTrait")
        ->setBaseClass("\\App\\Core\\PersonBase")
        ->addTrait('\\App\\Person\\Traits\\PersonInterface')
        ->addImplementation("\\App\\Contracts\\PersonInterface")
        ->addImplementation("\\App\\Contracts\\HumanInterface")
        ->asFinal()
        ->addProperty(PHPClassProperty('request', 'Illuminate\\Http\\Request', 'private', null, 'Injected request instance'))
        ->addProperty(PHPClassProperty('fillable', 'App\\Models\\Fillable', 'private', null, 'List of addresses'))
        ->addProperty(PHPClassProperty('incrementing', 'bool', PHPTypesModifiers::PUBLIC, true, 'Is the primary key incrementable'))
        ->addConstant(PHPClassProperty('parent_', 'App\\Person\\Contracts\\PersonInterface', 'private', null, 'Parent instance'))
        ->addMethod(create_class_method())
        ->addToNamespace("App\\Models");

    return $class_->__toString();
}

function create_php_traits()
{
    $class_ = (PHPTrait(
        "HasValidatableAttributes",
        [
            (PHPClassMethod('setFirstName', [
                PHPFunctionParameter('firstname', 'string')
            ], "self", 'public', 'firstname property setter')),
            (PHPClassMethod('setParent', [
                PHPFunctionParameter('person',  "\\App\\Person\\Contracts\\PersonInterface")
            ], "self", 'public', 'parent property setter')),
            (PHPClassMethod('getFirstName', [], "string", 'public', 'firstname property getter')),
            PHPClassMethod('setRequest', [
                PHPFunctionParameter('request', 'Illuminate\\Http\\Request')
            ], "self", 'public', 'Request property setter')
        ]
    ))
        ->addTrait('\\App\\Person\\Traits\\PersonInterface')
        ->addToNamespace("App\\Models")
        ->addProperty(PHPClassProperty('request', 'Illuminate\\Http\\Request', 'private', null, 'Injected request instance'))
        ->addProperty(PHPClassProperty('firstname', 'string', 'private', null, 'Person first name'))
        ->addMethod((PHPClassMethod('__construct', [
            PHPFunctionParameter('parent', '\\App\\Person\\Contracts\\PersonInterface'),
            PHPFunctionParameter('lastname', 'string')
        ], null, 'public', 'Class initializer')));

    return $class_->__toString();
}

function create_php_interfaces()
{
    $class_ = (PHPInterface(
        "Writer",
        [
            (PHPClassMethod('setWriter', [
                PHPFunctionParameter('writer', 'App\\Contracts\\Writer')
            ], "self", 'public', 'Writer property setter')),
            (PHPClassMethod('write', [
                PHPFunctionParameter('request', "\\Psr\Http\\ServerRequestInterface"),
            ], null, 'public', 'Write to the server request')),
            (PHPClassMethod('read', [
                PHPFunctionParameter('request', "\\Psr\Http\\ServerRequestInterface"),
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

// echo create_php_class_property('fillables', 'array<string>', 'protected', [
//     "firstname",
//     "lastname",
//     "address"
// ], "Table fillable attributes") . PHP_EOL;

echo create_class_method()->__toString() . PHP_EOL;


echo create_php_class() . PHP_EOL;

// echo create_interface_method() . PHP_EOL;

// // echo create_php_traits() . PHP_EOL;

// echo create_php_interfaces() . PHP_EOL;


// echo create_php_variable(
//     'fillables',
//     null,
//     [
//         "firstname",
//         "lastname",
//         "address"
//     ],
//     'This is a PHP Variable'
// ) . PHP_EOL;

// echo create_php_variable(
//     'welcome',
//     'string',
//     "Hello World!",
//     'This is a PHP Variable'
// ) . PHP_EOL;