<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS;

use ReliqArts\StyleImporter\Exception\InvalidArgument;

interface Extractor
{
    /**
     * @param string $styles
     *
     * @throws InvalidArgument
     *
     * @return Extractable[]
     */
    public function extract(string $styles): array;
}
