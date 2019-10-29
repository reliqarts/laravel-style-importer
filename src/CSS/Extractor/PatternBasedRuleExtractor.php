<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Extractor;

use ReliqArts\StyleImporter\CSS\Exception\RuleExtractionFailed;
use ReliqArts\StyleImporter\CSS\Rule;
use ReliqArts\StyleImporter\CSS\RuleExtractor;
use ReliqArts\StyleImporter\CSS\Rules;
use ReliqArts\StyleImporter\CSS\RuleSet;
use ReliqArts\StyleImporter\Exception\InvalidArgument;

final class PatternBasedRuleExtractor implements RuleExtractor
{
    private const RULE_PATTERN_TEMPLATE = '(?<selector>[^}{,]*%s%s[\s:.,][^{,]*)[^{]*\{(?<properties>[^}]++)\}';
    private const DELIMITED_RULE_PATTERN_TEMPLATE = '%s' . self::RULE_PATTERN_TEMPLATE . '%s';
    private const PATTERN_DELIMITER = '/';
    private const PRE_TAG_CHARACTER_SET = '\s';
    private const PRE_CLASS_OR_ID_CHARACTER_SET = '[\s\w]';
    private const MATCH_GROUP_KEY_SELECTOR = 1;
    private const MATCH_GROUP_KEY_PROPERTIES = 2;

    /**
     * @var MediaBlockExtractor
     */
    private $mediaBlockExtractor;

    /**
     * PatternBasedRuleExtractor constructor.
     *
     * @param MediaBlockExtractor $mediaBlockExtractor
     */
    public function __construct(MediaBlockExtractor $mediaBlockExtractor)
    {
        $this->mediaBlockExtractor = $mediaBlockExtractor;
    }

    /**
     * @param string   $styles
     * @param string[] $htmlElements
     *
     * @throws RuleExtractionFailed
     *
     * @return RuleSet
     */
    public function extractRules(string $styles, array $htmlElements): RuleSet
    {
        try {
            $mediaBlocks = $this->mediaBlockExtractor->extract($styles);
            $sanitizedStyles = $this->sanitizeStyles($this->removeMediaBlocks($styles, $mediaBlocks));
            $rules = $this->getRulesForElements($htmlElements, $sanitizedStyles);

            foreach ($mediaBlocks as $mediaBlock) {
                $mediaBlockRules = $this->getRulesForElements(
                    $htmlElements,
                    $mediaBlock->getStyles(),
                    $mediaBlock->getMediaQuery()
                );

                if (!empty($mediaBlockRules)) {
                    array_push($rules, ...$mediaBlockRules);
                }
            }

            return new Rules($rules);
        } catch (InvalidArgument $exception) {
            throw new RuleExtractionFailed(
                'Failed to extract rules.',
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @param string $styles
     * @param array  $mediaBlocks
     *
     * @return string
     */
    private function removeMediaBlocks(string $styles, array $mediaBlocks): string
    {
        return str_replace($mediaBlocks, '', $styles);
    }

    /**
     * @param string[] $htmlElements
     * @param string   $styles
     * @param string   $mediaQuery
     *
     * @return Rule[]
     */
    private function getRulesForElements(array $htmlElements, string $styles, string $mediaQuery = ''): array
    {
        $rules = [];

        foreach ($htmlElements as $element) {
            $sanitizedElement = $this->sanitizeElement($element);
            $preElementCharacterSet = $this->getElementPreCharacterSet($element);
            $pattern = sprintf(
                self::DELIMITED_RULE_PATTERN_TEMPLATE,
                self::PATTERN_DELIMITER,
                $preElementCharacterSet,
                $sanitizedElement,
                self::PATTERN_DELIMITER
            );

            $matchCount = preg_match_all($pattern, $styles, $matches);

            if (empty($matchCount)) {
                continue;
            }

            $selectors = $matches[self::MATCH_GROUP_KEY_SELECTOR];
            $properties = $matches[self::MATCH_GROUP_KEY_PROPERTIES];

            foreach ($selectors as $index => $selector) {
                $rule = Rule::createNormalized(
                    $selector,
                    $properties[$index],
                    $mediaQuery
                );

                if (!in_array($rule, $rules, true)) {
                    $rules[] = $rule;
                }
            }
        }

        return $rules;
    }

    /**
     * @param string $element
     *
     * @return string
     */
    private function sanitizeElement(string $element): string
    {
        return str_replace('.', '\.', str_replace('#', '\#', $element));
    }

    /**
     * @param string $element
     *
     * @return string
     */
    private function getElementPreCharacterSet(string $element): string
    {
        if (in_array($element[0], ['.', '#'], true)) {
            return self::PRE_CLASS_OR_ID_CHARACTER_SET;
        }

        return self::PRE_TAG_CHARACTER_SET;
    }

    /**
     * @param string $styles
     *
     * @return string
     *
     * @see https://www.regextester.com/94246 Adapted from
     */
    private function sanitizeStyles(string $styles): string
    {
        $replacements = [
            '/{/' => ' {',
            '/\/\*[\s\S]*?\*\/|([^:]|^)\/\/.*$/' => '',
        ];

        return preg_replace(
            array_keys($replacements),
            array_values($replacements),
            sprintf("\n%s", $styles)
        );
    }
}
