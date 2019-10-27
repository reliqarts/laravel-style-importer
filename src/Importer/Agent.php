<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\Importer;

use ReliqArts\StyleImporter\CSS\Exception\RuleExtractionFailed;
use ReliqArts\StyleImporter\CSS\RuleExtractor;
use ReliqArts\StyleImporter\CSS\RuleSet;
use ReliqArts\StyleImporter\Exception\ActiveViewHtmlRetrievalFailed;
use ReliqArts\StyleImporter\HTML\ElementExtractor;
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
     * @var ElementExtractor
     */
    private $htmlExtractor;

    /**
     * @var RuleExtractor
     */
    private $cssRuleExtractor;

    /**
     * @var FileAssistant
     */
    private $fileAssistant;

    /**
     * Importer constructor.
     *
     * @param ViewAccessor     $activeViewAccessor
     * @param ElementExtractor $htmlExtractor
     * @param RuleExtractor    $ruleExtractor
     * @param FileAssistant    $fileAssistant
     */
    public function __construct(
        ViewAccessor $activeViewAccessor,
        ElementExtractor $htmlExtractor,
        RuleExtractor $ruleExtractor,
        FileAssistant $fileAssistant
    ) {
        $this->activeViewAccessor = $activeViewAccessor;
        $this->htmlExtractor = $htmlExtractor;
        $this->cssRuleExtractor = $ruleExtractor;
        $this->fileAssistant = $fileAssistant;
    }

    /**
     * @param string $stylesheetUrl
     *
     * @throws ActiveViewHtmlRetrievalFailed
     * @throws RuleExtractionFailed
     *
     * @return string
     */
    public function import(string $stylesheetUrl): string
    {
        $viewHtml = $this->activeViewAccessor->getViewHTML();
        $elements = $this->htmlExtractor->extractElements($viewHtml);
        $stylesheet = $this->fileAssistant->getFileContents($stylesheetUrl);
        $cssRules = $this->cssRuleExtractor->extractRules($stylesheet, $elements);

        return $this->wrapImportedStyles($cssRules);
    }

    /**
     * @param RuleSet $styles
     *
     * @return string
     */
    private function wrapImportedStyles(RuleSet $styles): string
    {
        return sprintf(self::STYLE_TAG_TEMPLATE, $styles);
    }
}
