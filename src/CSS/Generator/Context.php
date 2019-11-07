<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Generator;

use ReliqArts\StyleImporter\CSS\MediaBlock;
use ReliqArts\StyleImporter\CSS\Rule\FontFace;
use ReliqArts\StyleImporter\CSS\Rule\Import;

final class Context
{
    /**
     * @var string
     */
    private $sanitizedStyles;

    /**
     * @var string[]
     */
    private $htmlElements;

    /**
     * @var Import[]
     */
    private $importRules;

    /**
     * @var FontFace[]
     */
    private $fontFaceRules;

    /**
     * @var MediaBlock[]
     */
    private $mediaBlocks;

    /**
     * Context constructor.
     *
     * @param string $sanitizedStyles
     * @param string ...$htmlElements
     */
    public function __construct(string $sanitizedStyles, string ...$htmlElements)
    {
        $this->sanitizedStyles = $sanitizedStyles;
        $this->htmlElements = $htmlElements;
        $this->importRules = [];
        $this->fontFaceRules = [];
        $this->mediaBlocks = [];
    }

    /**
     * @param Import ...$importRules
     *
     * @return self
     */
    public function withImportRules(Import ...$importRules): self
    {
        $this->importRules = $importRules;

        return $this;
    }

    /**
     * @param FontFace ...$fontFaceRules
     *
     * @return self
     */
    public function withFontFaceRules(FontFace ...$fontFaceRules): self
    {
        $this->fontFaceRules = $fontFaceRules;

        return $this;
    }

    /**
     * @param MediaBlock ...$mediaBlocks
     *
     * @return self
     */
    public function withMediaBlocks(MediaBlock ...$mediaBlocks): self
    {
        $this->mediaBlocks = $mediaBlocks;

        return $this;
    }

    /**
     * @return string
     */
    public function getSanitizedStyles(): string
    {
        return $this->sanitizedStyles;
    }

    /**
     * @return string[]
     */
    public function getHtmlElements(): array
    {
        return $this->htmlElements;
    }

    /**
     * @return Import[]
     */
    public function getImportRules(): array
    {
        return $this->importRules;
    }

    /**
     * @return FontFace[]
     */
    public function getFontFaceRules(): array
    {
        return $this->fontFaceRules;
    }

    /**
     * @return MediaBlock[]
     */
    public function getMediaBlocks(): array
    {
        return $this->mediaBlocks;
    }
}
