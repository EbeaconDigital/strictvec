<?php
declare(strict_types=1);

namespace ebeacon\strictvec\OptionalScalar;

use ebeacon\strictvec\AbstractVec;

/**
 * @extends AbstractVec<?bool>
 */
class OptionalBoolVec extends AbstractVec
{
    /**
     * @inheritDoc
     */
    public function validateValueType($value): bool
    {
        return is_bool($value) || $value === null;
    }
}
