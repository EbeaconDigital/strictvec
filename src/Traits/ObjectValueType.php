<?php
declare(strict_types=1);

namespace ebeacon\strictvec\Traits;

/**
 * @template T
 */
trait ObjectValueType
{
    /**
     * Returns the FQCN of the value class, i.e. the data type restriction for
     * all members of this vector.
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
        return is_object($value) && get_class($value) === $cls;
    }
}
