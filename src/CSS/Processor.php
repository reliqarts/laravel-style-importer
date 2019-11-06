<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS;

interface Processor
{
    /**
     * @param string $styles
     * @param string ...$htmlElements
     *
     * @return Ruleset
     */
    public function getStyles(string $styles, string ...$htmlElements): Ruleset;
}
