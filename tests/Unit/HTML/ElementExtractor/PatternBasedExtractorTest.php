<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\Tests\Unit\HTML\ElementExtractor;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use ReliqArts\StyleImporter\HTML\ElementExtractor\PatternBasedExtractor;

/**
 * Class PatternBasedExtractorTest.
 *
 * @coversDefaultClass \ReliqArts\StyleImporter\HTML\ElementExtractor\PatternBasedExtractor
 */
final class PatternBasedExtractorTest extends TestCase
{
    /**
     * @var PatternBasedExtractor|ObjectProphecy
     */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new PatternBasedExtractor();
    }

    /**
     * @covers ::extractClasses
     * @covers ::extractItemsByPattern
     * @dataProvider extractClassesDataProvider
     *
     * @param string $html
     * @param array  $expectedClasses
     */
    public function testExtractClasses(string $html, array $expectedClasses): void
    {
        $result = $this->subject->extractClasses($html);

        $this->assertIsArray($result);
        $this->assertSameSize($expectedClasses, $result);

        foreach ($expectedClasses as $class) {
            $this->assertContains($class, $result);
        }
    }

    /**
     * @return array
     */
    public function extractClassesDataProvider(): array
    {
        return [
            'simple' => [
                '<span class="hello world"></span>',
                [
                    '.hello',
                    '.world',
                ],
            ],
            'with additional attributes behind' => [
                '<span class="hello world" id="yo"></span>',
                [
                    '.hello',
                    '.world',
                ],
            ],
            'with additional attributes before' => [
                '<span id="yo" class="h w"></span>',
                [
                    '.h',
                    '.w',
                ],
            ],
            'with strange characters' => [
                '<span class="hel-o wo_rld" id="yo"></span>',
                [
                    '.hel-o',
                    '.wo_rld',
                ],
            ],
            'with multiple tags' => [
                '<span class="hel-o wo_rld" id="yo"></span>
                 <p class="foo" id="bar"></p>
                 <ul class="list">
                     <li id="hmm" class="sugar"></li>   
                 </ul>',
                [
                    '.hel-o',
                    '.wo_rld',
                    '.foo',
                    '.list',
                    '.sugar',
                ],
            ],
        ];
    }

    /**
     * @covers ::extractIds
     * @covers ::extractItemsByPattern
     * @dataProvider extractIdsDataProvider
     *
     * @param string $html
     * @param array  $expectedIds
     */
    public function testExtractIds(string $html, array $expectedIds): void
    {
        $result = $this->subject->extractIds($html);

        $this->assertIsArray($result);
        $this->assertSameSize($expectedIds, $result);

        foreach ($expectedIds as $expectedId) {
            $this->assertContains($expectedId, $result);
        }
    }

    /**
     * @return array
     */
    public function extractIdsDataProvider(): array
    {
        return [
            'simple' => [
                '<span id="helloworld"></span>',
                [
                    '#helloworld',
                ],
            ],
            'with additional attributes behind' => [
                '<span id="helloworld" class="yo"></span>',
                [
                    '#helloworld',
                ],
            ],
            'with additional attributes before' => [
                '<span class="yo" style="color: red;" id="hw"></span>',
                [
                    '#hw',
                ],
            ],
            'with strange characters' => [
                '<span id="hel-owo_rld"></span>',
                [
                    '#hel-owo_rld',
                ],
            ],
            'with multiple tags' => [
                '<div id="foo">
                    <span id="hel-owo_rld"></span>
                    <span id="a"></span>
                 </div>',
                [
                    '#hel-owo_rld',
                    '#foo',
                    '#a',
                ],
            ],
        ];
    }

    /**
     * @covers ::extractTags
     * @covers ::extractItemsByPattern
     * @dataProvider extractTagsDataProvider
     *
     * @param string $html
     * @param array  $expectedTags
     */
    public function testExtractTags(string $html, array $expectedTags): void
    {
        $result = $this->subject->extractTags($html);

        $this->assertIsArray($result);
        $this->assertSameSize($expectedTags, $result);

        foreach ($expectedTags as $expectedId) {
            $this->assertContains($expectedId, $result);
        }
    }

    /**
     * @return array
     */
    public function extractTagsDataProvider(): array
    {
        return [
            'simple' => [
                '<span id="helloworld"></span>',
                [
                    'span',
                ],
            ],
            'with strange characters' => [
                '<custom-tag id="hel-owo_rld"></custom-tag>',
                [
                    'custom-tag',
                ],
            ],
            'with multiple tags' => [
                '<div id="foo">
                    <span id="hel-owo_rld"></span>
                    <span id="a"></span>
                 </div>',
                [
                    'div',
                    'span',
                ],
            ],
        ];
    }

    /**
     * @covers ::extractElements
     * @covers ::extractItemsByPattern
     * @dataProvider extractElementsDataProvider
     *
     * @param string $html
     * @param array  $expectedElements
     */
    public function testExtractElements(string $html, array $expectedElements): void
    {
        $result = $this->subject->extractElements($html);

        $this->assertIsArray($result);
        $this->assertSameSize($expectedElements, $result);

        foreach ($expectedElements as $expectedElement) {
            $this->assertContains($expectedElement, $result);
        }
    }

    /**
     * @return array
     */
    public function extractElementsDataProvider(): array
    {
        return [
            'simple' => [
                '<span id="helloworld"></span>',
                [
                    'span',
                    '#helloworld',
                ],
            ],
            'with strange characters' => [
                '<custom-tag class="hello" id="hel-owo_rld"></custom-tag>',
                [
                    'custom-tag',
                    '.hello',
                    '#hel-owo_rld',
                ],
            ],
            'with multiple tags' => [
                '<div id="foo">
                    <span id="hel-owo_rld"></span>
                    <span id="a"></span>
                 </div>',
                [
                    'div',
                    'span',
                    '#hel-owo_rld',
                    '#foo',
                    '#a'
                ],
            ],
        ];
    }
}
