<?php
declare(strict_types=1);

namespace Kristos80\PasswordGenerator\Tests;

use ReflectionMethod;
use ReflectionException;
use Random\RandomException;
use Kristos80\PasswordGenerator\PoolRange;
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
}
