<?php
namespace extas\components\generators\jsonrpc;

use extas\components\generators\GeneratorDispatcher;
use extas\components\THasIO;
use extas\interfaces\generators\IGeneratorDispatcher;
use extas\interfaces\stages\IStageGenerateJsonRpcAddBefore;

/**
 * Class JsonRpcGenerator
 *
 * @package extas\components\generators\jsonrpc
 * @author jeyroik@gmail.com
 */
abstract class JsonRpcGenerator extends GeneratorDispatcher implements IGeneratorDispatcher
{
    use THasIO;

    public const FIELD__OPERATIONS = 'jsonrpc_operations';

    protected array $result = [
        'name' => '[ auto-generated ]',
        self::FIELD__OPERATIONS => []
    ];

    /**
     * @return bool
     */
    public function getOnlyEdge(): bool
    {
        return (bool) $this->getInput()->getOption('only-edge');
    }

    /**
     * @return string
     */
    public function getFilter(): string
    {
        return $this->getInput()->getOption('filter');
    }

    /**
     * @param string $fullName
     * @return bool
     */
    protected function isApplicableOperation(string $fullName): bool
    {
        $filter = $this->getFilter();

        if($filter && (strpos($fullName, $filter) === false)) {
            return false;
        }

        return true;
    }

    /**
     * @param array $operation
     */
    protected function addOperation(array $operation): void
    {
        foreach ($this->getPluginsByStage(IStageGenerateJsonRpcAddBefore::NAME) as $plugin) {
            /**
             * @var IStageGenerateJsonRpcAddBefore $plugin
             */
            $plugin($operation);
        }

        $this->result[static::FIELD__OPERATIONS][] = $operation;
    }
}
