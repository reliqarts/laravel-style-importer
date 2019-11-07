<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter;

use ReliqArts\StyleImporter\Exception\ActiveViewHtmlRetrievalFailed;

interface Importer
{
    /**
     * @param string $stylesheetUrl
     * @param string ...$initialHtmlElements
     *
     * @throws ActiveViewHtmlRetrievalFailed
     *
     * @return string
     */
    public function import(string $stylesheetUrl, string ...$initialHtmlElements): string;
}
