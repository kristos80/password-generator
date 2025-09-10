<?php
declare(strict_types=1);

namespace Kristos80\PasswordGenerator\Tests;

use ReflectionMethod;
use ReflectionException;
use Random\RandomException;
use Kristos80\PasswordGenerator\PoolRange;
use Kristos80\PasswordGenerator\PoolType;
use Kristos80\PasswordGenerator\PasswordGenerator;
use Kristos80\PasswordGenerator\EmptyPoolException;
use Kristos80\PasswordGenerator\PasswordGeneratorConfig;

final class PasswordGeneratorTest extends TestCase {

	/**
	 * @return void
	 * @throws ReflectionException
	 */
	public function testAvoidConsecutiveCharactersSimple(): void {
		$input =
			[
				"a",
				"a",
				"b",
				"b",
				"c",
				"c",
			];
		$output = $this->callAvoidConsecutiveCharacters($input);

		for($i = 1; $i < count($output); $i++) {
			$this->assertNotEquals(
				$output[$i],
				$output[$i - 1],
				"Found consecutive characters at positions $i-1 and $i",
			);
		}
	}

	/**
	 * @param array $input
	 * @return array
	 * @throws ReflectionException
	 */
	private function callAvoidConsecutiveCharacters(array $input): array {
		$generator = new PasswordGenerator();
		$method = new ReflectionMethod(PasswordGenerator::class, "avoidConsecutiveCharacters");
		$method->setAccessible(true);

		return $method->invoke($generator, $input);
	}

	/**
	 * @return void
	 * @throws ReflectionException
	 */
	public function testAlreadySafePassword(): void {
		$input =
			[
				"a",
				"b",
				"c",
				"d",
				"e",
			];
		$output = $this->callAvoidConsecutiveCharacters($input);

		$this->assertEquals($input, $output, "Password should remain unchanged");
	}

	/**
	 * @return void
	 * @throws ReflectionException
	 */
	public function testUnfixablePassword(): void {
		$input =
			[
				"a",
				"a",
				"a",
				"a",
				"a",
			];
		$output = $this->callAvoidConsecutiveCharacters($input);

		$consecutiveExists = FALSE;
		for($i = 1; $i < count($output); $i++) {
			if($output[$i] === $output[$i - 1]) {
				$consecutiveExists = TRUE;
				break;
			}
		}

		$this->assertTrue($consecutiveExists, "Unfixable input should still have consecutive characters");
	}

	/**
	 * @return void
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	public function testGeneratePasswordWithAllOptions(): void {
		$generator = new PasswordGenerator();

		$config = new PasswordGeneratorConfig(
			new PoolRange(3, 3),
			new PoolRange(2, 2),
			new PoolRange(2, 2),
			new PoolRange(2, 2),
			TRUE,
			[
				"l",
				"1",
				"O",
				"0",
			],
		);

		$password = $generator->generate($config);

		$this->assertIsString($password, "Password should be a string");
		$this->assertEquals(9, strlen($password), "Password should have 9 characters");

		$counts = [
			"lowercase" => 0,
			"uppercase" => 0,
			"numbers" => 0,
			"symbols" => 0,
		];

		foreach(str_split($password) as $char) {
			if(ctype_lower($char)) {
				$counts["lowercase"]++;
			} elseif(ctype_upper($char)) {
				$counts["uppercase"]++;
			} elseif(ctype_digit($char)) {
				$counts["numbers"]++;
			} elseif(strpbrk($char, "!@#$%^&*()-_=+[]{}|;:,.<>?") !== FALSE) {
				$counts["symbols"]++;
			}
		}

		$this->assertEquals(3, $counts["lowercase"], "Password should have 3 lowercase characters");
		$this->assertEquals(2, $counts["uppercase"], "Password should have 2 uppercase characters");
		$this->assertEquals(2, $counts["numbers"], "Password should have 2 numbers");
		$this->assertEquals(2, $counts["symbols"], "Password should have 2 symbols");

		$this->assertTrue(
			ctype_alpha($password[0]),
			"Password must start with a letter",
		);

		for($i = 1; $i < strlen($password); $i++) {
			$this->assertNotEquals(
				$password[$i],
				$password[$i - 1],
				"Characters at positions $i-1 and $i must not be the same",
			);
		}
	}

	/**
	 * @return void
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	public function testEmptyPoolExceptionWhenAllCharactersExcluded(): void {
		$generator = new PasswordGenerator();
		
		$config = new PasswordGeneratorConfig(
			new PoolRange(1, 1),
			new PoolRange(0, 0),
			new PoolRange(0, 0),
			new PoolRange(0, 0),
			FALSE,
			['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z']
		);

		$this->expectException(EmptyPoolException::class);
		$this->expectExceptionMessage("The pool 'characters' is empty");
		
		$generator->generate($config);
	}

	/**
	 * @return void
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	public function testEmptyPoolExceptionForNumbers(): void {
		$generator = new PasswordGenerator();
		
		$config = new PasswordGeneratorConfig(
			new PoolRange(0, 0),
			new PoolRange(0, 0),
			new PoolRange(1, 1),
			new PoolRange(0, 0),
			FALSE,
			['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']
		);

		$this->expectException(EmptyPoolException::class);
		$this->expectExceptionMessage("The pool 'numbers' is empty");
		
		$generator->generate($config);
	}

	/**
	 * @return void
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	public function testEmptyPoolExceptionForSymbols(): void {
		$generator = new PasswordGenerator();
		
		$config = new PasswordGeneratorConfig(
			new PoolRange(0, 0),
			new PoolRange(0, 0),
			new PoolRange(0, 0),
			new PoolRange(1, 1),
			FALSE,
			['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '=', '+', '[', ']', '{', '}', '|', ';', ':', ',', '.', '<', '>', '?']
		);

		$this->expectException(EmptyPoolException::class);
		$this->expectExceptionMessage("The pool 'symbols' is empty");
		
		$generator->generate($config);
	}

	/**
	 * @return void
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	public function testGenerateWithNoAlwaysStartWithCharacter(): void {
		$generator = new PasswordGenerator();
		
		$config = new PasswordGeneratorConfig(
			new PoolRange(1, 1),
			new PoolRange(1, 1),
			new PoolRange(1, 1),
			new PoolRange(1, 1),
			FALSE
		);

		$password = $generator->generate($config);
		
		$this->assertIsString($password);
		$this->assertEquals(4, strlen($password));
	}

	/**
	 * @return void
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	public function testGenerateWithZeroCharacterCounts(): void {
		$generator = new PasswordGenerator();
		
		$config = new PasswordGeneratorConfig(
			new PoolRange(0, 0),
			new PoolRange(0, 0),
			new PoolRange(1, 1),
			new PoolRange(0, 0),
			FALSE
		);

		$password = $generator->generate($config);
		
		$this->assertIsString($password);
		$this->assertEquals(1, strlen($password));
		$this->assertTrue(ctype_digit($password));
	}

	/**
	 * @return void
	 * @throws EmptyPoolException
	 * @throws RandomException  
	 */
	public function testGenerateWithOnlySymbols(): void {
		$generator = new PasswordGenerator();
		
		$config = new PasswordGeneratorConfig(
			new PoolRange(0, 0),
			new PoolRange(0, 0),
			new PoolRange(0, 0),
			new PoolRange(2, 2),
			FALSE
		);

		$password = $generator->generate($config);
		
		$this->assertIsString($password);
		$this->assertEquals(2, strlen($password));
		
		foreach(str_split($password) as $char) {
			$this->assertNotFalse(strpbrk($char, "!@#$%^&*()-_=+[]{}|;:,.<>?"));
		}
	}

	/**
	 * @return void
	 * @throws ReflectionException
	 * @throws RandomException
	 */
	public function testCalculateCharacterCounts(): void {
		$generator = new PasswordGenerator();
		$method = new ReflectionMethod(PasswordGenerator::class, "calculateCharacterCounts");
		$method->setAccessible(true);
		
		$config = new PasswordGeneratorConfig(
			new PoolRange(1, 3),
			new PoolRange(2, 4),
			new PoolRange(1, 2),
			new PoolRange(0, 1)
		);

		$counts = $method->invoke($generator, $config);

		$this->assertIsArray($counts);
		$this->assertArrayHasKey('lowercase', $counts);
		$this->assertArrayHasKey('uppercase', $counts);
		$this->assertArrayHasKey('numbers', $counts);
		$this->assertArrayHasKey('symbols', $counts);

		$this->assertGreaterThanOrEqual(1, $counts['lowercase']);
		$this->assertLessThanOrEqual(3, $counts['lowercase']);
		$this->assertGreaterThanOrEqual(2, $counts['uppercase']);
		$this->assertLessThanOrEqual(4, $counts['uppercase']);
		$this->assertGreaterThanOrEqual(1, $counts['numbers']);
		$this->assertLessThanOrEqual(2, $counts['numbers']);
		$this->assertGreaterThanOrEqual(0, $counts['symbols']);
		$this->assertLessThanOrEqual(1, $counts['symbols']);
	}


	/**
	 * @return void
	 * @throws ReflectionException
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	public function testPickRandom(): void {
		$generator = new PasswordGenerator();
		$method = new ReflectionMethod(PasswordGenerator::class, "pickRandom");
		$method->setAccessible(true);

		$char = $method->invoke($generator, PoolType::CHARACTERS, []);
		$this->assertTrue(ctype_lower($char));

		$digit = $method->invoke($generator, PoolType::NUMBERS, []);
		$this->assertTrue(ctype_digit($digit));

		$symbol = $method->invoke($generator, PoolType::SYMBOLS, []);
		$this->assertNotFalse(strpbrk($symbol, "!@#$%^&*()-_=+[]{}|;:,.<>?"));
	}

	/**
	 * @return void
	 * @throws ReflectionException
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	public function testPickRandomWithExclusions(): void {
		$generator = new PasswordGenerator();
		$method = new ReflectionMethod(PasswordGenerator::class, "pickRandom");
		$method->setAccessible(true);

		$char = $method->invoke($generator, PoolType::CHARACTERS, ['a', 'b', 'c']);
		$this->assertTrue(ctype_lower($char));
		$this->assertNotContains($char, ['a', 'b', 'c']);

		$digit = $method->invoke($generator, PoolType::NUMBERS, ['0', '1']);
		$this->assertTrue(ctype_digit($digit));
		$this->assertNotContains($digit, ['0', '1']);
	}

	/**
	 * @return void
	 * @throws ReflectionException
	 */
	public function testSwapArrayElements(): void {
		$generator = new PasswordGenerator();
		$method = new ReflectionMethod(PasswordGenerator::class, "swapArrayElements");
		$method->setAccessible(true);

		$array = ['a', 'b', 'c', 'd'];
		$method->invokeArgs($generator, [&$array, 0, 2]);

		$this->assertEquals(['c', 'b', 'a', 'd'], $array);
	}

	/**
	 * @return void
	 * @throws ReflectionException
	 */
	public function testEnsureAlphabeticStart(): void {
		$generator = new PasswordGenerator();
		$method = new ReflectionMethod(PasswordGenerator::class, "ensureAlphabeticStart");
		$method->setAccessible(true);

		// Test when alwaysStartWithCharacter is false
		$password = ['1', '2', 'a', 'b'];
		$config = new PasswordGeneratorConfig(alwaysStartWithCharacter: false);
		$counts = ['lowercase' => 2, 'uppercase' => 0, 'numbers' => 2, 'symbols' => 0];
		
		$originalPassword = $password;
		$method->invokeArgs($generator, [&$password, $config, $counts]);
		$this->assertEquals($originalPassword, $password); // Should not change

		// Test when alwaysStartWithCharacter is true but no letters available
		$password = ['1', '2', '3'];
		$config = new PasswordGeneratorConfig(alwaysStartWithCharacter: true);
		$counts = ['lowercase' => 0, 'uppercase' => 0, 'numbers' => 3, 'symbols' => 0];
		
		$originalPassword = $password;
		$method->invokeArgs($generator, [&$password, $config, $counts]);
		$this->assertEquals($originalPassword, $password); // Should not change

		// Test when alwaysStartWithCharacter is true and letters are available
		$password = ['1', '2', 'a'];
		$config = new PasswordGeneratorConfig(alwaysStartWithCharacter: true);
		$counts = ['lowercase' => 1, 'uppercase' => 0, 'numbers' => 2, 'symbols' => 0];
		
		$method->invokeArgs($generator, [&$password, $config, $counts]);
		$this->assertTrue(ctype_alpha($password[0])); // First character should be alphabetic
	}

	/**
	 * @return void
	 * @throws ReflectionException
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	public function testPopulatePassword(): void {
		$generator = new PasswordGenerator();
		$method = new ReflectionMethod(PasswordGenerator::class, "populatePassword");
		$method->setAccessible(true);
		
		$password = [];
		$config = new PasswordGeneratorConfig(
			new PoolRange(2, 2),
			new PoolRange(1, 1),
			new PoolRange(1, 1),
			new PoolRange(1, 1)
		);
		$counts = ['lowercase' => 2, 'uppercase' => 1, 'numbers' => 1, 'symbols' => 1];

		$method->invokeArgs($generator, [&$password, $config, $counts]);

		$this->assertCount(5, $password);
		
		$typeCounts = ['lowercase' => 0, 'uppercase' => 0, 'numbers' => 0, 'symbols' => 0];
		foreach($password as $char) {
			if(ctype_lower($char)) {
				$typeCounts['lowercase']++;
			} elseif(ctype_upper($char)) {
				$typeCounts['uppercase']++;
			} elseif(ctype_digit($char)) {
				$typeCounts['numbers']++;
			} elseif(strpbrk($char, "!@#$%^&*()-_=+[]{}|;:,.<>?") !== FALSE) {
				$typeCounts['symbols']++;
			}
		}

		$this->assertEquals(2, $typeCounts['lowercase']);
		$this->assertEquals(1, $typeCounts['uppercase']);
		$this->assertEquals(1, $typeCounts['numbers']);
		$this->assertEquals(1, $typeCounts['symbols']);
	}

	/**
	 * Test that password generation works with variable ranges
	 * @return void
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	public function testGenerateWithVariableRanges(): void {
		$generator = new PasswordGenerator();
		
		$config = new PasswordGeneratorConfig(
			new PoolRange(2, 5),
			new PoolRange(1, 3),
			new PoolRange(1, 4),
			new PoolRange(0, 2)
		);

		// Generate multiple passwords to test ranges
		for($i = 0; $i < 10; $i++) {
			$password = $generator->generate($config);
			$length = strlen($password);
			
			// Should be between 4 (2+1+1+0) and 14 (5+3+4+2) characters
			$this->assertGreaterThanOrEqual(4, $length);
			$this->assertLessThanOrEqual(14, $length);
		}
	}

	/**
	 * Test that case-insensitive exclusions work properly
	 * @return void
	 * @throws ReflectionException
	 * @throws EmptyPoolException
	 * @throws RandomException
	 */
	public function testCaseInsensitiveExclusions(): void {
		$generator = new PasswordGenerator();
		$method = new ReflectionMethod(PasswordGenerator::class, "pickRandom");
		$method->setAccessible(true);

		// Test case-insensitive exclusion (lowercase 'a' should exclude uppercase 'A')
		$char = $method->invoke($generator, PoolType::CHARACTERS, ['A']);
		$this->assertNotEquals('a', $char); // Should not pick 'a' since 'A' is excluded
	}
}
