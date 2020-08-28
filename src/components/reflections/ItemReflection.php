<?php
namespace extas\components\reflections;

use extas\interfaces\reflections\IItemReflection;

/**
 * Class ItemReflection
 *
 * @package extas\components\reflections
 * @author jeyroik <jeyroik@gmail.com>
 */
class ItemReflection implements IItemReflection
{
    protected string $itemClass = '';

    /**
     * ItemReflection constructor.
     * @param string $itemClass
     */
    public function __construct(string $itemClass)
    {
        $this->itemClass = $itemClass;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getTypedProperties(): array
    {
        $reflection = new \ReflectionClass($this->itemClass);
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
}
