<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\HTML\Extractor;

use ReliqArts\StyleImporter\HTML\Extractor;

final class ElementExtractor implements Extractor
{
    private const PATTERN_CLASSES = '#\<\w[^<>]*\sclass=[\"\\\']([\w\s-]+)[\"\\\'][\s\>\/]#';
    private const PATTERN_IDS = '#\<\w[^<>]*\sid=[\"\\\']([\w-]+)[\"\\\'][\s\>\/]\>?#';
    private const PATTERN_TAGS = '#\<([\w-]+)[\s\>]#';

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
    public function extractIds(string $html): array
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
    public function extract(string $html): array
    {
        return array_merge(
            $this->extractTags($html),
            $this->extractIds($html),
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
