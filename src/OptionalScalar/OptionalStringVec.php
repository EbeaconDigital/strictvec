<?php
declare(strict_types=1);

namespace ebeacon\strictvec\OptionalScalar;

use ebeacon\strictvec\AbstractVec;

/**
 * @extends AbstractVec<?string>
 */
class OptionalStringVec extends AbstractVec
{
    /**
     * @inheritDoc
     */
    public function validateValueType($value): bool
    {
        return is_string($value) || $value === null;
    }
}
