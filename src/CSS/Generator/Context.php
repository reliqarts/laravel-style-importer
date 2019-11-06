<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Generator;

use ReliqArts\StyleImporter\CSS\MediaBlock;
use ReliqArts\StyleImporter\CSS\Rule\ImportRule;

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
     * @var ImportRule[]
     */
    private $importRules;

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
        $this->mediaBlocks = [];
    }

    /**
     * @param ImportRule ...$importRules
     *
     * @return self
     */
    public function withImportRules(ImportRule ...$importRules): self
    {
        $this->importRules = $importRules;

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
     * @return ImportRule[]
     */
    public function getImportRules(): array
    {
        return $this->importRules;
    }

    /**
     * @return MediaBlock[]
     */
    public function getMediaBlocks(): array
    {
        return $this->mediaBlocks;
    }
}
