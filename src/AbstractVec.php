<?php
declare(strict_types=1);

namespace ebeacon\strictvec;

use ArrayAccess;
use CompileError;
use Countable;
use Iterator;
use OutOfRangeException;
use Traversable;
use TypeError;
use UnderflowException;
use ValueError;

/**
 * @template T
 *
 * @implements ArrayAccess<int, T>
 * @implements Iterator<int, T>
 */
abstract class AbstractVec implements ArrayAccess, Countable, Iterator
{
    /** @var array<int, T> */
    protected array $vec = [];

    private int $position = 0;

    /**
     * Constructs a new strictly-typed vector.
     *
     * @param T ...$values
     */
    public function __construct(...$values)
    {
        $this->push(...$values);
    }

    /**
     * @inheritDoc
     */
    final public function count(): int
    {
        return count($this->vec);
    }

    /**
     * @inheritDoc
     *
     * @return T
     */
    final public function current()
    {
        return $this->vec[$this->position];
    }

    /**
     * @inheritDoc
     *
     * @return int
     */
    final public function key(): int
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    final public function next(): void
    {
        ++$this->position;
    }

    /**
     * @inheritDoc
     */
    final public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * @inheritDoc
     */
    final public function valid(): bool
    {
        return isset($this->vec[$this->position]);
    }

    /**
     * @inheritDoc
     *
     * @param int $offset
     */
    final public function offsetExists($offset): bool
    {
        $this->validateOffset($offset);
        return ($offset >= 0)
            ? isset($this->vec[$offset])
            : isset($this->vec[count($this->vec) + $offset]);
    }

    /**
     * @inheritDoc
     *
     * @param int $offset
     * @return ?T
     */
    final public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) { // (Also validates offset.)
            return null;
        }

        return ($offset >= 0)
            ? $this->vec[$offset]
            : $this->vec[count($this->vec) + $offset];
    }

    /**
     * @inheritDoc
     *
     * @param ?int $offset
     * @param T $value
     */
    final public function offsetSet($offset, $value): void
    {
        $this->validateValue($value);

        if (is_null($offset)) {
            $this->vec[] = $value;
        } elseif ($this->offsetExists($offset) || $offset ===  $this->count()) { // (Also validates offset.)
            $realOffset = ($offset >= 0)
                ? $offset
                : count($this->vec) + $offset;
            $this->vec[$realOffset] = $value;
        } else {
            throw new OutOfRangeException("Index does not exist");
        }
    }

    /**
     * @inheritDoc
     *
     * @param int $offset
     */
    final public function offsetUnset($offset): void
    {
        // Calling `offsetExists` validates the provided offset and prevents us
        // from reindexing needlessly.
        if ($this->offsetExists($offset)) {
            $realOffset = ($offset >= 0)
                ? $offset
                : count($this->vec) + $offset;
            unset($this->vec[$realOffset]);
            $this->vec = array_values($this->vec);
            if ($this->position >= $realOffset) {
                --$this->position;
            }
        }
    }

    /**
     * Returns a new vector containing only the values in this vector that do
     * not appear in any of the provided iterables.
     *
     * @param iterable<int, T> ...$iterables
     * @return static
     */
    final public function difference(iterable ...$iterables): AbstractVec
    {
        $params = [];
        foreach ($iterables as $iter) {
            if (is_array($iter)) {
                $params[] = $iter;
            } else {
                /** @var Traversable $iter */
                $params[] = iterator_to_array($iter);
            }
        }

        $new = clone $this;
        $new->vec = array_values(array_diff($this->vec, ...$params));
        $new->rewind();
        return $new;
    }

    /**
     * Inserts values at a given index. This works up-to-and-including the
     * number of items currently in the vector, i.e. the next available index.
     *
     * @param int $index
     * @param T ...$values
     * @throws OutOfRangeException
     */
    final public function insert(int $index, ...$values): void
    {
        $offset = ($index >= 0)
            ? $index
            : count($this->vec) + $index;
        if ($offset === count($this->vec)) {
            $this->push(...$values);
        } elseif ($offset === 0) {
            $this->unshift(...$values);
        } elseif ($this->offsetExists($offset)) { // (Also validates offset.)
            /** @psalm-var array<int, T> $values */
            foreach ($values as $value) {
                $this->validateValue($value);
            }

            $this->vec = array_merge(
                array_slice($this->vec, 0, $index),
                $values,
                array_slice($this->vec, $index)
            );
        } else {
            throw new OutOfRangeException("Index does not exist");
        }
    }

    /**
     * Returns a new vector containing only the values in this vector that
     * appear in all the provided iterables.
     *
     * @param iterable<int, T> ...$iterables
     * @return static
     */
    final public function intersection(iterable ...$iterables): AbstractVec
    {
        $params = [];
        foreach ($iterables as $iter) {
            if (is_array($iter)) {
                $params[] = $iter;
            } else {
                /** @var Traversable $iter */
                $params[] = iterator_to_array($iter);
            }
        }

        $new = clone $this;
        $new->vec = array_values(array_intersect($this->vec, ...$params));
        $new->rewind();
        return $new;
    }

    /**
     * Returns a new vector containing the result of appending all the values
     * from all the provided iterables to the end of the current vector.
     *
     * @param iterable<int, T> ...$iterables
     * @return static
     */
    final public function merge(iterable ...$iterables): AbstractVec
    {
        $new = clone $this;
        foreach ($iterables as $iter) {
            $new->push(...$iter);
        }
        $new->rewind();
        return $new;
    }

    /**
     * Removes and returns the value at the end of the vector.
     *
     * @return T
     * @throws UnderflowException
     */
    final public function pop()
    {
        $lastIndex = count($this->vec) - 1;
        if ($lastIndex === -1) {
            throw new UnderflowException("Vector is empty");
        }

        $lastValue = $this->vec[$lastIndex];
        $this->offsetUnset($lastIndex);
        return $lastValue;
    }

    /**
     * Appends values to the end of the vector.
     *
     * @param T ...$values
     */
    final public function push(...$values): void
    {
        /** @psalm-var array<int, T> $values */
        foreach ($values as $value) {
            $this->validateValue($value);
        }

        array_push($this->vec, ...$values);
    }

    /**
     * Removes and returns a value by index.
     *
     * @param int $index
     * @return T
     * @throws OutOfRangeException
     */
    final public function remove(int $index)
    {
        if (!$this->offsetExists($index)) {
            throw new OutOfRangeException("Index does not exist");
        }

        $value = $this->offsetGet($index);
        $this->offsetUnset($index);
        return $value;
    }

    /**
     * Removes and returns the value at the beginning of the vector.
     *
     * @return T
     * @throws UnderflowException
     */
    final public function shift()
    {
        if (count($this->vec) === 0) {
            throw new UnderflowException("Vector is empty");
        }

        $firstValue = $this->vec[0];
        $this->offsetUnset(0);
        return $firstValue;
    }

    /**
     * Returns a sub-vector of a given range.
     *
     * @param int $index
     * @param int|null $length
     * @return static
     */
    final public function slice(int $index, ?int $length = null): AbstractVec
    {
        $values = array_slice($this->vec, $index, $length, false);

        $clone = clone $this;
        $clone->vec = $values;
        $clone->position = 0;

        return $clone;
    }

    /**
     * Prepends values to the front of the vector.
     *
     * @param T ...$values
     */
    final public function unshift(...$values): void
    {
        /** @psalm-var array<int, T> $values */
        foreach ($values as $value) {
            $this->validateValue($value);
        }

        array_unshift($this->vec, ...$values);
    }

    /**
     * Enforces that an offset is a valid index for this vector.
     *
     * @param mixed $offset
     * @throws TypeError
     */
    final protected function validateOffset($offset): void
    {
        if (!is_int($offset)) {
            $msg = sprintf("Indexes for %s must be integers",
                static::class
            );
            throw new TypeError($msg);
        }
    }

    /**
     * Enforces that a value is a valid value for this vector. Throws a
     * `TypeError` if it is not the expected type. Throws a `ValueError` if
     * the value itself is invalid.
     *
     * @param mixed $value
     * @throws TypeError
     * @throws ValueError
     */
    protected function validateValue($value): void
    {
        if (!$this->validateValueType($value)) {
            $msg = sprintf("Invalid value type for %s",
                static::class
            );
            throw new TypeError($msg);
        }
    }

    /**
     * Determines whether the provided value is the expected type for this
     * vector.
     *
     * @param mixed $value
     * @return bool
     * @throws CompileError
     */
    public function validateValueType($value): bool
    {
        $msg = sprintf("Unimplemented `validateValueType` for %s",
            static::class
        );
        throw new CompileError($msg);
    }
}
