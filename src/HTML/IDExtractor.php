<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\HTML;

interface IDExtractor
{
    /**
     * @param string $html
     *
     * @return string[]
     */
    public function extractIds(string $html): array;
}
