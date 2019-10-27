<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS;

use ReliqArts\StyleImporter\CSS\Exception\RuleExtractionFailed;

interface RuleExtractor
{
    /**
     * @param string   $styles
     * @param string[] $htmlElements
     *
     * @throws RuleExtractionFailed
     *
     * @return RuleSet
     */
    public function extractRules(string $styles, array $htmlElements): RuleSet;
}
