<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\Tests\Unit\Importer;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use ReliqArts\StyleImporter\CSS\RuleExtractor;
use ReliqArts\StyleImporter\CSS\RuleSet;
use ReliqArts\StyleImporter\HTML\ElementExtractor;
use ReliqArts\StyleImporter\Importer;
use ReliqArts\StyleImporter\Importer\Agent;
use ReliqArts\StyleImporter\Util\FileAssistant;
use ReliqArts\StyleImporter\Util\ViewAccessor;

/**
 * Class AgentTest
 *
 * @coversDefaultClass \ReliqArts\StyleImporter\Importer\Agent
 */
final class AgentTest extends TestCase
{
    /**
     * @var ViewAccessor|ObjectProphecy
     */
    private $activeViewAccessor;

    /**
     * @var ElementExtractor|ObjectProphecy
     */
    private $htmlExtractor;

    /**
     * @var RuleExtractor|ObjectProphecy
     */
    private $cssRuleExtractor;

    /**
     * @var FileAssistant|ObjectProphecy
     */
    private $fileAssistant;

    /**
     * @var RuleSet|ObjectProphecy
     */
    private $ruleSet;

    /**
     * @var Importer
     */
    private $subject;

    protected function setUp(): void
    {
        $this->activeViewAccessor = $this->prophesize(ViewAccessor::class);
        $this->htmlExtractor = $this->prophesize(ElementExtractor::class);
        $this->cssRuleExtractor = $this->prophesize(RuleExtractor::class);
        $this->fileAssistant = $this->prophesize(FileAssistant::class);
        $this->ruleSet = $this->prophesize(RuleSet::class);

        $this->subject = new Agent(
            $this->activeViewAccessor->reveal(),
            $this->htmlExtractor->reveal(),
            $this->cssRuleExtractor->reveal(),
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
            ->extractElements($html)
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

        $this->cssRuleExtractor
            ->extractRules($styles, $elements)
            ->shouldBeCalledTimes(1)
            ->willReturn($this->ruleSet);

        $this->assertSame($expectedResult, $this->subject->import($stylesheetUrl));
    }
}
