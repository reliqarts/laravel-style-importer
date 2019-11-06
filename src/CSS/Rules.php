<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Rules.
 *
 * @method Rule[] getIterator()
 */
final class Rules extends ArrayCollection implements Ruleset
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function __toString(): string
    {
        return implode("\n", $this->toArray());
    }
}
