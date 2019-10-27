<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Extractor;

use ReliqArts\StyleImporter\CSS\MediaBlock;
use ReliqArts\StyleImporter\Exception\InvalidArgument;

final class MediaBlockExtractor
{
    /**
     * @param string $styles
     *
     * @throws InvalidArgument
     *
     * @return MediaBlock[]
     *
     * @see https://stackoverflow.com/a/14145856 Adapted from this answer on the legendary StackOverflow
     */
    public function extract(string $styles): array
    {
        $mediaBlocks = [];

        $start = 0;
        while (($start = strpos($styles, MediaBlock::TOKEN_MEDIA, $start)) !== false) {
            // stack to manage brackets
            $brackets = [];

            // get the first opening bracket
            $openingBracketPosition = strpos($styles, MediaBlock::TOKEN_OPENING_BRACKET, $start);

            // if $i is false, then there is probably a css syntax error
            if ($openingBracketPosition !== false) {
                // push bracket onto stack
                array_push($brackets, $styles[$openingBracketPosition]);

                // move past first bracket
                ++$openingBracketPosition;

                while (!empty($brackets)) {
                    // if the character is an opening bracket, push it onto the stack, otherwise pop the stack
                    if ($styles[$openingBracketPosition] === MediaBlock::TOKEN_OPENING_BRACKET) {
                        array_push($brackets, MediaBlock::TOKEN_OPENING_BRACKET);
                    } elseif ($styles[$openingBracketPosition] === MediaBlock::TOKEN_CLOSING_BRACKET) {
                        array_pop($brackets);
                    }

                    ++$openingBracketPosition;
                }

                // cut the media block out of the css and store
                $mediaBlocks[] = MediaBlock::createFromString(substr(
                    $styles,
                    $start,
                    ($openingBracketPosition + 1) - $start
                ) ?: '');

                // set the new $start to the end of the block
                $start = $openingBracketPosition;
            }
        }

        return $mediaBlocks;
    }
}
