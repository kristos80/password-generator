<?php
declare(strict_types=1);

namespace Kristos80\PasswordGenerator;

use Random\RandomException;

final readonly class PasswordGenerator {

	/**
	 *
	 */
	private const CHARACTERS = "abcdefghijklmnopqrstuvwxyz";

	/**
	 *
	 */
	private const NUMBERS = "0123456789";

	/**
	 *
	 */
	private const SYMBOLS = "!@#$%^&*()-_=+[]{}|;:,.<>?";

	/**
	 *
	 */
	private const POOL_CHARACTERS = "characters";

	/**
	 *
	 */
	private const POOL_NUMBERS = "numbers";

	/**
	 *
	 */
	private const POOL_SYMBOLS = "symbols";

	/**
	 * @param PasswordGeneratorConfig $generatorConfig
	 * @return string
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	public function generate(PasswordGeneratorConfig $generatorConfig): string {
		$password = [];

		$lowerCount = random_int(
			$generatorConfig->getLowercaseRange()->min,
			$generatorConfig->getLowercaseRange()->max,
		);

		$upperCount = random_int(
			$generatorConfig->getUppercaseRange()->min,
			$generatorConfig->getUppercaseRange()->max,
		);

		$numberCount = random_int(
			$generatorConfig->getNumbersRange()->min,
			$generatorConfig->getNumbersRange()->max,
		);

		$symbolCount = random_int(
			$generatorConfig->getSymbolsRange()->min,
			$generatorConfig->getSymbolsRange()->max,
		);

		for($i = 0; $i < $lowerCount; $i++) {
			$password[] = $this->pickRandom(self::POOL_CHARACTERS, $generatorConfig->getDoNotUse());
		}

		for($i = 0; $i < $upperCount; $i++) {
			$password[] = strtoupper($this->pickRandom(self::POOL_CHARACTERS, $generatorConfig->getDoNotUse()));
		}

		for($i = 0; $i < $numberCount; $i++) {
			$password[] = $this->pickRandom(self::POOL_NUMBERS, $generatorConfig->getDoNotUse());
		}

		for($i = 0; $i < $symbolCount; $i++) {
			$password[] = $this->pickRandom(self::POOL_SYMBOLS, $generatorConfig->getDoNotUse());
		}

		shuffle($password);

		if($generatorConfig->getAlwaysStartWithCharacter() &&
			($lowerCount > 0 || $upperCount > 0)) {
			foreach($password as $index => $char) {
				if(ctype_alpha($char)) {
					if($index !== 0) {
						[
							$password[0],
							$password[$index],
						] =
							[
								$char,
								$password[0],
							];
					}

					break;
				}
			}
		}

		$password = $this->avoidConsecutiveCharacters($password);

		return implode("", $password);
	}

	/**
	 * @param string $poolMode
	 * @param array $doNotUse
	 * @return string
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	private function pickRandom(string $poolMode, array $doNotUse): string {
		$pool = match ($poolMode) {
			self::POOL_NUMBERS => self::NUMBERS,
			self::POOL_SYMBOLS => self::SYMBOLS,
			default => self::CHARACTERS,
		};

		$pool = str_split($pool);
		$pool = array_filter($pool, fn($char) => !in_array(strtolower($char), array_map("strtolower", $doNotUse)));
		$pool = array_values($pool);

		if(!count($pool)) {
			throw new EmptyPoolException("The pool '$poolMode' is empty");
		}

		return $pool[random_int(0, count($pool) - 1)];
	}

	/**
	 * @param array $password
	 * @return array
	 */
	private function avoidConsecutiveCharacters(array $password): array {
		$attempts = 0;

		while($attempts < 5) {
			$hasConsecutive = FALSE;

			for($i = 1; $i < count($password); $i++) {
				if($password[$i] === $password[$i - 1]) {
					$hasConsecutive = TRUE;

					for($j = 0; $j < count($password); $j++) {
						if(
							$j !== $i &&
							$password[$j] !== $password[$i] &&
							$password[$j] !== $password[$i - 1]
						) {
							[
								$password[$i],
								$password[$j],
							] =
								[
									$password[$j],
									$password[$i],
								];
							break;
						}
					}
				}
			}

			if(!$hasConsecutive) {
				break;
			}

			$attempts++;
		}

		return $password;
	}
}