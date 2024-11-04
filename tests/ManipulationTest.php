<?php
declare(strict_types=1);

use Prewk\Result\{Ok, Err};
use ebeacon\strictvec\Result\ResultVec;
use ebeacon\strictvec\Scalar\{StringVec, UIntVec};

class ManipulationTest extends PHPUnit\Framework\TestCase
{
    public function testDifference(): void
    {
        $vec = new UIntVec(1, 2, 3, 4, 5);
        $vecTwo = new UIntVec(1, 2, 5);
        $vecThree = new UIntVec(1, 2, 3);

        $diff = $vec->difference($vecTwo);
        $this->assertCount(2, $diff);
        $this->assertSame(3, $diff[0]);
        $this->assertSame(4, $diff[1]);

        $diff = $vecTwo->difference($vec);
        $this->assertCount(0, $diff);

        $diff = $vecTwo->difference($vecThree);
        $this->assertCount(1, $diff);
        $this->assertSame(5, $diff[0]);

        $diff = $vecThree->difference($vecTwo);
        $this->assertCount(1, $diff);
        $this->assertSame(3, $diff[0]);

        $diff = $vec->difference($vecTwo, $vecThree);
        $this->assertCount(1, $diff);
        $this->assertSame(4, $diff[0]);
    }

    public function testInsert(): void
    {
        $vec = new StringVec("q", "w", "t", "y");
        $vec->insert(2, ...["e", "r"]);

        $this->assertCount(6, $vec);
        $this->assertSame("q", $vec[0]);
        $this->assertSame("w", $vec[1]);
        $this->assertSame("e", $vec[2]);
        $this->assertSame("r", $vec[3]);
        $this->assertSame("t", $vec[4]);
        $this->assertSame("y", $vec[5]);
    }

    public function testInsertAtBadIndex(): void
    {
        $vec = new StringVec("just", "some", "data");

        $this->expectException(\OutOfRangeException::class);
        $vec->insert(4, "!");
    }

    public function testInsertAtCount(): void
    {
        $vec = new UIntVec(1, 2, 3);
        $vec->insert(3, 4, 5, 6);

        $this->assertCount(6, $vec);
        $this->assertSame(1, $vec[0]);
        $this->assertSame(2, $vec[1]);
        $this->assertSame(3, $vec[2]);
        $this->assertSame(4, $vec[3]);
        $this->assertSame(5, $vec[4]);
        $this->assertSame(6, $vec[5]);
    }

    public function testInsertAtNegativeIndex(): void
    {
        $vec = new UIntVec(1, 3, 7);
        $vec->insert(-1, 3);

        $this->assertCount(4, $vec);
        $this->assertSame(1, $vec[0]);
        $this->assertSame(3, $vec[1]);
        $this->assertSame(3, $vec[2]);
        $this->assertSame(7, $vec[3]);
    }

    public function testInsertAtZero(): void
    {
        $vec = new UIntVec(4, 5, 6);
        $vec->insert(0, 1, 2, 3);

        $this->assertCount(6, $vec);
        $this->assertSame(1, $vec[0]);
        $this->assertSame(2, $vec[1]);
        $this->assertSame(3, $vec[2]);
        $this->assertSame(4, $vec[3]);
        $this->assertSame(5, $vec[4]);
        $this->assertSame(6, $vec[5]);
    }

    public function testIntersection(): void
    {
        $vec = new UIntVec(1, 2, 3, 4, 5);
        $vecTwo = new UIntVec(1, 2, 5);
        $vecThree = new UIntVec(1, 2, 3);

        $same = $vec->intersection($vecTwo);
        $this->assertCount(3, $same);
        $this->assertSame(1, $same[0]);
        $this->assertSame(2, $same[1]);
        $this->assertSame(5, $same[2]);

        $same = $vecTwo->intersection($vec);
        $this->assertCount(3, $same);
        $this->assertSame(1, $same[0]);
        $this->assertSame(2, $same[1]);
        $this->assertSame(5, $same[2]);

        $same = $vecTwo->intersection($vecThree);
        $this->assertCount(2, $same);
        $this->assertSame(1, $same[0]);
        $this->assertSame(2, $same[1]);

        $same = $vecThree->intersection($vecTwo);
        $this->assertCount(2, $same);
        $this->assertSame(1, $same[0]);
        $this->assertSame(2, $same[1]);

        $same = $vec->intersection($vecTwo, $vecThree);
        $this->assertCount(2, $same);
        $this->assertSame(1, $same[0]);
        $this->assertSame(2, $same[1]);
    }

    public function testMerge(): void
    {
        $vec = new StringVec("scooby", "dooby", "doo");
        $vecTwo = new StringVec("where", "are", "you");
        $vecThree = new StringVec("we", "got", "some", "work",
                                  "to", "do", "now");

        $this->assertCount(3, $vec);
        $this->assertCount(3, $vecTwo);
        $this->assertCount(7, $vecThree);

        $merged = $vec->merge($vecTwo);
        $this->assertCount(3, $vec);
        $this->assertCount(3, $vecTwo);
        $this->assertCount(6, $merged);
        $this->assertSame("scooby", $merged[0]);
        $this->assertSame("dooby", $merged[1]);
        $this->assertSame("doo", $merged[2]);
        $this->assertSame("where", $merged[3]);
        $this->assertSame("are", $merged[4]);
        $this->assertSame("you", $merged[5]);

        $merged = $vec->merge($vecTwo, $vecThree);
        $this->assertCount(3, $vec);
        $this->assertCount(3, $vecTwo);
        $this->assertCount(7, $vecThree);
        $this->assertCount(13, $merged);
        $this->assertSame("scooby", $merged[0]);
        $this->assertSame("dooby", $merged[1]);
        $this->assertSame("doo", $merged[2]);
        $this->assertSame("where", $merged[3]);
        $this->assertSame("are", $merged[4]);
        $this->assertSame("you", $merged[5]);
        $this->assertSame("we", $merged[6]);
        $this->assertSame("got", $merged[7]);
        $this->assertSame("some", $merged[8]);
        $this->assertSame("work", $merged[9]);
        $this->assertSame("to", $merged[10]);
        $this->assertSame("do", $merged[11]);
        $this->assertSame("now", $merged[12]);
    }

    public function testPopEmpty(): void
    {
        $vec = new UIntVec();

        $this->expectException(\UnderflowException::class);
        $_ = $vec->pop();
    }

    public function testPopObject(): void
    {
        $vec = new ResultVec(
            new Ok("hooray"),
            new Ok("yippee"),
            new Err("uh-oh"),
            new Ok("yahoo")
        );
        $last = $vec->pop();

        $this->assertCount(3, $vec);
        $this->assertTrue($vec[0]->isOk());
        $this->assertSame("hooray", $vec[0]->unwrap());
        $this->assertTrue($vec[1]->isOk());
        $this->assertSame("yippee", $vec[1]->unwrap());
        $this->assertTrue($vec[2]->isErr());
        $this->assertSame("uh-oh", $vec[2]->unwrapErr());
        $this->assertFalse(isset($vec[3]));
        $this->assertTrue($last->isOk());
        $this->assertSame("yahoo", $last->unwrap());
    }

    public function testPopScalar(): void
    {
        $vec = new UIntVec(1, 2, 3);
        $last = $vec->pop();

        $this->assertCount(2, $vec);
        $this->assertSame(1, $vec[0]);
        $this->assertSame(2, $vec[1]);
        $this->assertFalse(isset($vec[2]));
        $this->assertSame(3, $last);
    }

    public function testPush(): void
    {
        $vec = new UIntVec(1, 2, 3);
        $vec->push(4, 5, 6);

        $this->assertCount(6, $vec);
        $this->assertSame(1, $vec[0]);
        $this->assertSame(2, $vec[1]);
        $this->assertSame(3, $vec[2]);
        $this->assertSame(4, $vec[3]);
        $this->assertSame(5, $vec[4]);
        $this->assertSame(6, $vec[5]);
    }

    public function testRemoveAtBadIndex(): void
    {
        $vec = new StringVec("just", "some", "data");

        $this->expectException(\OutOfRangeException::class);
        $_ = $vec->remove(4);
    }

    public function testRemoveObject(): void
    {
        $vec = new ResultVec(
            new Ok(1),
            new Ok(2),
            new Ok(4),
            new Ok(8)
        );

        /** @var Ok $value */
        $value = $vec->remove(2);

        $this->assertCount(3, $vec);
        $this->assertSame(4, $value->unwrap());
    }

    public function testRemoveScalar(): void
    {
        $vec = new UIntVec(1, 2, 4, 8);
        $value = $vec->remove(2);

        $this->assertCount(3, $vec);
        $this->assertSame(4, $value);
    }

    public function testShiftEmpty(): void
    {
        $vec = new UIntVec();

        $this->expectException(\UnderflowException::class);
        $_ = $vec->shift();
    }

    public function testShiftObject(): void
    {
        $vec = new ResultVec(
            new Ok("hooray"),
            new Ok("yippee"),
            new Err("uh-oh"),
            new Ok("yahoo")
        );
        $first = $vec->shift();

        $this->assertCount(3, $vec);
        $this->assertTrue($vec[0]->isOk());
        $this->assertSame("yippee", $vec[0]->unwrap());
        $this->assertTrue($vec[1]->isErr());
        $this->assertSame("uh-oh", $vec[1]->unwrapErr());
        $this->assertTrue($vec[2]->isOk());
        $this->assertSame("yahoo", $vec[2]->unwrap());
        $this->assertFalse(isset($vec[3]));
        $this->assertTrue($first->isOk());
        $this->assertSame("hooray", $first->unwrap());
    }

    public function testShiftScalar(): void
    {
        $vec = new UIntVec(1, 2, 3);
        $first = $vec->shift();

        $this->assertCount(2, $vec);
        $this->assertSame(2, $vec[0]);
        $this->assertSame(3, $vec[1]);
        $this->assertFalse(isset($vec[2]));
        $this->assertSame(1, $first);
    }

    public function testSlice(): void
    {
        $vec = new UIntVec(1, 2, 4, 8, 16, 32, 64, 128);

        $slice = $vec->slice(4, 2);
        $this->assertCount(8, $vec);
        $this->assertCount(2, $slice);
        $this->assertSame(get_class($vec), get_class($slice));
        $this->assertSame(16, $slice[0]);
        $this->assertSame(32, $slice[1]);

        $slice = $vec->slice(6);
        $this->assertCount(8, $vec);
        $this->assertCount(2, $slice);
        $this->assertSame(get_class($vec), get_class($slice));
        $this->assertSame(64, $slice[0]);
        $this->assertSame(128, $slice[1]);

        $slice = $vec->slice(-2);
        $this->assertCount(8, $vec);
        $this->assertCount(2, $slice);
        $this->assertSame(get_class($vec), get_class($slice));
        $this->assertSame(64, $slice[0]);
        $this->assertSame(128, $slice[1]);

        $slice = $vec->slice(4, -2);
        $this->assertCount(8, $vec);
        $this->assertCount(2, $slice);
        $this->assertSame(get_class($vec), get_class($slice));
        $this->assertSame(16, $slice[0]);
        $this->assertSame(32, $slice[1]);
    }

    public function testUnshift(): void
    {
        $vec = new UIntVec(1, 2, 3);
        $vec->unshift(4, 5, 6);

        $this->assertCount(6, $vec);
        $this->assertSame(4, $vec[0]);
        $this->assertSame(5, $vec[1]);
        $this->assertSame(6, $vec[2]);
        $this->assertSame(1, $vec[3]);
        $this->assertSame(2, $vec[4]);
        $this->assertSame(3, $vec[5]);
    }
}
