<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\Importer;

use ReliqArts\StyleImporter\CSS\Exception\RulesetGenerationFailed;
use ReliqArts\StyleImporter\CSS\Processor;
use ReliqArts\StyleImporter\CSS\Ruleset;
use ReliqArts\StyleImporter\Exception\ActiveViewHtmlRetrievalFailed;
use ReliqArts\StyleImporter\HTML\Extractor;
use ReliqArts\StyleImporter\Importer;
use ReliqArts\StyleImporter\Util\FileAssistant;
use ReliqArts\StyleImporter\Util\ViewAccessor;

final class Agent implements Importer
{
    private const STYLE_TAG_TEMPLATE = '<style type="text/css">%s</style>';

    /**
     * @var ViewAccessor
     */
    private $activeViewAccessor;

    /**
     * @var Extractor
     */
    private $htmlExtractor;

    /**
     * @var Processor
     */
    private $cssProcessor;

    /**
     * @var FileAssistant
     */
    private $fileAssistant;

    /**
     * Importer constructor.
     *
     * @param ViewAccessor  $activeViewAccessor
     * @param Extractor     $htmlExtractor
     * @param Processor     $cssProcessor
     * @param FileAssistant $fileAssistant
     */
    public function __construct(
        ViewAccessor $activeViewAccessor,
        Extractor $htmlExtractor,
        Processor $cssProcessor,
        FileAssistant $fileAssistant
    ) {
        $this->activeViewAccessor = $activeViewAccessor;
        $this->htmlExtractor = $htmlExtractor;
        $this->cssProcessor = $cssProcessor;
        $this->fileAssistant = $fileAssistant;
    }

    /**
     * @param string $stylesheetUrl
     *
     * @throws ActiveViewHtmlRetrievalFailed
     * @throws RulesetGenerationFailed
     *
     * @return string
     */
    public function import(string $stylesheetUrl): string
    {
        $viewHtml = $this->activeViewAccessor->getViewHTML();
        $elements = $this->htmlExtractor->extract($viewHtml);
        $stylesheet = $this->fileAssistant->getFileContents($stylesheetUrl);
        $cssRules = $this->cssProcessor->getStyles($stylesheet, ...$elements);

        return $this->wrapImportedStyles($cssRules);
    }

    /**
     * @param Ruleset $styles
     *
     * @return string
     */
    private function wrapImportedStyles(Ruleset $styles): string
    {
        return sprintf(self::STYLE_TAG_TEMPLATE, $styles);
    }
}
