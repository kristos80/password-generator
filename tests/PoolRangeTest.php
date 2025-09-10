<?php
declare(strict_types=1);

namespace Kristos80\PasswordGenerator\Tests;

use InvalidArgumentException;
use Kristos80\PasswordGenerator\PoolRange;

final class PoolRangeTest extends TestCase {

	public function testValidPoolRange(): void {
		$range = new PoolRange(1, 5);
		
		$this->assertEquals(1, $range->min);
		$this->assertEquals(5, $range->max);
	}

	public function testEqualMinMax(): void {
		$range = new PoolRange(3, 3);
		
		$this->assertEquals(3, $range->min);
		$this->assertEquals(3, $range->max);
	}

	public function testZeroMin(): void {
		$range = new PoolRange(0, 2);
		
		$this->assertEquals(0, $range->min);
		$this->assertEquals(2, $range->max);
	}

	public function testInvalidNegativeMin(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Invalid PoolRange: min must be >= 0 and max must be >= min.");
		
		new PoolRange(-1, 5);
	}

	public function testInvalidMaxLessThanMin(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Invalid PoolRange: min must be >= 0 and max must be >= min.");
		
		new PoolRange(5, 3);
	}

	public function testInvalidBothNegative(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Invalid PoolRange: min must be >= 0 and max must be >= min.");
		
		new PoolRange(-2, -1);
	}
}