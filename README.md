# Password Generator for PHP 8.2+

A modern, flexible, and secure password generator built for PHP 8.2+. Designed with configuration-first principles, it supports character-type rules, avoidance of consecutive characters, and rich presets out of the box.

## ✨ Features

- PHP 8.2+ with `readonly` and strong types
- Configurable character pools: lowercase, uppercase, numbers, symbols
- Range-based character counts via `PoolRange`
- Always start with a letter (optional)
- Avoid consecutive characters (enabled by default)
- Character exclusion (e.g., omit `l`, `1`, `0`, `O`)
- Built-in presets (safe, strong, human-friendly, dev)
- Pest-based test suite
- Clean Composer autoloading (PSR-4)

---

## ⚙ Installation

```bash
composer require kristos80/password-generator
```

---

## 📄 Usage

### Basic Example

```php
<?php
declare(strict_types=1);

require_once __DIR__ . "/vendor/autoload.php";

use Kristos80\PasswordGenerator\PasswordGenerator;
use Kristos80\PasswordGenerator\PasswordGeneratorConfigFactory;

$config = PasswordGeneratorConfigFactory::safeDefault();
$password = (new PasswordGenerator())->generate($config);

echo $password; //Cf<q{h8q4M%
```

---

## 🔧 Presets

### `safeDefault()`
Balanced password with 3 lowercase, 2 uppercase, 2 numbers, 2 symbols.
Avoids confusing characters like `l`, `1`, `0`, `O`.

### `strong()`
16-20 character high-entropy password with balanced character pools.

### `humanFriendly()`
No symbols, avoids ambiguous characters. Easier to type & read.

### `symbolHeavy()`
Perfect for password vaults or power users. Focuses on symbol density.

### `developerPreset()`
Ideal for API tokens: all alphanumeric, no symbols, fixed length.

---

## ⚖️ Custom Configuration

You can define your own ranges:

```php
use Kristos80\PasswordGenerator\PasswordGeneratorConfig;
use Kristos80\PasswordGenerator\PoolRange;

$config = new PasswordGeneratorConfig(
    new PoolRange(3, 5),   // lowercase
    new PoolRange(2, 4),   // uppercase
    new PoolRange(2, 2),   // numbers
    new PoolRange(1, 2),   // symbols
    true,                  // must start with a letter
    ['l', '1', '0', 'O']   // characters to exclude
);
```

Then:

```php
$password = (new PasswordGenerator())->generate($config);
```

---

## 🧰 Built-in Logic

### Consecutive Character Avoidance
Consecutive identical characters are avoided (e.g., `aa`, `11`, `$$`), with up to 5 reshuffling attempts internally.

### Start with Character
When enabled, the password will always begin with a letter (a-z or A-Z).

### Exclusions
Characters passed via `doNotUse` will never appear in the final password, case-insensitively.

---

## 🧪 Testing

This package uses [Pest](https://pestphp.com/) for testing.

```bash
composer test
```

---

## 📖 License

MIT © Kristos80

