<?php

/*
 * Opulence
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/console/blob/master/LICENSE.md
 */

namespace Aphiria\Console\Responses\Compilers;

use Aphiria\Console\Responses\Compilers\Elements\Style;
use Aphiria\Console\Responses\Compilers\Lexers\ILexer;
use Aphiria\Console\Responses\Compilers\Parsers\IParser;
use Aphiria\Console\Responses\Compilers\Parsers\Nodes\Node;
use InvalidArgumentException;
use RuntimeException;

/**
 * Defines an element compiler
 */
class Compiler implements ICompiler
{
    /** @var ILexer The lexer to use */
    private $lexer;
    /** @var IParser The parser to use */
    private $parser;
    /** @var Style[] The list of elements registered to the compiler */
    private $elements = [];
    /** @var bool Whether or not messages should be styled */
    private $isStyled = true;

    /**
     * @param ILexer $lexer The lexer to use
     * @param IParser $parser The parser to use
     */
    public function __construct(ILexer $lexer, IParser $parser)
    {
        $this->lexer = $lexer;
        $this->parser = $parser;
        // Register built-in elements
        (new ElementRegistrant())->registerElements($this);
    }

    /**
     * @inheritdoc
     */
    public function compile(string $message): string
    {
        if (!$this->isStyled) {
            return strip_tags($message);
        }

        try {
            $tokens = $this->lexer->lex($message);
            $ast = $this->parser->parse($tokens);

            return $this->compileNode($ast->getRootNode());
        } catch (InvalidArgumentException $ex) {
            throw new RuntimeException('Failed to compile console response', 0, $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function registerElement(string $name, Style $style): void
    {
        $this->elements[$name] = $style;
    }

    /**
     * @inheritdoc
     */
    public function setStyled(bool $isStyled): void
    {
        $this->isStyled = $isStyled;
    }

    /**
     * Recursively compiles a node and its children
     *
     * @param Node $node The node to compile
     * @return string The compiled node
     * @throws RuntimeException Thrown if there was an error compiling the node
     * @throws InvalidArgumentException Thrown if there is no matching element for a particular tag
     */
    private function compileNode(Node $node): string
    {
        if ($node->isLeaf()) {
            // Don't compile a leaf that is a tag because that means it doesn't have any content
            if ($node->isTag()) {
                return '';
            }

            return $node->getValue() ?: '';
        }

        $output = '';

        foreach ($node->getChildren() as $childNode) {
            if ($node->isTag()) {
                if (!isset($this->elements[$node->getValue()])) {
                    throw new InvalidArgumentException("No style registered for element \"{$node->getValue()}\"");
                }

                $style = $this->elements[$node->getValue()];
                $output .= $style->format($this->compileNode($childNode));
            } else {
                $output .= $this->compileNode($childNode);
            }
        }

        return $output;
    }
}
