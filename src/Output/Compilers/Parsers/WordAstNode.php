<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/console/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Aphiria\Console\Output\Compilers\Parsers;

/**
 * Defines a word node
 */
final class WordAstNode extends AstNode
{
    /**
     * @inheritdoc
     */
    public function isTag(): bool
    {
        return false;
    }
}
