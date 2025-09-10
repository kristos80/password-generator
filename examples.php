<?php
declare(strict_types=1);

require_once __DIR__ . "/vendor/autoload.php";

use Kristos80\PasswordGenerator\PasswordGenerator;
use Kristos80\PasswordGenerator\PasswordGeneratorConfigFactory;

$config = PasswordGeneratorConfigFactory::safeDefault();
$password = (new PasswordGenerator())->generate($config);

echo $password; //Cf<q{h8q4M%