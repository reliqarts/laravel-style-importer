<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Rule;

use ReliqArts\StyleImporter\CSS\Extractable;

abstract class AtRule implements Extractable
{
    /**
     * @var string
     */
    private $raw;

    /**
     * ImportRule constructor.
     *
     * @param string $raw
     */
    public function __construct(string $raw)
    {
        $this->raw = $raw;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->raw;
    }
}
