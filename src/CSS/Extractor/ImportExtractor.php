<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Extractor;

use ReliqArts\StyleImporter\CSS\Extractable;
use ReliqArts\StyleImporter\CSS\Extractor;
use ReliqArts\StyleImporter\CSS\Rule\ImportRule;

/**
 * Class ImportExtractor.
 */
final class ImportExtractor implements Extractor
{
    private const PATTERN = '/(@import[^;]+\;)/';

    /**
     * @param string $styles
     *
     * @return array
     */
    public function extract(string $styles): array
    {
        $matchCount = preg_match_all(self::PATTERN, $styles, $matches);

        if (empty($matchCount)) {
            return [];
        }

        return array_map(
            function (string $match): Extractable {
                return new ImportRule($match);
            },
            $matches[1]
        );
    }
}
