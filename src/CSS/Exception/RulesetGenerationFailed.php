<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Exception;

use ReliqArts\StyleImporter\CSS\Exception;
use RuntimeException;

final class RulesetGenerationFailed extends RuntimeException implements Exception
{
}
