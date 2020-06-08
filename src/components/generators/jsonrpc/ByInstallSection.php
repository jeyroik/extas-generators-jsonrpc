<?php
namespace extas\components\generators\jsonrpc;

use extas\components\generators\JsonRpcGenerator;
use extas\interfaces\operations\IJsonRpcOperation;
use extas\interfaces\stages\IStageInstallSection;

/**
 * Class ByInstallSection
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class ByInstallSection extends JsonRpcGenerator
{
    public const NAME = 'by.install.section';

    protected IStageInstallSection $currentPlugin;
    protected array $currentProperties;

    /**
     * @var \ReflectionProperty[]
     */
    protected array $currentPluginProperties = [];

    /**
     * @param array $sourceItems
     * @return array
     * @throws \Exception
     */
    protected function run(array $sourceItems): array
    {
        if (isset($sourceItems['by.install.sections'])) {
            return $this->generate($sourceItems['by.install.sections']);
        }

        return [];
    }

    /**
     * @param IStageInstallSection[] $plugins
     * @return array
     * @throws \Exception
     */
    public function generate(array $plugins): array
    {
        foreach ($plugins as $plugin) {
            $this->grabCurrentPluginProperties($plugin);
            $properties = $this->generateProperties($plugin);
            $parts = explode(' ', $this->getCurrentPluginProperty('selfName'));
            $fullName = implode('.', $parts);
            $dotted = $this->getOnlyEdge() ? array_pop($parts) : $fullName;
            $this->appendCRUDOperations($fullName, $plugin, $dotted, $properties);
        }

        return $this->result;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    protected function getCurrentPluginProperty(string $name)
    {
        if (!isset($this->currentPluginProperties[$name])) {
            throw new \Exception('Missed current plugin property "' . $name . '"');
        }

        return $this->currentPluginProperties[$name];
    }

    /**
     * @param IStageInstallSection $plugin
     * @throws \ReflectionException
     */
    protected function grabCurrentPluginProperties(IStageInstallSection $plugin): void
    {
        $pluginReflection = new \ReflectionClass($plugin);
        $this->currentPluginProperties = $pluginReflection->getDefaultProperties();
    }

    /**
     * @param string $fullName
     * @param $plugin
     * @param $dotted
     * @param $properties
     * @throws
     */
    protected function appendCRUDOperations(string $fullName, $plugin, $dotted, $properties): void
    {
        $reflection = new \ReflectionClass($this->getCurrentPluginProperty('selfItemClass'));
        $methods = $this->grabMethodsFromComments($reflection);
        $methods = empty($methods) ? ['create', 'index', 'update', 'delete'] : $methods;

        if ($this->isApplicableOperation($fullName)) {
            $this->currentPlugin = $plugin;
            $this->currentProperties = $properties;
            foreach ($methods as $method) {
                $methodConstruct = 'construct' . ucfirst($method);
                $this->addOperation($this->$methodConstruct($dotted));
            }
        }
    }

    /**
     * @param \ReflectionClass $reflection
     * @return array
     */
    protected function grabMethodsFromComments(\ReflectionClass $reflection): array
    {
        $comment = $reflection->getDocComment();
        preg_match_all('/@jsonrpc_method\s(\S+)/', $comment, $matches);

        return empty($matches[1]) ? [] : $matches[1];
    }

    /**
     * @param IStageInstallSection $plugin
     *
     * @return array
     * @throws
     */
    protected function generateProperties(IStageInstallSection $plugin): array
    {
        $itemClass = $this->getCurrentPluginProperty('selfItemClass');
        $reflection = new \ReflectionClass($itemClass);
        $properties = $this->grabPropertiesFromComments($reflection);

        if (empty($properties)) {
            $constants = $reflection->getConstants();
            $this->appendConstantsToProperties($constants, $properties);
            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            $byNameMethods = array_column($methods, null, 'name');

            $this->fillInPropertySpec($byNameMethods, $properties);
        }

        return $properties;
    }

    /**
     * @param \ReflectionClass $reflection
     * @return array
     */
    protected function grabPropertiesFromComments(\ReflectionClass $reflection): array
    {
        $comment = $reflection->getDocComment();
        preg_match_all('/@jsonrpc_field\s(\S+):(\S+)/', $comment, $matches);
        $properties = [];

        if (!empty($matches[1])) {
            foreach ($matches[1] as $index => $propertyName) {
                $properties[$propertyName] = ['type' => $matches[2][$index]];
            }
        }

        return $properties;
    }

    /**
     * @param string $property
     * @param array $byNameMethods
     * @return string
     */
    protected function generatePropertyType(string $property, array $byNameMethods): string
    {
        $methodName = 'get' . ucwords(str_replace('_', ' ', $property));
        $type = 'string';
        if (isset($byNameMethods[$methodName])) {
            $returnType = $byNameMethods[$methodName]->getReturnType();
            $type = $returnType ? $returnType->getName() : 'string';
        }

        return $type;
    }

    /**
     * @param array $byNameMethods
     * @param array $properties
     */
    protected function fillInPropertySpec(array $byNameMethods, array &$properties): void
    {
        foreach ($properties as $property => $spec) {
            $properties[$property] = ['type' => $this->generatePropertyType($property, $byNameMethods)];
        }
    }

    /**
     * @param array $constants
     * @param array $properties
     */
    protected function appendConstantsToProperties(array $constants, array &$properties): void
    {
        foreach ($constants as $name => $value) {
            if (strpos($name, 'FIELD') !== false) {
                $properties[$value] = [];
            }
        }
    }

    /**
     * @param string $name
     * @return array
     * @throws \Exception
     */
    protected function constructCreate(string $name)
    {
        return $this->constructCRUDOperation(
            'create',
            $name,
            'extas\components\jsonrpc\operations\Create'
        );
    }

    /**
     * @param string $name
     * @return array
     * @throws \Exception
     */
    protected function constructIndex(string $name)
    {
        return $this->constructCRUDOperation(
            'index',
            $name,
            'extas\components\jsonrpc\operations\Index',
                [
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
                            "properties" => $this->currentProperties
                        ],
                        "total" => [
                            "type" => "number"
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @param string $name
     * @return array
     * @throws \Exception
     */
    protected function constructUpdate(string $name)
    {
        return $this->constructCRUDOperation(
            'update',
            $name,
            'extas\components\jsonrpc\operations\Update'
        );
    }

    /**
     * @param string $name
     * @return array
     * @throws \Exception
     */
    protected function constructDelete(string $name)
    {
        return $this->constructCRUDOperation(
            'delete',
            $name,
            'extas\components\jsonrpc\operations\Delete'
        );
    }

    /**
     * @param string $crudName
     * @param string $operationName
     * @param string $operationClass
     * @param array $specs
     * @return array
     * @throws \Exception
     */
    protected function constructCRUDOperation(
        string $crudName,
        string $operationName,
        string $operationClass,
        array $specs = []
    ): array
    {
        $specs = $specs ?: [
            "request" => [
                "type" => "object",
                "properties" => [
                    "data" => [
                        "type" => "object",
                        "properties" => $this->currentProperties
                    ]
                ]
            ],
            "response" => ["type" => "object", "properties" => $this->currentProperties]
        ];

        return [
            IJsonRpcOperation::FIELD__NAME => $operationName . '.' . $crudName,
            IJsonRpcOperation::FIELD__TITLE => $this->getHighName($crudName),
            IJsonRpcOperation::FIELD__DESCRIPTION => $this->getHighName($crudName),
            IJsonRpcOperation::FIELD__PARAMETERS => [
                IJsonRpcOperation::PARAM__ITEM_NAME => [
                    'name' => IJsonRpcOperation::PARAM__ITEM_NAME,
                    'value' => $operationName
                ],
                IJsonRpcOperation::PARAM__ITEM_CLASS => [
                    'name' => IJsonRpcOperation::PARAM__ITEM_CLASS,
                    'value' => $this->getCurrentPluginProperty('selfItemClass')
                ],
                IJsonRpcOperation::PARAM__ITEM_REPOSITORY => [
                    'name' => IJsonRpcOperation::PARAM__ITEM_REPOSITORY,
                    'value' => $this->getCurrentPluginProperty('selfRepositoryClass')
                ],
                IJsonRpcOperation::PARAM__METHOD => [
                    'name' => IJsonRpcOperation::PARAM__METHOD,
                    'value' => $crudName
                ]
            ],
            IJsonRpcOperation::FIELD__CLASS => $operationClass,
            IJsonRpcOperation::FIELD__SPECS => $specs
        ];
    }

    /**
     * @param string $crudName
     * @return string
     * @throws \Exception
     */
    protected function getHighName(string $crudName): string
    {
        return ucfirst($crudName) . ' ' . $this->getCurrentPluginProperty('selfName');
    }
}
