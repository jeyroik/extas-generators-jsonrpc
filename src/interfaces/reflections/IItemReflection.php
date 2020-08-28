<?php
namespace extas\interfaces\reflections;

/**
 * Interface IItemReflection
 *
 * @package extas\interfaces\reflections
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IItemReflection
{
    /**
     * IItemReflection constructor.
     * @param string $itemClass
     */
    public function __construct(string $itemClass);

    /**
     * @return array
     */
    public function getTypedProperties(): array;
}
