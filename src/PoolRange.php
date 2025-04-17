<?php
declare(strict_types=1);

namespace Kristos80\PasswordGenerator;

use InvalidArgumentException;

final readonly class PoolRange {

	/**
	 * @param int $min
	 * @param int $max
	 */
	public function __construct(
		public int $min,
		public int $max,
	) {
		if($min < 0 || $max < $min) {
			throw new InvalidArgumentException("Invalid PoolRange: min must be >= 0 and max must be >= min.");
		}
	}
}