<?php
declare(strict_types=1);

namespace Kristos80\PasswordGenerator\Tests;

use Kristos80\PasswordGenerator\PoolRange;
use Kristos80\PasswordGenerator\PasswordGeneratorConfigFactory;

final class PasswordGeneratorConfigFactoryTest extends TestCase {

	public function testSafeDefault(): void {
		$config = PasswordGeneratorConfigFactory::safeDefault();

		$this->assertEquals(new PoolRange(4, 4), $config->lowercase);
		$this->assertEquals(new PoolRange(2, 2), $config->uppercase);
		$this->assertEquals(new PoolRange(2, 2), $config->numbers);
		$this->assertEquals(new PoolRange(2, 2), $config->symbols);
		$this->assertTrue($config->alwaysStartWithCharacter);
		$this->assertEquals(['l', '1', 'O', '0'], $config->doNotUse);
		$this->assertEquals(10, $config->getTotalMinCharacters());
		$this->assertEquals(10, $config->getTotalMaxCharacters());
	}

	public function testStrong(): void {
		$config = PasswordGeneratorConfigFactory::strong();

		$this->assertEquals(new PoolRange(5, 6), $config->lowercase);
		$this->assertEquals(new PoolRange(4, 5), $config->uppercase);
		$this->assertEquals(new PoolRange(3, 4), $config->numbers);
		$this->assertEquals(new PoolRange(4, 5), $config->symbols);
		$this->assertTrue($config->alwaysStartWithCharacter);
		$this->assertEquals([], $config->doNotUse);
		$this->assertEquals(16, $config->getTotalMinCharacters());
		$this->assertEquals(20, $config->getTotalMaxCharacters());
	}

	public function testHumanFriendly(): void {
		$config = PasswordGeneratorConfigFactory::humanFriendly();

		$this->assertEquals(new PoolRange(5, 6), $config->lowercase);
		$this->assertEquals(new PoolRange(3, 4), $config->uppercase);
		$this->assertEquals(new PoolRange(2, 2), $config->numbers);
		$this->assertEquals(new PoolRange(0, 0), $config->symbols);
		$this->assertTrue($config->alwaysStartWithCharacter);
		$this->assertEquals(['l', '1', 'I', 'O', '0'], $config->doNotUse);
		$this->assertEquals(10, $config->getTotalMinCharacters());
		$this->assertEquals(12, $config->getTotalMaxCharacters());
	}

	public function testSymbolHeavy(): void {
		$config = PasswordGeneratorConfigFactory::symbolHeavy();

		$this->assertEquals(new PoolRange(2, 3), $config->lowercase);
		$this->assertEquals(new PoolRange(2, 3), $config->uppercase);
		$this->assertEquals(new PoolRange(2, 3), $config->numbers);
		$this->assertEquals(new PoolRange(5, 6), $config->symbols);
		$this->assertTrue($config->alwaysStartWithCharacter);
		$this->assertEquals([], $config->doNotUse);
		$this->assertEquals(11, $config->getTotalMinCharacters());
		$this->assertEquals(15, $config->getTotalMaxCharacters());
	}

	public function testDeveloperPreset(): void {
		$config = PasswordGeneratorConfigFactory::developerPreset();

		$this->assertEquals(new PoolRange(6, 6), $config->lowercase);
		$this->assertEquals(new PoolRange(5, 5), $config->uppercase);
		$this->assertEquals(new PoolRange(5, 5), $config->numbers);
		$this->assertEquals(new PoolRange(0, 0), $config->symbols);
		$this->assertFalse($config->alwaysStartWithCharacter);
		$this->assertEquals([], $config->doNotUse);
		$this->assertEquals(16, $config->getTotalMinCharacters());
		$this->assertEquals(16, $config->getTotalMaxCharacters());
	}
}