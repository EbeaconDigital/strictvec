<?php
declare(strict_types=1);

namespace ebeacon\strictvec\Scalar;

use ebeacon\strictvec\AbstractVec;

/**
 * @extends AbstractVec<string>
 */
class StringVec extends AbstractVec
{
    /**
     * @inheritDoc
     */
    public function validateValueType($value): bool
    {
        return is_string($value);
    }
}
