<?php
declare(strict_types=1);

use Prewk\Result\{Ok, Err};
use ebeacon\strictvec\Vec;

class VecTest extends PHPUnit\Framework\TestCase
{
    public function testAllIntegers(): void
    {
        $vec = new Vec(1337, 0xdeadbeef, 0);

        $this->assertSame(1337, $vec[0]);
        $this->assertSame(0xdeadbeef, $vec[1]);
        $this->assertSame(0, $vec[2]);
    }

    public function testAllStrings(): void
    {
        $vec = new Vec(
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
        $vec = new Vec(...$strings);

        $this->assertSame("asdf", $vec[0]);
        $this->assertSame("qwerty", $vec[1]);
        $this->assertSame("test", $vec[2]);
    }

    public function testNullFirst(): void
    {
        $values = [null, 5678];

        $this->expectException(\TypeError::class);
        $_ = new Vec(...$values);
    }

    public function testNullSecond(): void
    {
        $values = [5678, null];

        $this->expectException(\TypeError::class);
        $_ = new Vec(...$values);
    }

    public function testClass(): void
    {
        $vec = new Vec(
            new Ok(1),
            new Ok(2),
            new Ok(4)
        );

        $this->assertCount(3, $vec);

        $this->expectException(\TypeError::class);
        $vec[] = new Err("whoops");
    }

    public function testBadType(): void
    {
        $vec = new Vec(3);
        $this->assertSame(3, $vec[0]);

        $this->expectException(\TypeError::class);
        $vec[] = "moo";
    }
}

