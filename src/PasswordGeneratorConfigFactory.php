<?php
declare(strict_types=1);

namespace Kristos80\PasswordGenerator;

final readonly class PasswordGeneratorFactory {

	/**
	 * A balanced preset with a mix of types, 10 characters total.
	 */
	public static function safeDefault(): PasswordGeneratorConfig {
		return new PasswordGeneratorConfig(
			new PoolRange(4, 4),
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
	}

	/**
	 * Stronger, longer password (16–20 characters).
	 */
	public static function strong(): PasswordGeneratorConfig {
		return new PasswordGeneratorConfig(
			new PoolRange(5, 6),
			new PoolRange(4, 5),
			new PoolRange(3, 4),
			new PoolRange(4, 5),
			TRUE,
			[],
		);
	}

	/**
	 * Passwords that are easier to type/read.
	 * Excludes symbols and common confusing characters.
	 */
	public static function humanFriendly(): PasswordGeneratorConfig {
		return new PasswordGeneratorConfig(
			new PoolRange(5, 6),
			new PoolRange(3, 4),
			new PoolRange(2, 2),
			new PoolRange(0, 0),
			TRUE,
			[
				"l",
				"1",
				"I",
				"O",
				"0",
			],
		);
	}

	/**
	 * Designed for very strong symbol-heavy passwords (e.g., vaults).
	 */
	public static function symbolHeavy(): PasswordGeneratorConfig {
		return new PasswordGeneratorConfig(
			new PoolRange(2, 3),
			new PoolRange(2, 3),
			new PoolRange(2, 3),
			new PoolRange(5, 6),
			TRUE,
			[],
		);
	}

	/**
	 * Ideal for developer API keys or secrets (alphanumeric only, fixed length).
	 */
	public static function developerPreset(): PasswordGeneratorConfig {
		return new PasswordGeneratorConfig(
			new PoolRange(6, 6),
			new PoolRange(5, 5),
			new PoolRange(5, 5),
			new PoolRange(0, 0),
			FALSE,
			[],
		);
	}
}
