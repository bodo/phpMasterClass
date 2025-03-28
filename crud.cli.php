<?php   declare(strict_types=1);

use Model\AdminUser;
use Model\Guest;
use Model\User;

define('MAIN_SCRIPT', 1);       //protect the includes from URL-calling

//require "vendor/autoload.php";
require_once "utility.inc.php";

//Class Hirarchie

// CRUDAbstract ---(extends)---> User ---(extends)---> AdminUser
// CRUDAbstract ---(extends)---> Gast


//1. Abstract , 1. User
$steffi = new User("Steffi", "Meyer", 52);

$steffi->setLastname("Lange");
$success = $steffi->persist();      //update
$success = $steffi->persist();      //check, if update doesn't run twice

$steffi->delete();

$steffiFromDB = User::findByID(6);


$success = User::deleteById(5);
echo $success;



//2. Abstract , 2. User
$juergen = new User();
$juergen->setFirstname('Jürgen');
$juergen->setLastname('Luchmann');
$juergen->setAge(61);
$juergen->persist();


$gast1 = new Guest();

$sven = new AdminUser('Sven', 'Kleinschmidt', 23);
$bodo = new AdminUser('Bodo', 'Eichstädt', 49);


$gast2 = new Guest();
$sam = new User("Sam", "Bandoly", 42);
$manuel = new User("Manuel", "Martinez", 33);
$michael = new User("Michael", "Neumann", 34);

$hansdieter = new AdminUser('Hans-Dieter', 'Schwarze', 69);
$gast3 = new Guest();
$gast4 = new Guest();

$sammy = new User("Sammy", "Deluxe", 42);

echo $steffi;
$hansdieter->activateSuperPower();

$steffi->setAge(55);
$success = $steffi->persist();      //save to DB (create if needed)


if($success) {
    echo "DB UPDATE / INSERT successful";
}


/*

exit(0);

function ref(?array $arr) {
    $arr = [" "];
}

$my = ["4"];

print_r($my);

ref($my);
print_r($my);

ref(&$my);
print_r($my);*/
