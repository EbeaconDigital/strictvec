<?php
declare(strict_types=1);

namespace ebeacon\strictvec\OptionalScalar;

use ebeacon\strictvec\AbstractVec;

/**
 * @extends AbstractVec<?int>
 */
class OptionalIntVec extends AbstractVec
{
    /**
     * @inheritDoc
     */
    public function validateValueType($value): bool
    {
        return is_int($value) || $value === null;
    }
}
