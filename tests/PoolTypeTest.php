<?php
declare(strict_types=1);

namespace Kristos80\PasswordGenerator\Tests;

use Kristos80\PasswordGenerator\PoolType;

final class PoolTypeTest extends TestCase {

	public function testCharactersCase(): void {
		$this->assertEquals("characters", PoolType::CHARACTERS->value);
	}

	public function testNumbersCase(): void {
		$this->assertEquals("numbers", PoolType::NUMBERS->value);
	}

	public function testSymbolsCase(): void {
		$this->assertEquals("symbols", PoolType::SYMBOLS->value);
	}

	public function testAllCasesAreDifferent(): void {
		$this->assertNotEquals(PoolType::CHARACTERS->value, PoolType::NUMBERS->value);
		$this->assertNotEquals(PoolType::CHARACTERS->value, PoolType::SYMBOLS->value);
		$this->assertNotEquals(PoolType::NUMBERS->value, PoolType::SYMBOLS->value);
	}

	public function testEnumInstances(): void {
		$this->assertInstanceOf(PoolType::class, PoolType::CHARACTERS);
		$this->assertInstanceOf(PoolType::class, PoolType::NUMBERS);
		$this->assertInstanceOf(PoolType::class, PoolType::SYMBOLS);
	}
}