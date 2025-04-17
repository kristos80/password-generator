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
		private PoolRange $lowercase = new PoolRange(4, 4),
		private PoolRange $uppercase = new PoolRange(2, 2),
		private PoolRange $numbers = new PoolRange(1, 1),
		private PoolRange $symbols = new PoolRange(1, 1),
		private bool $alwaysStartWithCharacter = FALSE,
		private array $doNotUse = [],
	) {}

	/**
	 * @return PoolRange
	 */
	public function getLowercaseRange(): PoolRange {
		return $this->lowercase;
	}

	/**
	 * @return PoolRange
	 */
	public function getUppercaseRange(): PoolRange {
		return $this->uppercase;
	}

	/**
	 * @return PoolRange
	 */
	public function getNumbersRange(): PoolRange {
		return $this->numbers;
	}

	/**
	 * @return PoolRange
	 */
	public function getSymbolsRange(): PoolRange {
		return $this->symbols;
	}

	/**
	 * @return bool
	 */
	public function getAlwaysStartWithCharacter(): bool {
		return $this->alwaysStartWithCharacter;
	}

	/**
	 * @return array
	 */
	public function getDoNotUse(): array {
		return $this->doNotUse;
	}

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
