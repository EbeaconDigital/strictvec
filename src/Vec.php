<?php
declare(strict_types=1);

namespace ebeacon\strictvec;

/**
 * A vector that dynamically sets the allowed value type when the first item
 * is added to it.
 *
 * @template T
 * @extends AbstractVec<T>
 */
class Vec extends AbstractVec
{
    private ?string $valueType = null;
    private ?string $valueClass = null;

    /**
     * @inheritDoc
     */
    public function validateValueType($value): bool
    {
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
                    $value !== null &&
                    !is_scalar($value) &&
                    !is_array($value) &&
                    !is_object($value)
                );
            case "NULL": // Are you some sort of lunatic?
                return $value === null;
            default:
                return false;
        }
    }
}
