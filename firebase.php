<?php
require 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;

// Load Firebase credentials
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/csadproject-bbab0-firebase-adminsdk-fbsvc-af5d3d3432.json') // Replace with your actual JSON key path
    ->withDatabaseUri('https://csadproject-bbab0-default-rtdb.asia-southeast1.firebasedatabase.app/'); // Replace with your Firebase Database URL

$database = $factory->createDatabase();
$storage = $factory->createStorage();
$bucket = $storage->getBucket();
?>
