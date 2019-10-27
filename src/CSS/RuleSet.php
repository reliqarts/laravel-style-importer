<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS;

interface RuleSet
{
    /**
     * @return string
     */
    public function __toString(): string;
}
