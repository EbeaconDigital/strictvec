<?php
declare(strict_types=1);

namespace ebeacon\strictvec\Traits;

/**
 * @template T
 */
trait LenientOptionalObjectValueType
{
    /**
     * Returns the FQCN of the value class or interface, i.e. the data type
     * restriction for all members of this vector.
     *
     * @return class-string<T>
     */
    abstract public function getValueType(): string;

    /**
     * @inheritDoc
     *
     * @param mixed $value
     * @return bool
     */
    public function validateValueType($value): bool
    {
        $cls = $this->getValueType();
        return $value === null ||
               (is_object($value) && is_a($value, $cls));
    }
}
