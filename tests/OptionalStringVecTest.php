<?php
declare(strict_types=1);

use ebeacon\strictvec\OptionalScalar\OptionalStringVec;

class OptionalStringVecTest extends PHPUnit\Framework\TestCase
{
    public function testAllStrings(): void
    {
        $vec = new OptionalStringVec(
            "asdf",
            null,
            "test"
        );

        $this->assertSame("asdf", $vec[0]);
        $this->assertNull($vec[1]);
        $this->assertSame("test", $vec[2]);
    }

    public function testFromStringArray(): void
    {
        $strings = ["asdf", null, "test"];
        $vec = new OptionalStringVec(...$strings);

        $this->assertSame("asdf", $vec[0]);
        $this->assertNull($vec[1]);
        $this->assertSame("test", $vec[2]);
    }

    public function testBadType(): void
    {
        $this->expectException(\TypeError::class);
        $_ = new OptionalStringVec(3);
    }
}
