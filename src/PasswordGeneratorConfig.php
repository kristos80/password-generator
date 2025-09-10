<?php
declare(strict_types=1);

namespace Kristos80\PasswordGenerator;

final readonly class PasswordGeneratorConfig {

	/**
	 * @param PoolRange $lowercase
	 * @param PoolRange $uppercase
	 * @param PoolRange $numbers
	 * @param PoolRange $symbols
	 * @param bool $alwaysStartWithCharacter
	 * @param array $doNotUse
	 */
	public function __construct(
		public PoolRange $lowercase = new PoolRange(4, 4),
		public PoolRange $uppercase = new PoolRange(2, 2),
		public PoolRange $numbers = new PoolRange(1, 1),
		public PoolRange $symbols = new PoolRange(1, 1),
		public bool $alwaysStartWithCharacter = FALSE,
		public array $doNotUse = [],
	) {}


	/**
	 * @return int
	 */
	public function getTotalMinCharacters(): int {
		return $this->lowercase->min + $this->uppercase->min + $this->numbers->min + $this->symbols->min;
	}

	/**
	 * @return int
	 */
	public function getTotalMaxCharacters(): int {
		return $this->lowercase->max + $this->uppercase->max + $this->numbers->max + $this->symbols->max;
	}
}
