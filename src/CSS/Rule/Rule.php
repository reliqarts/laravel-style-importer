<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Rule;

use ReliqArts\StyleImporter\CSS\Rule as RuleContract;

final class Rule implements RuleContract
{
    /**
     * @var string
     */
    private $selector;

    /**
     * @var string
     */
    private $properties;

    /**
     * @var string
     */
    private $mediaQuery;

    /**
     * Rule constructor.
     *
     * @param string $selector
     * @param string $properties
     * @param string $mediaQuery
     */
    private function __construct(string $selector, string $properties, string $mediaQuery)
    {
        $this->selector = $selector;
        $this->properties = $properties;
        $this->mediaQuery = $mediaQuery;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $rule = sprintf('%s {%s}', $this->selector, $this->properties);

        if (empty($this->mediaQuery)) {
            return $rule;
        }

        return sprintf('%s {%s}', $this->mediaQuery, $rule);
    }

    /**
     * @param string $selector
     * @param string $properties
     * @param string $mediaQuery
     *
     * @return Rule
     */
    public static function createNormalized(string $selector, string $properties, string $mediaQuery = ''): Rule
    {
        return new self(
            self::normalizeSelector($selector),
            self::normalizeProperties($properties),
            trim($mediaQuery)
        );
    }

    /**
     * @param string $selector
     *
     * @return string
     */
    private static function normalizeSelector(string $selector): string
    {
        return trim(current(explode(',', $selector)));
    }

    /**
     * @param string $properties
     *
     * @return string
     */
    private static function normalizeProperties(string $properties): string
    {
        return trim(str_replace("\n", ' ', $properties));
    }
}
