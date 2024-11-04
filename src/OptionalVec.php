<?php
declare(strict_types=1);

namespace ebeacon\strictvec;

/**
 * A vector that always allows null values, and dynamically sets the other
 * allowed value type when the first non-null item is added to it.
 *
 * @template T
 * @extends AbstractVec<T>
 */
class OptionalVec extends AbstractVec
{
    private ?string $valueType = null;
    private ?string $valueClass = null;

    /**
     * @inheritDoc
     */
    public function validateValueType($value): bool
    {
        // Always allow null. This also enables us to not set the type until
        // a non-null type is encountered.
        if ($value === null) {
            return true;
        }

        if ($this->valueType === null) {
            $this->valueType = gettype($value);
            if ($this->valueType === "object") {
                /** @var object $value */
                $this->valueClass = get_class($value);
            }
        }

        switch ($this->valueType) {
            case "boolean":
                return is_bool($value);
            case "integer":
                return is_int($value);
            case "double":
                return is_float($value);
            case "float": // Should never occur, just future-proofing.
                return is_float($value);
            case "string":
                return is_string($value);
            case "array":
                return is_array($value);
            case "object":
                /** @var object $value */
                return get_class($value) === $this->valueClass;
            case "resource":
                return is_resource($value);
            case "resource (closed)":
                return is_resource($value) || (
                        // Normally we'd check that `$value` is not null, too,
                        // but we already know that it isn't--it was the first
                        // thing we checked in this function.
                        !is_scalar($value) &&
                        !is_array($value) &&
                        !is_object($value)
                    );
            default:
                return false;
        }
    }
}
