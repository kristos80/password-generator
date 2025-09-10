<?php
declare(strict_types=1);

namespace Kristos80\PasswordGenerator\Tests;

use Kristos80\PasswordGenerator\CharacterPool;

final class CharacterPoolTest extends TestCase {

	public function testCharactersCase(): void {
		$this->assertEquals("abcdefghijklmnopqrstuvwxyz", CharacterPool::CHARACTERS->value);
		$this->assertEquals("abcdefghijklmnopqrstuvwxyz", CharacterPool::CHARACTERS->getPool());
	}

	public function testNumbersCase(): void {
		$this->assertEquals("0123456789", CharacterPool::NUMBERS->value);
		$this->assertEquals("0123456789", CharacterPool::NUMBERS->getPool());
	}

	public function testSymbolsCase(): void {
		$this->assertEquals("!@#$%^&*()-_=+[]{}|;:,.<>?", CharacterPool::SYMBOLS->value);
		$this->assertEquals("!@#$%^&*()-_=+[]{}|;:,.<>?", CharacterPool::SYMBOLS->getPool());
	}

	public function testGetPoolMethod(): void {
		$this->assertIsString(CharacterPool::CHARACTERS->getPool());
		$this->assertIsString(CharacterPool::NUMBERS->getPool());
		$this->assertIsString(CharacterPool::SYMBOLS->getPool());
	}

	public function testAllCasesAreDifferent(): void {
		$characters = CharacterPool::CHARACTERS->getPool();
		$numbers = CharacterPool::NUMBERS->getPool();
		$symbols = CharacterPool::SYMBOLS->getPool();

		$this->assertNotEquals($characters, $numbers);
		$this->assertNotEquals($characters, $symbols);
		$this->assertNotEquals($numbers, $symbols);
	}
}