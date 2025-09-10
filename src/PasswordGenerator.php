<?php
declare(strict_types=1);

namespace Kristos80\PasswordGenerator;

use Random\RandomException;

final readonly class PasswordGenerator {

	private const MAX_CONSECUTIVE_AVOIDANCE_ATTEMPTS = 5;

	/**
	 * @param PasswordGeneratorConfig $generatorConfig
	 * @return string
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	public function generate(PasswordGeneratorConfig $generatorConfig): string {
		$password = [];

		$characterCounts = $this->calculateCharacterCounts($generatorConfig);
		$this->populatePassword($password, $generatorConfig, $characterCounts);

		shuffle($password);
		$password = $this->avoidConsecutiveCharacters($password);
		$this->ensureAlphabeticStart($password, $generatorConfig, $characterCounts);

		return implode("", $password);
	}

	/**
	 * @param PasswordGeneratorConfig $config
	 * @return array
	 * @throws RandomException
	 */
	private function calculateCharacterCounts(PasswordGeneratorConfig $config): array {
		return [
			'lowercase' => random_int($config->lowercase->min, $config->lowercase->max),
			'uppercase' => random_int($config->uppercase->min, $config->uppercase->max),
			'numbers' => random_int($config->numbers->min, $config->numbers->max),
			'symbols' => random_int($config->symbols->min, $config->symbols->max),
		];
	}

	/**
	 * @param array $password
	 * @param PasswordGeneratorConfig $config
	 * @param array $characterCounts
	 * @return void
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	private function populatePassword(array &$password, PasswordGeneratorConfig $config, array $characterCounts): void {
		$this->addCharacters($password, PoolType::CHARACTERS, $characterCounts['lowercase'], $config->doNotUse);
		$this->addCharacters($password, PoolType::CHARACTERS, $characterCounts['uppercase'], $config->doNotUse, true);
		$this->addCharacters($password, PoolType::NUMBERS, $characterCounts['numbers'], $config->doNotUse);
		$this->addCharacters($password, PoolType::SYMBOLS, $characterCounts['symbols'], $config->doNotUse);
	}

	/**
	 * @param array $password
	 * @param PasswordGeneratorConfig $config
	 * @param array $characterCounts
	 * @return void
	 */
	private function ensureAlphabeticStart(array &$password, PasswordGeneratorConfig $config, array $characterCounts): void {
		if($config->alwaysStartWithCharacter && ($characterCounts['lowercase'] > 0 || $characterCounts['uppercase'] > 0)) {
			foreach($password as $index => $char) {
				if(ctype_alpha($char)) {
					if($index !== 0) {
						$this->swapArrayElements($password, 0, $index);
					}
					break;
				}
			}
		}
	}

	/**
	 * @param array $password
	 * @param PoolType $poolType
	 * @param int $count
	 * @param array $doNotUse
	 * @param bool $uppercase
	 * @return void
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	private function addCharacters(array &$password, PoolType $poolType, int $count, array $doNotUse, bool $uppercase = false): void {
		for($i = 0; $i < $count; $i++) {
			$char = $this->pickRandom($poolType, $doNotUse);
			$password[] = $uppercase ? strtoupper($char) : $char;
		}
	}

	/**
	 * @param PoolType $poolType
	 * @param array $doNotUse
	 * @return string
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	private function pickRandom(PoolType $poolType, array $doNotUse): string {
		$pool = match ($poolType) {
			PoolType::NUMBERS => CharacterPool::NUMBERS->getPool(),
			PoolType::SYMBOLS => CharacterPool::SYMBOLS->getPool(),
			default => CharacterPool::CHARACTERS->getPool(),
		};

		$pool = str_split($pool);
		$pool = array_filter($pool, fn($char) => !in_array(strtolower($char), array_map("strtolower", $doNotUse)));
		$pool = array_values($pool);

		if(!count($pool)) {
			throw new EmptyPoolException("The pool '{$poolType->value}' is empty");
		}

		return $pool[random_int(0, count($pool) - 1)];
	}

	/**
	 * @param array $array
	 * @param int $index1
	 * @param int $index2
	 * @return void
	 */
	private function swapArrayElements(array &$array, int $index1, int $index2): void {
		[$array[$index1], $array[$index2]] = [$array[$index2], $array[$index1]];
	}

	/**
	 * @param array $password
	 * @return array
	 */
	private function avoidConsecutiveCharacters(array $password): array {
		$attempts = 0;

		while($attempts < self::MAX_CONSECUTIVE_AVOIDANCE_ATTEMPTS) {
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
							$this->swapArrayElements($password, $i, $j);
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