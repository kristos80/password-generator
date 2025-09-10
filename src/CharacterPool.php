<?php
declare(strict_types=1);

namespace Kristos80\PasswordGenerator;

enum CharacterPool: string {
	case CHARACTERS = "abcdefghijklmnopqrstuvwxyz";
	case NUMBERS = "0123456789";
	case SYMBOLS = "!@#$%^&*()-_=+[]{}|;:,.<>?";

	public function getPool(): string {
		return $this->value;
	}
}