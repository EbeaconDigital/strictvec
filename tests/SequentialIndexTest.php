<?php
declare(strict_types=1);

use ebeacon\strictvec\Vec;

class SequentialIndexTest extends PHPUnit\Framework\TestCase
{
    public function testIndexing(): void
    {
        /** @psalm-var Vec<int> $vec */
        $vec = new Vec(1, 2, 3);

        $this->assertCount(3, $vec);

        $i = 0;
        foreach ($vec as $k => $_) {
            $this->assertSame($i, $k);
            $i++;
        }
        $this->assertSame(1, $vec[0]);
        $this->assertSame(2, $vec[1]);
        $this->assertSame(3, $vec[2]);

        $vec[] = 4;
        $this->assertCount(4, $vec);
        $this->assertSame(4, $vec[3]);

        unset($vec[2]);
        $this->assertCount(3, $vec);
        $this->assertSame(1, $vec[0]);
        $this->assertSame(2, $vec[1]);
        $this->assertSame(4, $vec[2]);

        $vec[] = 8;
        $this->assertCount(4, $vec);
        $this->assertSame(8, $vec[3]);

        foreach ($vec as $k => $_) {
            // Because of the unset below, the key should never make it past 1.
            $this->assertContains($k, [0, 1]);

            if ($k % 2 !== 0) {
                unset($vec[$k]);
            }
        }

        $this->assertCount(1, $vec);

        $vec[] = 2;
        $vec[] = 4;
        $vec[] = 8;

        $this->assertCount(4, $vec);

        $i = 0;
        foreach ($vec as $k => $_) {
            // Because of the unset below, the key should never make it past 2.
            $this->assertContains($k, [0, 1, 2]);
            $this->assertSame($i, $k);
            $i++;

            unset($vec[$k + 3]);
        }

        $this->assertCount(3, $vec);
        $this->assertSame(1, $vec[0]);
        $this->assertSame(2, $vec[1]);
        $this->assertSame(4, $vec[2]);

        $this->assertNull($vec[3]);

        // $offset === count($vec) should be permissible as an alternative to
        // $vec[] = ..., to facilitate copying from one collection to another
        // using generators (yield $key => $value)
        $vec[count($vec)] = 8;
        $this->assertCount(4, $vec);
        $this->assertSame(1, $vec[0]);
        $this->assertSame(2, $vec[1]);
        $this->assertSame(4, $vec[2]);
        $this->assertSame(8, $vec[3]);

        // Any offset higher than the current count should throw an exception!
        $this->expectException(\OutOfRangeException::class);
        $vec[count($vec) + 1]  = 16;
    }
}
