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

		$filteredPools = $this->buildFilteredPools($generatorConfig->doNotUse);
		$characterCounts = $this->calculateCharacterCounts($generatorConfig);
		$this->populatePassword($password, $filteredPools, $characterCounts);

		shuffle($password);
		$password = $this->avoidConsecutiveCharacters($password);
		$this->ensureAlphabeticStart($password, $generatorConfig, $characterCounts);

		return implode("", $password);
	}

	/**
	 * @param array $doNotUse
	 * @return array<string, array<int, string>>
	 * @throws EmptyPoolException
	 */
	private function buildFilteredPools(array $doNotUse): array {
		$doNotUseLower = array_map("strtolower", $doNotUse);

		$pools = [];
		foreach(CharacterPool::cases() as $characterPool) {
			$filtered = array_values(array_filter(
				str_split($characterPool->getPool()),
				fn($char) => !in_array(strtolower($char), $doNotUseLower),
			));

			if(!count($filtered)) {
				throw new EmptyPoolException("The pool '{$characterPool->name}' is empty");
			}

			$pools[$characterPool->name] = $filtered;
		}

		return $pools;
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
	 * @param array<string, array<int, string>> $filteredPools
	 * @param array $characterCounts
	 * @return void
	 * @throws RandomException
	 */
	private function populatePassword(array &$password, array $filteredPools, array $characterCounts): void {
		$this->addCharacters($password, $filteredPools[CharacterPool::CHARACTERS->name], $characterCounts['lowercase']);
		$this->addCharacters($password, $filteredPools[CharacterPool::CHARACTERS->name], $characterCounts['uppercase'], true);
		$this->addCharacters($password, $filteredPools[CharacterPool::NUMBERS->name], $characterCounts['numbers']);
		$this->addCharacters($password, $filteredPools[CharacterPool::SYMBOLS->name], $characterCounts['symbols']);
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
	 * @param array<int, string> $pool
	 * @param int $count
	 * @param bool $uppercase
	 * @return void
	 * @throws RandomException
	 */
	private function addCharacters(array &$password, array $pool, int $count, bool $uppercase = false): void {
		$maxIndex = count($pool) - 1;
		for($i = 0; $i < $count; $i++) {
			$char = $pool[random_int(0, $maxIndex)];
			$password[] = $uppercase ? strtoupper($char) : $char;
		}
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