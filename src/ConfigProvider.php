<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter;

interface ConfigProvider
{
    /**
     * @return string
     */
    public function getCurrentViewNameVariableName(): string;

    /**
     * @return string
     */
    public function getSkipStyleImportVariableName(): string;
}
