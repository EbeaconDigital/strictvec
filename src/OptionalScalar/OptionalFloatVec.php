<?php
declare(strict_types=1);

namespace ebeacon\strictvec\OptionalScalar;

use ebeacon\strictvec\AbstractVec;

/**
 * @extends AbstractVec<?float>
 */
class OptionalFloatVec extends AbstractVec
{
    /**
     * @inheritDoc
     */
    public function validateValueType($value): bool
    {
        return is_float($value) || $value === null;
    }
}
