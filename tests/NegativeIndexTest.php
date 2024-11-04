<?php
declare(strict_types=1);

use ebeacon\strictvec\Vec;

class NegativeIndexTest extends PHPUnit\Framework\TestCase
{
    public function testNegativeIndexAccess(): void
    {
        $vec = new Vec(19567, 21541, 80);

        $this->assertSame(19567, $vec[0]);
        $this->assertSame(21541, $vec[1]);
        $this->assertSame(80, $vec[2]);

        $this->assertSame(19567, $vec[-3]);
        $this->assertSame(21541, $vec[-2]);
        $this->assertSame(80, $vec[-1]);
    }

    public function testEmptyArrayAccess(): void
    {
        $vec = new Vec();

        $this->assertNull($vec[-1]);
    }

    public function testNegativeIndexWrite(): void
    {
        /** @psalm-var Vec<int> $vec */
        $vec = new Vec(19567, 21541, 80);

        $vec[-1] = 8000;

        $this->assertSame(8000, $vec[2]);
        $this->assertSame(8000, $vec[-1]);

        $this->expectException(\OutOfRangeException::class);
        $vec[-4] = 1337;
    }

    public function testNegativeIndexUnset(): void
    {
        $vec = new Vec(19567, 21541, 80);

        unset($vec[-2]);

        $this->assertSame(19567, $vec[0]);
        $this->assertSame(80, $vec[1]);

        $this->assertSame(19567, $vec[-2]);
        $this->assertSame(80, $vec[-1]);
    }

    public function testNegativeIndexTooLarge(): void
    {
        $vec = new Vec(19567, 21541, 80);

        $this->assertNull($vec[-4]);
    }
}
