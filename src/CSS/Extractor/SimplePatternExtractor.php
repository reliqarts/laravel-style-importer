<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Extractor;

use ReliqArts\StyleImporter\CSS\Extractable;
use ReliqArts\StyleImporter\CSS\Extractor;
use ReliqArts\StyleImporter\CSS\Rule\Import;

abstract class SimplePatternExtractor implements Extractor
{
    /**
     * @param string $styles
     *
     * @return Import[]
     */
    public function extract(string $styles): array
    {
        $matchCount = preg_match_all($this->getPattern(), $styles, $matches);

        if (empty($matchCount)) {
            return [];
        }

        return array_map(
            function (string $match): Extractable {
                return $this->createExtractable($match);
            },
            $matches[1]
        );
    }

    /**
     * @return string
     */
    abstract protected function getPattern(): string;

    /**
     * @param string $match
     *
     * @return Extractable
     */
    abstract protected function createExtractable(string $match): Extractable;
}
