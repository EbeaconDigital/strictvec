<?php
declare(strict_types=1);

use ebeacon\strictvec\Scalar\StringVec;

class StringVecTest extends PHPUnit\Framework\TestCase
{
    public function testAllStrings(): void
    {
        $vec = new StringVec(
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
        $vec = new StringVec(...$strings);

        $this->assertSame("asdf", $vec[0]);
        $this->assertSame("qwerty", $vec[1]);
        $this->assertSame("test", $vec[2]);
    }

    public function testBadType(): void
    {
        $this->expectException(\TypeError::class);
        $_ = new StringVec(3);
    }
}
