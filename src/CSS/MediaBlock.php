<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS;

use ReliqArts\StyleImporter\Exception\InvalidArgument;

final class MediaBlock
{
    public const TOKEN_MEDIA = '@media';
    public const TOKEN_OPENING_BRACKET = '{';
    public const TOKEN_CLOSING_BRACKET = '}';

    /**
     * @var string
     */
    private $mediaQuery;

    /**
     * @var string
     */
    private $styles;

    /**
     * @var string block exactly how it appeared in the source
     */
    private $raw;

    /**
     * MediaBlock constructor.
     *
     * @param string $mediaQuery
     * @param string $styles
     * @param string $raw
     */
    private function __construct(string $mediaQuery, string $styles, string $raw)
    {
        $this->mediaQuery = $mediaQuery;
        $this->styles = $styles;
        $this->raw = $raw;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->raw;
    }

    /**
     * @param string $block
     *
     * @throws InvalidArgument
     *
     * @return self
     */
    public static function createFromString(string $block): self
    {
        if (empty($block)) {
            throw new InvalidArgument('Cannot build media block from empty string.');
        }

        list($mediaQuery, $stylesWithTrailingBracket) = array_map(
            'trim',
            explode(MediaBlock::TOKEN_OPENING_BRACKET, $block, 2)
        );
        $styles = substr($stylesWithTrailingBracket, 0, -1);

        return new self($mediaQuery, $styles, $block);
    }

    /**
     * @return string
     */
    public function getMediaQuery(): string
    {
        return $this->mediaQuery;
    }

    /**
     * @return string
     */
    public function getStyles(): string
    {
        return $this->styles;
    }
}
