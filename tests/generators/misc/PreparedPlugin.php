<?php
namespace tests\generators\misc;

use extas\components\plugins\Plugin;
use extas\interfaces\operations\IJsonRpcOperation;
use extas\interfaces\stages\IStageDynamicPluginsPrepared;

/**
 * Class PreparedPlugin
 *
 * @package tests\generators\misc
 * @author jeyroik <jeyroik@gmail.com>
 */
class PreparedPlugin extends Plugin implements IStageDynamicPluginsPrepared
{
    /**
     * @param IJsonRpcOperation $operation
     * @return IJsonRpcOperation
     */
    public function __invoke(IJsonRpcOperation $operation): IJsonRpcOperation
    {
        $operation['test'] = 'is ok';

        return $operation;
    }
}
