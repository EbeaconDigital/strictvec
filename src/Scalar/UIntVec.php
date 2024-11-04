<?php
declare(strict_types=1);

namespace ebeacon\strictvec\Scalar;

use ebeacon\strictvec\AbstractVec;

/**
 * @extends AbstractVec<positive-int|0>
 */
class UIntVec extends AbstractVec
{
    /**
     * @inheritDoc
     */
    protected function validateValue($value): void
    {
        parent::validateValue($value);

        if ($value < 0) {
            $msg = sprintf("Values for %s must be non-negative",
            static::class
            );
            throw new \ValueError($msg);
        }
    }

    /**
     * @inheritDoc
     */
    public function validateValueType($value): bool
    {
        return is_int($value);
    }
}
