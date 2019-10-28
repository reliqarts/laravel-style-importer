<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\Util;

class BacktraceAccessor
{
    private const KEY_CLASS = 'class';
    private const KEY_OBJECT = 'object';
    private const NO_CLASS = '**NO_CLASS**';
    private const NO_OBJECT = '**NO_OBJECT**';

    /**
     * @param int    $position Position (n)
     * @param string $type     Object
     *
     * @return null|mixed
     */
    public function getNthObjectOfType(int $position, string $type)
    {
        $backtrace = debug_backtrace();
        $lastFoundObjectOfType = null;
        $currentPosition = 0;

        foreach ($backtrace as $item) {
            $itemClass = $item[self::KEY_CLASS] ?? self::NO_CLASS;
            $itemObject = $item[self::KEY_OBJECT] ?? self::NO_OBJECT;

            if ($itemClass !== $type
                || (!empty($lastFoundObjectOfType) && $lastFoundObjectOfType === $itemObject)) {
                continue;
            }

            $lastFoundObjectOfType = $itemObject;
            ++$currentPosition;

            if ($currentPosition === $position) {
                return $itemObject;
            }
        }

        return null;
    }
}
