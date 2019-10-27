<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\Util;

final class FileAssistant
{
    /**
     * @param string $path
     *
     * @return string
     */
    public function getFileContents(string $path): string
    {
        return file_get_contents($path) ?: '';
    }
}
