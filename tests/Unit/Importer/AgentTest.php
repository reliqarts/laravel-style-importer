<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\Tests\Unit\Importer;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use ReliqArts\StyleImporter\CSS\Processor;
use ReliqArts\StyleImporter\CSS\Ruleset;
use ReliqArts\StyleImporter\HTML\Extractor;
use ReliqArts\StyleImporter\Importer;
use ReliqArts\StyleImporter\Importer\Agent;
use ReliqArts\StyleImporter\Util\FileAssistant;
use ReliqArts\StyleImporter\Util\ViewAccessor;

/**
 * Class AgentTest.
 *
 * @coversDefaultClass \ReliqArts\StyleImporter\Importer\Agent
 *
 * @internal
 */
final class AgentTest extends TestCase
{
    /**
     * @var ObjectProphecy|ViewAccessor
     */
    private $activeViewAccessor;

    /**
     * @var Extractor|ObjectProphecy
     */
    private $htmlExtractor;

    /**
     * @var ObjectProphecy|Processor
     */
    private $cssProcessor;

    /**
     * @var FileAssistant|ObjectProphecy
     */
    private $fileAssistant;

    /**
     * @var ObjectProphecy|Ruleset
     */
    private $ruleSet;

    /**
     * @var Importer
     */
    private $subject;

    protected function setUp(): void
    {
        $this->activeViewAccessor = $this->prophesize(ViewAccessor::class);
        $this->htmlExtractor = $this->prophesize(Extractor::class);
        $this->cssProcessor = $this->prophesize(Processor::class);
        $this->fileAssistant = $this->prophesize(FileAssistant::class);
        $this->ruleSet = $this->prophesize(Ruleset::class);

        $this->subject = new Agent(
            $this->activeViewAccessor->reveal(),
            $this->htmlExtractor->reveal(),
            $this->cssProcessor->reveal(),
            $this->fileAssistant->reveal()
        );
    }

    public function testImport(): void
    {
        $html = '';
        $elements = [];
        $stylesheetUrl = 'url://stylesheet';
        $styles = 'span {color: red}';
        $expectedResult = sprintf('<style type="text/css">%s</style>', $styles);

        $this->activeViewAccessor
            ->getViewHTML()
            ->shouldBeCalledTimes(1)
            ->willReturn($html);

        $this->htmlExtractor
            ->extract($html)
            ->shouldBeCalledTimes(1)
            ->willReturn($elements);

        $this->fileAssistant
            ->getFileContents($stylesheetUrl)
            ->shouldBeCalledTimes(1)
            ->willReturn($styles);

        $this->ruleSet
            ->__toString()
            ->shouldBeCalledTimes(1)
            ->willReturn($styles);

        $this->cssProcessor
            ->getStyles($styles, ...$elements)
            ->shouldBeCalledTimes(1)
            ->willReturn($this->ruleSet);

        $this->assertSame($expectedResult, $this->subject->import($stylesheetUrl));
    }
}
