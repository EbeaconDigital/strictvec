<?php
declare(strict_types=1);

namespace ebeacon\strictvec;

/**
 * A vector that dynamically sets the allowed value type(s) as items are added.
 * This is distinct from `Vec` because--in the case of objects--parent classes
 * and implemented interfaces are taken into consideration. So long as added
 * items share a type, parent type, or interface in common, they are allowed.
 *
 * @template T
 * @extends AbstractVec<T>
 */
class LenientVec extends AbstractVec
{
    private ?string $valueType = null;
    private ?array $valueClassProfile = null;

    /**
     * @inheritDoc
     */
    public function validateValueType($value): bool
    {
        if ($this->valueType === null) {
            $this->valueType = gettype($value);
            if ($this->valueType === "object") {
                /** @var object $value */
                $cls = get_class($value);
                $this->valueClassProfile = static::getClassProfile($cls);
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
                $cls = get_class($value);

                /** @var array $this->valueClassProfile */
                $newProfile = array_intersect_key(
                    $this->valueClassProfile,
                    static::getClassProfile($cls)
                );

                if (count($newProfile) === 0) {
                    return false;
                }

                $this->valueClassProfile = $newProfile;
                return true;
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

    /**
     * Returns an array containing the provided class name, its ancestor
     * classes, and any interfaces it implements.
     *
     * @param string $cls
     * @return array<string, string>
     */
    protected static function getClassProfile(string $cls): array
    {
        return array_merge(
            [$cls => $cls],
            class_parents($cls),
            class_implements($cls)
        );
    }
}
