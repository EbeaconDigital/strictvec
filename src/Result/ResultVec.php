<?php
declare(strict_types=1);

namespace ebeacon\strictvec\Result;

use Prewk\Result;
use ebeacon\iterutil\IterUtil;
use ebeacon\strictvec\AbstractVec;
use ebeacon\strictvec\Traits\LenientObjectValueType;

/**
 * @extends AbstractVec<Result>
 */
class ResultVec extends AbstractVec
{
    /** @use LenientObjectValueType<Result> */
    use LenientObjectValueType;

    /**
     * @inheritDoc
     */
    public function getValueType(): string
    {
        return Result::class;
    }

    /**
     * Return an `IterUtil` iterator traversing any `Err` items in this
     * `ResultVec`.
     *
     * @return IterUtil
     */
    public function getErrs(): IterUtil
    {
        return IterUtil::from($this->vec)
            ->filter(fn(Result $item): bool => $item->isErr());
    }

    /**
     * Return an `IterUtil` iterator traversing any `Ok` items in this
     * `ResultVec`.
     *
     * @return IterUtil
     */
    public function getOks(): IterUtil
    {
        return IterUtil::from($this->vec)
            ->filter(fn(Result $item): bool => $item->isOk());
    }

    /**
     * Return whether any `Err` items are present in this `ResultVec`.
     *
     * @return bool
     */
    public function hasErrs(): bool
    {
        return IterUtil::from($this->vec)
            ->map(fn(Result $item): bool => $item->isErr())
            ->any();
    }
}
