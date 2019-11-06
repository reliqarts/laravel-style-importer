<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Rule;

use ReliqArts\StyleImporter\CSS\Extractable;
use ReliqArts\StyleImporter\CSS\Rule as RuleContract;

final class ImportRule implements Extractable, RuleContract
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
