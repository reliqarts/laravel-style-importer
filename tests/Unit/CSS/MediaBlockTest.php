<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\Tests\Unit\CSS;

use PHPUnit\Framework\TestCase;
use ReliqArts\StyleImporter\CSS\MediaBlock;
use ReliqArts\StyleImporter\Exception\InvalidArgument;

/**
 * Class MediaBlockTest.
 *
 * @coversDefaultClass \ReliqArts\StyleImporter\CSS\MediaBlock
 *
 * @internal
 */
final class MediaBlockTest extends TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $input
     * @param string $expectedMediaQuery
     * @param string $expectedStyles
     */
    public function testCreateFromString(string $input, string $expectedMediaQuery, string $expectedStyles): void
    {
        $mediaBlock = MediaBlock::createFromString($input);

        $this->assertSame($expectedMediaQuery, $mediaBlock->getMediaQuery());
        $this->assertSame($expectedStyles, $mediaBlock->getStyles());
        $this->assertSame($input, (string)$mediaBlock);
    }

    /**
     * @return array
     */
    public function createDataProvider(): array
    {
        return [
            [
                '@media screen { .foo {color: red} }',
                '@media screen',
                '.foo {color: red}',
            ],
            [
                '@media screen and (max-width: 20em)   { 
                    .foo {color: red}
                    span {
                        background-color: purple;
                    }
                }',
                '@media screen and (max-width: 20em)',
                '.foo {color: red}
                    span {
                        background-color: purple;
                    }',
            ],
        ];
    }

    public function testCreateFromStringWithInvalidInput(): void
    {
        $this->expectException(InvalidArgument::class);

        $mediaBlock = MediaBlock::createFromString('');
    }
}
