<?php
namespace extas\components\generators\jsonrpc;

use extas\components\generators\JsonRpcGenerator;
use extas\components\generators\TConstructOperations;
use extas\components\reflections\ItemReflection;
use extas\interfaces\operations\IJsonRpcOperation;

/**
 * Class ByDynamicPlugins
 *
 * @package extas\components\generators\jsonrpc
 * @author jeyroik <jeyroik@gmail.com>
 */
class ByDynamicPlugins extends JsonRpcGenerator
{
    use TConstructOperations;

    public const FIELD__ENTITY_NAME = 'entity_name';
    public const FIELD__REPOSITORY = 'repository';
    public const FIELD__ITEM_CLASS = 'item_class';

    /**
     * @param array $sourceItems
     * @return array
     * @throws \ReflectionException
     */
    protected function run(array $sourceItems): array
    {
        if (isset($sourceItems['jsonrpc__dynamic_plugins'])) {
            return $this->generate($sourceItems['jsonrpc__dynamic_plugins']);
        } else {
            $this->infoLn(['[generator][by dynamic plugins] Missed data']);
        }

        return [];
    }

    /**
     * @param array $plugins
     * @return array
     * @throws \ReflectionException
     */
    public function generate(array $plugins): array
    {
        foreach ($plugins as $plugin) {
            $this->filterPlugin($plugin);
        }

        return $this->result;
    }

    /**
     * @param array $plugin
     * @return bool
     * @throws \ReflectionException
     */
    protected function filterPlugin(array $plugin): bool
    {
        if (!$this->isValidPlugin($plugin)) {
            return false;
        }

        $reflection = new ItemReflection($plugin[static::FIELD__ITEM_CLASS]);
        $props = $reflection->getTypedProperties();
        $methods = ['create', 'index', 'update', 'delete'];

        foreach ($methods as $method) {
            $this->addOperation($this->prepareOperationData($method, $plugin, $props));
        }

        return true;
    }

    /**
     * @param string $method
     * @param array $plugin
     * @param array $props
     * @return array
     */
    protected function prepareOperationData(string $method, array $plugin, array $props): array
    {
        $methodConstruct = 'construct' . ucfirst($method);
        $name = str_replace(' ', '.', $plugin[static::FIELD__ENTITY_NAME]);
        $title = ucfirst($method) . ' ' . $plugin[static::FIELD__ENTITY_NAME];

        /**
         * @var IJsonRpcOperation $operation
         */
        $operation = $this->$methodConstruct($name, $props);
        $operation->setParameterValue(
                IJsonRpcOperation::PARAM__ITEM_CLASS,
                $plugin[static::FIELD__ITEM_CLASS]
            )
            ->setParameterValue(
                IJsonRpcOperation::PARAM__ITEM_REPOSITORY,
                $plugin[static::FIELD__REPOSITORY]
            )
            ->setTitle($title)
            ->setDescription($title);

        return $operation->__toArray();
    }

    /**
     * @param array $plugin
     * @return bool
     */
    protected function isValidPlugin(array $plugin): bool
    {
        if (empty($plugin)) {
            return false;
        }

        if (!isset(
            $plugin[static::FIELD__ENTITY_NAME],
            $plugin[static::FIELD__REPOSITORY],
            $plugin[static::FIELD__ITEM_CLASS]
        )) {
            $this->writeLn(['Incorrect data for dynamic plugin detected: ' . print_r($plugin, true)]);
            return false;
        }

        return true;
    }
}
