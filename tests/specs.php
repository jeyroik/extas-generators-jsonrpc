<?php

use extas\interfaces\operations\IJsonRpcOperation as IOperation;
use extas\components\items\SnuffItem;

return [
    [
        IOperation::FIELD__NAME => 'snuff.item.create',
        IOperation::FIELD__TITLE => 'Create snuff item',
        IOperation::FIELD__DESCRIPTION => 'Create snuff item',
        IOperation::FIELD__PARAMETERS => [
            IOperation::PARAM__METHOD => [
                'name' => IOperation::PARAM__METHOD,
                'value' => 'create'
            ],
            IOperation::PARAM__ITEM_NAME => [
                'name' => IOperation::PARAM__ITEM_NAME,
                'value' => 'snuff.item'
            ],
            IOperation::PARAM__ITEM_CLASS => [
                'name' => IOperation::PARAM__ITEM_CLASS,
                'value' => SnuffItem::class
            ],
            IOperation::PARAM__ITEM_REPOSITORY => [
                'name' => IOperation::PARAM__ITEM_REPOSITORY,
                'value' => 'snuffRepository'
            ],
        ],
        IOperation::FIELD__CLASS => 'extas\components\operations\jsonrpc\Create',
        IOperation::FIELD__SPECS => [
            "request" => [
                "type" => "object",
                "properties" => [
                    "data" => [
                        "type" => "object",
                        "properties" => []
                    ]
                ]
            ],
            "response" => [
                "type" => "object",
                "properties" => []
            ]
        ]
    ],
    [
        IOperation::FIELD__NAME => 'snuff.item.index',
        IOperation::FIELD__TITLE => 'Index snuff item',
        IOperation::FIELD__DESCRIPTION => 'Index snuff item',
        IOperation::FIELD__PARAMETERS => [
            IOperation::PARAM__METHOD => [
                'name' => IOperation::PARAM__METHOD,
                'value' => 'index'
            ],
            IOperation::PARAM__ITEM_NAME => [
                'name' => IOperation::PARAM__ITEM_NAME,
                'value' => 'snuff.item'
            ],
            IOperation::PARAM__ITEM_CLASS => [
                'name' => IOperation::PARAM__ITEM_CLASS,
                'value' => SnuffItem::class
            ],
            IOperation::PARAM__ITEM_REPOSITORY => [
                'name' => IOperation::PARAM__ITEM_REPOSITORY,
                'value' => 'snuffRepository'
            ],
        ],
        IOperation::FIELD__CLASS => 'extas\components\operations\jsonrpc\Index',
        IOperation::FIELD__SPECS => [
            "request" => [
                "type" => "object",
                "properties" => [
                    "limit" => [
                        "type" => "number"
                    ]
                ]
            ],
            "response" => [
                "type" => "object",
                "properties" => [
                    "items" => [
                        "type" => "object",
                        "properties" => []
                    ],
                    "total" => [
                        "type" => "number"
                    ]
                ]
            ]
        ]
    ],[
        IOperation::FIELD__NAME => 'snuff.item.update',
        IOperation::FIELD__TITLE => 'Update snuff item',
        IOperation::FIELD__DESCRIPTION => 'Update snuff item',
        IOperation::FIELD__PARAMETERS => [
            IOperation::PARAM__METHOD => [
                'name' => IOperation::PARAM__METHOD,
                'value' => 'update'
            ],
            IOperation::PARAM__ITEM_NAME => [
                'name' => IOperation::PARAM__ITEM_NAME,
                'value' => 'snuff.item'
            ],
            IOperation::PARAM__ITEM_CLASS => [
                'name' => IOperation::PARAM__ITEM_CLASS,
                'value' => SnuffItem::class
            ],
            IOperation::PARAM__ITEM_REPOSITORY => [
                'name' => IOperation::PARAM__ITEM_REPOSITORY,
                'value' => 'snuffRepository'
            ],
        ],
        IOperation::FIELD__CLASS => 'extas\components\operations\jsonrpc\Update',
        IOperation::FIELD__SPECS => [
            "request" => [
                "type" => "object",
                "properties" => [
                    "data" => [
                        "type" => "object",
                        "properties" => []
                    ]
                ]
            ],
            "response" => [
                "type" => "object",
                "properties" => []
            ]
        ]
    ],[
        IOperation::FIELD__NAME => 'snuff.item.delete',
        IOperation::FIELD__TITLE => 'Delete snuff item',
        IOperation::FIELD__DESCRIPTION => 'Delete snuff item',
        IOperation::FIELD__PARAMETERS => [
            IOperation::PARAM__METHOD => [
                'name' => IOperation::PARAM__METHOD,
                'value' => 'delete'
            ],
            IOperation::PARAM__ITEM_NAME => [
                'name' => IOperation::PARAM__ITEM_NAME,
                'value' => 'snuff.item'
            ],
            IOperation::PARAM__ITEM_CLASS => [
                'name' => IOperation::PARAM__ITEM_CLASS,
                'value' => SnuffItem::class
            ],
            IOperation::PARAM__ITEM_REPOSITORY => [
                'name' => IOperation::PARAM__ITEM_REPOSITORY,
                'value' => 'snuffRepository'
            ],
        ],
        IOperation::FIELD__CLASS => 'extas\components\operations\jsonrpc\Delete',
        IOperation::FIELD__SPECS => [
            "request" => [
                "type" => "object",
                "properties" => [
                    "data" => [
                        "type" => "object",
                        "properties" => []
                    ]
                ]
            ],
            "response" => [
                "type" => "object",
                "properties" => []
            ]
        ]
    ],
];
