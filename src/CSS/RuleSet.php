<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS;

interface Ruleset
{
    /**
     * @return string
     */
    public function __toString(): string;
}
