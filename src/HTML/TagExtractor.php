<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\HTML;

interface TagExtractor
{
    /**
     * @param string $html
     *
     * @return string[]
     */
    public function extractTags(string $html): array;
}
