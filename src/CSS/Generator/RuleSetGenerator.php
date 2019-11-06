<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Generator;

use Exception;
use ReliqArts\StyleImporter\CSS\Exception\RulesetGenerationFailed;
use ReliqArts\StyleImporter\CSS\Generator;
use ReliqArts\StyleImporter\CSS\Rule\Rule;
use ReliqArts\StyleImporter\CSS\Rules;
use ReliqArts\StyleImporter\CSS\Ruleset;
use ReliqArts\StyleImporter\CSS\Util\Sanitizer;

final class RuleSetGenerator implements Generator
{
    private const RULE_PATTERN_TEMPLATE = '(?<selector>[^}{,]*%s%s[\s:.,][^{,]*)[^{]*\{(?<properties>[^}]++)\}';
    private const DELIMITED_RULE_PATTERN_TEMPLATE = '%s' . self::RULE_PATTERN_TEMPLATE . '%s';
    private const PATTERN_DELIMITER = '/';
    private const PRE_TAG_CHARACTER_SET = '\s';
    private const PRE_CLASS_OR_ID_CHARACTER_SET = '[\s\w]';
    private const MATCH_GROUP_KEY_SELECTOR = 1;
    private const MATCH_GROUP_KEY_PROPERTIES = 2;

    /**
     * @param Context $context
     *
     * @throws RulesetGenerationFailed
     *
     * @return Ruleset
     */
    public function generate(Context $context): Ruleset
    {
        $htmlElements = $context->getHtmlElements();

        try {
            $rules = array_merge(
                $context->getImportRules(),
                $this->getRulesForElements($htmlElements, $context->getSanitizedStyles())
            );

            foreach ($context->getMediaBlocks() as $mediaBlock) {
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
        } catch (Exception $exception) {
            throw new RulesetGenerationFailed(
                sprintf('Failed to generate rules. %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
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
            $sanitizedElement = Sanitizer::sanitizeElementName($element);
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
    private function getElementPreCharacterSet(string $element): string
    {
        if (in_array($element[0], ['.', '#'], true)) {
            return self::PRE_CLASS_OR_ID_CHARACTER_SET;
        }

        return self::PRE_TAG_CHARACTER_SET;
    }
}
