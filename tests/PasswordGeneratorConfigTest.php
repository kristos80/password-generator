<?php
declare(strict_types=1);

namespace Kristos80\PasswordGenerator\Tests;

use Kristos80\PasswordGenerator\PoolRange;
use Kristos80\PasswordGenerator\PasswordGeneratorConfig;

final class PasswordGeneratorConfigTest extends TestCase {

	public function testDefaultConstructor(): void {
		$config = new PasswordGeneratorConfig();

		$this->assertEquals(new PoolRange(4, 4), $config->lowercase);
		$this->assertEquals(new PoolRange(2, 2), $config->uppercase);
		$this->assertEquals(new PoolRange(1, 1), $config->numbers);
		$this->assertEquals(new PoolRange(1, 1), $config->symbols);
		$this->assertFalse($config->alwaysStartWithCharacter);
		$this->assertEquals([], $config->doNotUse);
	}

	public function testCustomConstructor(): void {
		$config = new PasswordGeneratorConfig(
			new PoolRange(1, 2),
			new PoolRange(3, 4),
			new PoolRange(5, 6),
			new PoolRange(7, 8),
			TRUE,
			['a', 'b', 'c']
		);

		$this->assertEquals(new PoolRange(1, 2), $config->lowercase);
		$this->assertEquals(new PoolRange(3, 4), $config->uppercase);
		$this->assertEquals(new PoolRange(5, 6), $config->numbers);
		$this->assertEquals(new PoolRange(7, 8), $config->symbols);
		$this->assertTrue($config->alwaysStartWithCharacter);
		$this->assertEquals(['a', 'b', 'c'], $config->doNotUse);
	}

	public function testGetTotalMinCharacters(): void {
		$config = new PasswordGeneratorConfig(
			new PoolRange(2, 5),
			new PoolRange(1, 3),
			new PoolRange(1, 2),
			new PoolRange(1, 4)
		);

		$this->assertEquals(5, $config->getTotalMinCharacters()); // 2+1+1+1
	}

	public function testGetTotalMaxCharacters(): void {
		$config = new PasswordGeneratorConfig(
			new PoolRange(2, 5),
			new PoolRange(1, 3),
			new PoolRange(1, 2),
			new PoolRange(1, 4)
		);

		$this->assertEquals(14, $config->getTotalMaxCharacters()); // 5+3+2+4
	}

	public function testCalculationMethodsWithZeroRanges(): void {
		$config = new PasswordGeneratorConfig(
			new PoolRange(0, 0),
			new PoolRange(0, 0),
			new PoolRange(0, 0),
			new PoolRange(2, 3)
		);

		$this->assertEquals(2, $config->getTotalMinCharacters());
		$this->assertEquals(3, $config->getTotalMaxCharacters());
	}

	public function testCalculationMethodsWithSameMinMax(): void {
		$config = new PasswordGeneratorConfig(
			new PoolRange(3, 3),
			new PoolRange(3, 3),
			new PoolRange(3, 3),
			new PoolRange(3, 3)
		);

		$this->assertEquals(12, $config->getTotalMinCharacters());
		$this->assertEquals(12, $config->getTotalMaxCharacters());
	}

	public function testPublicPropertiesAreAccessible(): void {
		$config = new PasswordGeneratorConfig();

		// Test that properties are public and accessible
		$this->assertIsObject($config->lowercase);
		$this->assertIsObject($config->uppercase);
		$this->assertIsObject($config->numbers);
		$this->assertIsObject($config->symbols);
		$this->assertIsBool($config->alwaysStartWithCharacter);
		$this->assertIsArray($config->doNotUse);
	}
}