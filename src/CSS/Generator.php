<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS;

use ReliqArts\StyleImporter\CSS\Exception\RulesetGenerationFailed;
use ReliqArts\StyleImporter\CSS\Generator\Context;

interface Generator
{
    /**
     * @param Context $context
     *
     * @throws RulesetGenerationFailed
     *
     * @return Ruleset
     */
    public function generate(Context $context): Ruleset;
}
