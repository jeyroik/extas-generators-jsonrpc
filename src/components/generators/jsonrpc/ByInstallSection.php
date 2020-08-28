<?php
namespace extas\components\generators\jsonrpc;

use extas\components\generators\JsonRpcGenerator;
use extas\components\generators\TConstructOperations;
use extas\components\reflections\ItemReflection;
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
    use TConstructOperations;

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
                $this->addOperation($this->prepareOperationData($method, $dotted, $properties));
            }
        }
    }

    /**
     * @param string $method
     * @param string $dotted
     * @param array $properties
     * @return array
     * @throws \Exception
     */
    protected function prepareOperationData(string $method, string $dotted, array $properties): array
    {
        $title = ucfirst($method) . ' ' . $this->getCurrentPluginProperty('selfName');
        $methodConstruct = 'construct' . ucfirst($method);
        /**
         * @var IJsonRpcOperation $operation
         */
        $operation = $this->$methodConstruct($dotted, $properties);
        $operation->setTitle($title)
            ->setDescription($title)
            ->setParameterValue(
                IJsonRpcOperation::PARAM__ITEM_CLASS,
                $this->getCurrentPluginProperty('selfItemClass')
            )
            ->setparameterValue(
                IJsonRpcOperation::PARAM__ITEM_REPOSITORY,
                $this->getCurrentPluginProperty('selfRepositoryClass')
            );

        return $operation->__toArray();
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
        $reflection = new ItemReflection($itemClass);

        return $reflection->getTypedProperties();
    }
}
