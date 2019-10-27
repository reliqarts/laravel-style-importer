<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter;

interface Importer
{
    /**
     * @param string $stylesheetUrl
     *
     * @return string
     */
    public function import(string $stylesheetUrl): string;
}
