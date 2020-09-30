<?php
namespace extas\components\generators;

use extas\components\operations\JsonRpcOperation;
use extas\interfaces\operations\IJsonRpcOperation;

/**
 * Trait TConstructOperations
 *
 * @package extas\components\generators
 * @author jeyroik <jeyroik@gmail.com>
 */
trait TConstructOperations
{
    /**
     * @param string $name
     * @param array $properties
     * @return IJsonRpcOperation
     * @throws \Exception
     */
    protected function constructCreate(string $name, array $properties): IJsonRpcOperation
    {
        $skeleton = $this->fillInNames($this->getOperationSkeleton(), 'create', $name);
        $skeleton->setSpecs($this->constructSpecs($properties));

        return $skeleton;
    }

    /**
     * @param string $name
     * @param array $properties
     * @return IJsonRpcOperation
     * @throws \Exception
     */
    protected function constructIndex(string $name, array $properties): IJsonRpcOperation
    {
        $skeleton = $this->fillInNames($this->getOperationSkeleton(), 'index', $name);
        $skeleton->setSpecs([
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
                            "properties" => $properties
                        ],
                        "total" => [
                            "type" => "number"
                        ]
                    ]
                ]
            ]
        );

        return $skeleton;
    }

    /**
     * @param string $name
     * @param array $properties
     * @return IJsonRpcOperation
     * @throws \Exception
     */
    protected function constructUpdate(string $name, array $properties): IJsonRpcOperation
    {
        $skeleton = $this->fillInNames($this->getOperationSkeleton(), 'update', $name);
        $skeleton->setSpecs($this->constructSpecs($properties));

        return $skeleton;
    }

    /**
     * @param string $name
     * @param array $properties
     * @return IJsonRpcOperation
     * @throws \Exception
     */
    protected function constructDelete(string $name, array $properties): IJsonRpcOperation
    {
        $skeleton = $this->fillInNames($this->getOperationSkeleton(), 'delete', $name);
        $skeleton->setSpecs($this->constructSpecs($properties));

        return $skeleton;
    }

    /**
     * @param array $properties
     * @param array $specs
     * @return array|array[]
     */
    protected function constructSpecs(array $properties, array $specs = []): array
    {
        return $specs ?: [
            "request" => [
                "type" => "object",
                "properties" => [
                    "data" => [
                        "type" => "object",
                        "properties" => $properties
                    ]
                ]
            ],
            "response" => ["type" => "object", "properties" => $properties]
        ];
    }

    /**
     * @param IJsonRpcOperation $skeleton
     * @param string $crudName
     * @param string $itemName
     * @return IJsonRpcOperation
     * @throws \Exception
     */
    protected function fillInNames(IJsonRpcOperation $skeleton, string $crudName, string $itemName): IJsonRpcOperation
    {
        $skeleton->setName($itemName . '.' . $crudName)
            ->setParameterValue($skeleton::PARAM__ITEM_NAME, $itemName)
            ->setParameterValue($skeleton::PARAM__METHOD, $crudName)
            ->setClass('extas\components\operations\jsonrpc\\' . ucfirst($crudName))
        ;

        return $skeleton;
    }

    /**
     * @return IJsonRpcOperation
     */
    protected function getOperationSkeleton(): IJsonRpcOperation
    {
        return new JsonRpcOperation([
            IJsonRpcOperation::FIELD__NAME => '',
            IJsonRpcOperation::FIELD__TITLE => '',
            IJsonRpcOperation::FIELD__DESCRIPTION => '',
            IJsonRpcOperation::FIELD__PARAMETERS => [
                IJsonRpcOperation::PARAM__ITEM_NAME => [
                    'name' => IJsonRpcOperation::PARAM__ITEM_NAME,
                    'value' => ''
                ],
                IJsonRpcOperation::PARAM__ITEM_CLASS => [
                    'name' => IJsonRpcOperation::PARAM__ITEM_CLASS,
                    'value' => ''
                ],
                IJsonRpcOperation::PARAM__ITEM_REPOSITORY => [
                    'name' => IJsonRpcOperation::PARAM__ITEM_REPOSITORY,
                    'value' => ''
                ],
                IJsonRpcOperation::PARAM__METHOD => [
                    'name' => IJsonRpcOperation::PARAM__METHOD,
                    'value' => ''
                ]
            ],
            IJsonRpcOperation::FIELD__CLASS => '',
            IJsonRpcOperation::FIELD__SPECS => []
        ]);
    }
}
