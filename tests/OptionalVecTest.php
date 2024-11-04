<?php
declare(strict_types=1);

use ebeacon\strictvec\OptionalVec;

class OptionalVecTest extends PHPUnit\Framework\TestCase
{
    public function testAllIntegers(): void
    {
        $vec = new OptionalVec(1337, 0xdeadbeef, 0);

        $this->assertSame(1337, $vec[0]);
        $this->assertSame(0xdeadbeef, $vec[1]);
        $this->assertSame(0, $vec[2]);
    }

    public function testAllStrings(): void
    {
        $vec = new OptionalVec(
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
        $vec = new OptionalVec(...$strings);

        $this->assertSame("asdf", $vec[0]);
        $this->assertSame("qwerty", $vec[1]);
        $this->assertSame("test", $vec[2]);
    }

    public function testNullFirst(): void
    {
        $values = [null, 5678];

        $vec = new OptionalVec(...$values);

        $this->assertNull($vec[0]);
        $this->assertSame(5678, $vec[1]);
    }

    public function testNullSecond(): void
    {
        $values = [5678, null];

        $vec = new OptionalVec(...$values);

        $this->assertSame(5678, $vec[0]);
        $this->assertNull($vec[1]);
    }

    public function testBadType(): void
    {
        $vec = new OptionalVec(3);
        $this->assertSame(3, $vec[0]);

        $this->expectException(\TypeError::class);
        $vec[] = "moo";
    }
}

