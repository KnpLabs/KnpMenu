<?php

namespace Knp\Menu\Attribute;

/**
 * A reusable attribute to help configure a class method as being a menu builder.
 *
 * Using it offers no guarantee: it needs to be leveraged by a KnpMenu third-party consumer.
 *
 * Using it with the KnpMenu library only has no effect at all: wiring the menu builder into
 * the menu provider is expected to be handled by framework integrations.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class AsMenuBuilder
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}
