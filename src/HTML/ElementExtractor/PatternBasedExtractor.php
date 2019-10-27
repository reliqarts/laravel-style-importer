<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\HTML\ElementExtractor;

use ReliqArts\StyleImporter\HTML\ElementExtractor;

final class PatternBasedExtractor implements ElementExtractor
{
    private const PATTERN_CLASSES = '#\<[a-z][\w\s]+\sclass=[\"\\\']([A-za-z\s]+)[\"\\\'][\s\>\/]\>?#';
    private const PATTERN_IDS = '#\<[a-z][\w\s]+\sid=[\"\\\']([A-za-z]+)[\"\\\'][\s\>\/]\>?#';
    private const PATTERN_TAGS = '#\<([a-z]+)[\s\>]#';

    /**
     * @param string $html
     *
     * @return string[]
     */
    public function extractClasses(string $html): array
    {
        $extractedClasses = $this->extractItemsByPattern($html, self::PATTERN_CLASSES);
        $classNames = [];

        foreach ($extractedClasses as $combinedClass) {
            $splitClasses = array_map(
                function (string $class): string {
                    return sprintf('.%s', $class);
                },
                preg_split('/\s+/', $combinedClass)
            );

            array_push($classNames, ...$splitClasses);
        }

        return array_unique($classNames);
    }

    /**
     * @param string $html
     *
     * @return string[]
     */
    public function extractIDs(string $html): array
    {
        $idNames = $this->extractItemsByPattern($html, self::PATTERN_IDS);

        return array_map(
            function (string $id): string {
                return sprintf('#%s', $id);
            },
            array_unique($idNames)
        );
    }

    /**
     * @param string $html
     *
     * @return string[]
     */
    public function extractTags(string $html): array
    {
        return array_unique($this->extractItemsByPattern($html, self::PATTERN_TAGS));
    }

    /**
     * {@inheritdoc}
     *
     * @param string $html
     *
     * @return array
     */
    public function extractElements(string $html): array
    {
        return array_merge(
            $this->extractTags($html),
            $this->extractIDs($html),
            $this->extractClasses($html)
        );
    }

    /**
     * @param string $subject
     * @param string $pattern
     *
     * @return array
     */
    private function extractItemsByPattern(string $subject, string $pattern): array
    {
        $matchCount = preg_match_all($pattern, $subject, $matches);

        if (empty($matchCount)) {
            return [];
        }

        return $matches[1];
    }
}
