<?php
namespace extas\interfaces\stages;

/**
 * Interface IStageGenerateJsonRpcAddBefore
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageGenerateJsonRpcAddBefore
{
    public const NAME = 'extas.generate.jsonrpc.add.before';

    /**
     * @param array $operation
     */
    public function __invoke(array &$operation): void;
}
