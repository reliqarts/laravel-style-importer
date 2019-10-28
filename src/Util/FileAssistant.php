<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\Util;

class FileAssistant
{
    /**
     * @codeCoverageIgnore
     * @param string $path
     *
     * @return string
     */
    public function getFileContents(string $path): string
    {
        return file_get_contents($path) ?: '';
    }
}
