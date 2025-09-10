<?php
declare(strict_types=1);

namespace Kristos80\PasswordGenerator;

enum PoolType: string {
	case CHARACTERS = "characters";
	case NUMBERS = "numbers";
	case SYMBOLS = "symbols";
}