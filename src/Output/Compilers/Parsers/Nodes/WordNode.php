<?php

/*
 * Opulence
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/console/blob/master/LICENSE.md
 */

namespace Aphiria\Console\Output\Compilers\Parsers\Nodes;

/**
 * Defines a word node
 */
final class WordNode extends Node
{
    /**
     * @inheritdoc
     */
    public function isTag(): bool
    {
        return false;
    }
}