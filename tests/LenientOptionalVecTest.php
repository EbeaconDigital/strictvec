<?php
declare(strict_types=1);

use Prewk\Result\{Ok, Err};
use ebeacon\strictvec\LenientOptionalVec;

class LenientOptionalVecTest extends PHPUnit\Framework\TestCase
{
    public function testAllIntegers(): void
    {
        $vec = new LenientOptionalVec(1337, 0xdeadbeef, 0);

        $this->assertSame(1337, $vec[0]);
        $this->assertSame(0xdeadbeef, $vec[1]);
        $this->assertSame(0, $vec[2]);
    }

    public function testAllStrings(): void
    {
        $vec = new LenientOptionalVec(
            "asdf",
            "qwerty",
            "test"
        );

        $this->assertSame("asdf", $vec[0]);
        $this->assertSame("qwerty", $vec[1]);
        $this->assertSame("test", $vec[2]);
    }

    public function testFromStringArray(): void
    {
        $strings = ["asdf", "qwerty", "test"];
        $vec = new LenientOptionalVec(...$strings);

        $this->assertSame("asdf", $vec[0]);
        $this->assertSame("qwerty", $vec[1]);
        $this->assertSame("test", $vec[2]);
    }

    public function testNullFirst(): void
    {
        $values = [null, 5678];

        $vec = new LenientOptionalVec(...$values);

        $this->assertNull($vec[0]);
        $this->assertSame(5678, $vec[1]);
    }

    public function testNullSecond(): void
    {
        $values = [5678, null];

        $vec = new LenientOptionalVec(...$values);

        $this->assertSame(5678, $vec[0]);
        $this->assertNull($vec[1]);
    }

    public function testClass(): void
    {
        /** @psalm-var LenientOptionalVec<Prewk\Result> $vec */
        $vec = new LenientOptionalVec(
            new Ok(1),
            new Ok(2),
            new Ok(4)
        );

        $this->assertCount(3, $vec);

        $vec[] = null;

        $this->assertCount(4, $vec);
        $this->assertNull($vec[3]);

        $vec[] = new Err("whoops");
        $this->assertCount(5, $vec);

        $this->expectException(\TypeError::class);
        $vec[] = new stdClass();
    }

    public function testBadType(): void
    {
        /** @psalm-var LenientOptionalVec<int> $vec */
        $vec = new LenientOptionalVec(3);
        $this->assertSame(3, $vec[0]);

        $this->expectException(\TypeError::class);
        $vec[] = "moo";
    }
}

