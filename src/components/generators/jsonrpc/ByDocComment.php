<?php
namespace extas\components\generators\jsonrpc;

use extas\interfaces\operations\IJsonRpcOperation;

/**
 * Class ByDocComment
 *
 * @package extas\components\generators\jsonrpc
 * @author jeyroik <jeyroik@gmail.com>
 */
class ByDocComment extends JsonRpcGenerator
{
    protected string $docComment = '';

    /**
     * @param array $sourceItems
     * @return array
     * @throws \ReflectionException
     */
    protected function run(array $sourceItems): array
    {
        if (isset($sourceItems['by.doc.comment'])) {
            return $this->generate($sourceItems['by.doc.comment']);
        }

        return [];
    }

    /**
     * @param array $operations
     * @return array
     * @throws \ReflectionException
     */
    public function generate(array $operations): array
    {
        foreach ($operations as $operation) {
            $reflection = new \ReflectionClass($operation);
            $this->docComment = $reflection->getDocComment();
            if ($this->isApplicableOperation($this->getOperationName())) {
                $this->addOperation($this->buildOperation($operation));
            }
        }

        return $this->result;
    }

    /**
     * @param $operation
     * @return array
     */
    protected function buildOperation($operation): array
    {
        return [
            IJsonRpcOperation::FIELD__NAME => $this->getOperationName(),
            IJsonRpcOperation::FIELD__TITLE => $this->getOperationTitle(),
            IJsonRpcOperation::FIELD__DESCRIPTION => $this->getOperationDescription(),
            IJsonRpcOperation::FIELD__CLASS => get_class($operation),
            IJsonRpcOperation::FIELD__PARAMETERS => [],
            IJsonRpcOperation::FIELD__SPECS => [
                "request" => ["type" => "object", "properties" => $this->getRequestProperties()],
                "response" => ["type" => "object", "properties" => $this->getResponseProperties()]
            ]
        ];
    }

    /**
     * @return string
     */
    protected function getOperationName(): string
    {
        return $this->oneByPattern('name');
    }

    /**
     * @return string
     */
    protected function getOperationTitle(): string
    {
        return $this->oneByPattern('title');
    }

    /**
     * @return string
     */
    protected function getOperationDescription(): string
    {
        return $this->oneByPattern('description');
    }

    /**
     * @return array
     */
    protected function getRequestProperties(): array
    {
        return $this->getProperties('request');
    }

    /**
     * @return array
     */
    protected function getResponseProperties(): array
    {
        return $this->getProperties('response');
    }

    /**
     * @param string $prefix
     * @return array
     */
    protected function getProperties(string $prefix): array
    {
        $fields = $this->allByPattern($prefix . '_field');
        $properties = [];
        foreach ($fields as $field) {
            list($propertyName, $propertyType) = explode(':', $field);
            $properties[$propertyName] = ['type' => $propertyType];
        }

        return $properties;
    }

    /**
     * @param string $subject
     * @return string
     */
    protected function oneByPattern(string $subject): string
    {
        preg_match_all('/@jsonrpc_' . $subject . '\s(.*)/', $this->docComment, $matches);

        $result = '';
        if (!empty($matches[0])) {
            $result = array_shift($matches[1]);
        }

        return $result;
    }

    /**
     * @param string $subject
     * @return array
     */
    protected function allByPattern(string $subject): array
    {
        preg_match_all('/@jsonrpc_' . $subject . '\s(.*)/', $this->docComment, $matches);

        $result = [];
        if (!empty($matches[0])) {
            $result = $matches[1];
        }

        return $result;
    }
}
