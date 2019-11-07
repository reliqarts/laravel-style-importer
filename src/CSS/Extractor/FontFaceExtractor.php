<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Extractor;

use ReliqArts\StyleImporter\CSS\Extractable;
use ReliqArts\StyleImporter\CSS\Rule\FontFace;

/**
 * Class ImportExtractor.
 */
final class FontFaceExtractor extends SimplePatternExtractor
{
    private const PATTERN = '/(@font-face[^}]+\})/';

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
        return new FontFace($match);
    }
}
