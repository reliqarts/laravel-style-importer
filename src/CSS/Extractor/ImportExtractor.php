<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Extractor;

use ReliqArts\StyleImporter\CSS\Extractable;
use ReliqArts\StyleImporter\CSS\Rule\Import;

/**
 * Class ImportExtractor.
 */
final class ImportExtractor extends SimplePatternExtractor
{
    private const PATTERN = '/(@import[^;]+\;)/';

    /**
     * @return string
     */
    protected function getPattern(): string
    {
        return self::PATTERN;
    }

    /**
     * @param string $match
     *
     * @return Extractable
     */
    protected function createExtractable(string $match): Extractable
    {
        return new Import($match);
    }
}
