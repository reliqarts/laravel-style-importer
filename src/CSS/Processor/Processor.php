<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Processor;

use ReliqArts\Contracts\Logger;
use ReliqArts\StyleImporter\CSS\Exception\RulesetGenerationFailed;
use ReliqArts\StyleImporter\CSS\Extractable;
use ReliqArts\StyleImporter\CSS\Extractor;
use ReliqArts\StyleImporter\CSS\Generator;
use ReliqArts\StyleImporter\CSS\Generator\Context;
use ReliqArts\StyleImporter\CSS\Processor as ProviderContract;
use ReliqArts\StyleImporter\CSS\Rules;
use ReliqArts\StyleImporter\CSS\Ruleset;
use ReliqArts\StyleImporter\CSS\Util\Sanitizer;
use ReliqArts\StyleImporter\Exception\InvalidArgument;

final class Processor implements ProviderContract
{
    /**
     * @var Extractor
     */
    private $importExtractor;

    /**
     * @var Extractor
     */
    private $mediaBlockExtractor;

    /**
     * @var Generator
     */
    private $ruleGenerator;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Processor constructor.
     *
     * @param Extractor $importExtractor
     * @param Extractor $mediaBlockExtractor
     * @param Generator $ruleGenerator
     * @param Logger    $logger
     */
    public function __construct(
        Extractor $importExtractor,
        Extractor $mediaBlockExtractor,
        Generator $ruleGenerator,
        Logger $logger
    ) {
        $this->importExtractor = $importExtractor;
        $this->mediaBlockExtractor = $mediaBlockExtractor;
        $this->ruleGenerator = $ruleGenerator;
        $this->logger = $logger;
    }

    /**
     * @param string $styles
     * @param string ...$htmlElements
     *
     * @return Ruleset
     */
    public function getStyles(string $styles, string ...$htmlElements): Ruleset
    {
        try {
            $importRules = $this->importExtractor->extract($styles);
            $mediaBlocks = $this->mediaBlockExtractor->extract($styles);
            $extracted = array_merge($importRules, $mediaBlocks);
            $sanitizedStyles = Sanitizer::sanitizeStyles($this->removeExtractedSections($styles, ...$extracted));
            $generatorContext = new Context($sanitizedStyles, ...$htmlElements);

            return $this->ruleGenerator->generate(
                $generatorContext
                    ->withImportRules(...$importRules)
                    ->withMediaBlocks(...$mediaBlocks)
            );
        } catch (InvalidArgument | RulesetGenerationFailed $exception) {
            $this->logger->error(
                sprintf('CSS processing failed; %s', $exception->getMessage()),
                ['trace' => $exception->getTrace()]
            );

            return new Rules();
        }
    }

    /**
     * @param string      $styles
     * @param Extractable ...$extracted
     *
     * @return string
     */
    private function removeExtractedSections(string $styles, Extractable ...$extracted): string
    {
        return str_replace($extracted, '', $styles);
    }
}
