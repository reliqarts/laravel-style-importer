<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\HTML;

interface ClassExtractor
{
    /**
     * @param string $html
     *
     * @return string[]
     */
    public function extractClasses(string $html): array;
}
