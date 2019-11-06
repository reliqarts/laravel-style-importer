<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\HTML;

interface Extractor extends ClassExtractor, IDExtractor, TagExtractor
{
    /**
     * Extract elements of interest from HTML.
     * i.e. tags, IDs and classes.
     *
     * @param string $html
     *
     * @return array
     */
    public function extract(string $html): array;
}
