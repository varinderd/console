<?php

/*
 * Opulence
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/console/blob/master/LICENSE.md
 */

namespace Aphiria\Console\Responses;

use Aphiria\Console\Responses\Compilers\ICompiler;
use InvalidArgumentException;

/**
 * Defines the stream response
 */
class StreamResponse extends Response
{
    /** @var resource The output stream */
    protected $stream;

    /**
     * @param resource $stream The stream to write to
     * @param ICompiler $compiler The response compiler to use
     * @throws InvalidArgumentException Thrown if the stream is not a resource
     */
    public function __construct($stream, ICompiler $compiler)
    {
        if (!is_resource($stream)) {
            throw new InvalidArgumentException('The stream must be a resource');
        }

        parent::__construct($compiler);

        $this->stream = $stream;
    }

    /**
     * @inheritdoc
     */
    public function clear(): void
    {
        // Don't do anything
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @inheritdoc
     */
    protected function doWrite(string $message, bool $includeNewLine): void
    {
        fwrite($this->stream, $message . ($includeNewLine ? PHP_EOL : ''));
        fflush($this->stream);
    }
}
