<?php
namespace extas\interfaces\stages;

use extas\interfaces\operations\IJsonRpcOperation;

/**
 * Interface IStageDynamicPluginsPrepared
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageDynamicPluginsPrepared
{
    public const NAME = 'extas.dynamic.plugins.prepared';

    /**
     * @param IJsonRpcOperation $operation
     * @return IJsonRpcOperation
     */
    public function __invoke(IJsonRpcOperation $operation): IJsonRpcOperation;
}
