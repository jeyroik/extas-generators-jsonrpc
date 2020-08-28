<?php
namespace tests\generators\misc;

use extas\components\Item;
use extas\components\THasName;
use extas\components\THasValue;
use extas\interfaces\IHasName;
use extas\interfaces\IHasValue;

/**
 * Class SomeEntity
 *
 * @package tests\generators\misc
 * @author jeyroik <jeyroik@gmail.com>
 */
class SomeEntity extends Item implements IHasName, IHasValue
{
    use THasName;
    use THasValue;

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'some.entity';
    }
}
