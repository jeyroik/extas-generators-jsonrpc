<?php

use extas\interfaces\operations\IJsonRpcOperation as IOperation;

return [
    [
        IOperation::FIELD__NAME => 'test',
        IOperation::FIELD__TITLE => 'Test',
        IOperation::FIELD__DESCRIPTION => 'This is operation for tests only',
        IOperation::FIELD__PARAMETERS => [],
        IOperation::FIELD__CLASS => \tests\generators\misc\OperationWithDocComment::class,
        IOperation::FIELD__SPECS => [
            "request" => [
                "type" => "object",
                "properties" => [
                    "id" => ["type" => "string"],
                    "name" => ["type" => "string"]
                ]
            ],
            "response" => [
                "type" => "object",
                "properties" => [
                    "id" => ["type" => "int"],
                    "parameters" => ["type" => "array"]
                ]
            ]
        ]
    ]
];
