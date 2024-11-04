<?php
declare(strict_types=1);

use ebeacon\strictvec\Result\ResultVec;

use Prewk\Result\{Ok, Err};

class ResultVecTest extends PHPUnit\Framework\TestCase
{
    public function testFromResultArray(): void
    {
        $results = [new Ok(null), new Err("bonk"), new Ok(7)];
        $vec = new ResultVec(...$results);

        $this->assertTrue($vec[0]->isOk());
        $this->assertNull($vec[0]->unwrap());
        $this->assertTrue($vec[1]->isErr());
        $this->assertSame("bonk", $vec[1]->unwrapErr());
        $this->assertTrue($vec[2]->isOk());
        $this->assertSame(7, $vec[2]->unwrap());
        $this->assertTrue($vec->hasErrs());
        $this->assertSame(1, count($vec->getErrs()->collect()));
        $this->assertSame(2, count($vec->getOks()->collect()));
    }

    public function testBadType(): void
    {
        $this->expectException(\TypeError::class);
        $_ = new ResultVec(3);
    }
}
