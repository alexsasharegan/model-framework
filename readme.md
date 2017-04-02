# PHP Model Framework

[![Latest Stable Version](https://poser.pugx.org/alexsasharegan/model-framework/v/stable)](https://packagist.org/packages/alexsasharegan/model-framework)
[![Total Downloads](https://poser.pugx.org/alexsasharegan/model-framework/downloads)](https://packagist.org/packages/alexsasharegan/model-framework)
[![Latest Unstable Version](https://poser.pugx.org/alexsasharegan/model-framework/v/unstable)](https://packagist.org/packages/alexsasharegan/model-framework)
[![License](https://poser.pugx.org/alexsasharegan/model-framework/license)](https://packagist.org/packages/alexsasharegan/model-framework)

A lightweight, MySQL-based data modeling framework in PHP strongly inspired by Laravel & Underscore/Lodash.

## Getting Started
Using Composer, load up the library:
```bash
composer require alexsasharegan/model-framework
```

This library uses the `vlucas/phpdotenv` library to connect models to your database (_already included_).
Start by creating a `.env` file with these database connection variables defined:
```bash
DB_HOST=localhost
DB_DATABASE=sandbox
DB_PORT=3306
DB_CHARSET=utf8
DB_USERNAME=root
DB_PASSWORD=root
```
Now load the environment:
```php
<?php
// in your main entry point file, load the environment
// this technique avoids any global variable pollution
call_user_func( function () {
	// constructed with the directory containing the `.env` file
	$environment = new \Dotenv\Dotenv( __DIR__ );
	$environment->load();
  
  // also a nice place to set things like timezone & session
  date_default_timezone_set('America/Phoenix');
  session_start();
} );
```
Now extend the abstract `\Framework\Model` class to get all the functionality:
```php
<?php

use Framework\Model;

class Character extends Model {
	
	/*
	 * This is the name of the database table
	 */
	const TABLE = 'characters';
	
	/*
	 * Calling Model::delete() when this is set to true
	 * will only set the softDeleteFieldName to TRUE and update the db model
	 */
	protected $useSoftDeletes = TRUE;
	
	protected $softDeleteFieldName = 'deleted';
	
	/*
	 * Use this to handle time-stamping model creation
	 * (since MySQL handles updates automatically,
	 *  but not creation automatically)
	 */
	protected $timestamp = TRUE;
	
	protected $timestampFieldName = 'created_at';
	
	protected $casts = [
		'default_value' => self::CAST_TO_INT,
		'deleted'       => self::CAST_TO_BOOL,
		'float'         => self::CAST_TO_FLOAT,
		'price'         => self::CAST_TO_PRICE,
		'special'       => self::CAST_FROM_JSON_TO_ARRAY,
		'specialObject' => self::CAST_FROM_JSON_TO_OBJECT,
	];
}

// Convenient static methods return instances
// this returns a model instance with the data from this id
// already parsed, or an empty model
$character = Character::fetch( 65 );

// Models implement ArrayAccess interface,
// but they still enforce the internal cast instructions
// as well as returning NULL when an index isn't defined
// instead of throwing an exception
$character['special'] = [
	'key'  => [
		'nested' => 'value',
	],
	'prop' => TRUE,
];

echo $character->toJson( JSON_PRETTY_PRINT );

$newCharacter = Character::fetch( 655 );
$newCharacter->mergeData( [
	'first_name' => 'Jamie',
	'last_name'  => 'Lanaster',
	'special'    => [
		'house' => [
			'name'  => 'Lanaster',
			'words' => 'A Lanaster always pays his debts.',
			'sigil' => 'Lion',
		],
	],
	'price'      => 12.3493046,
	'deleted'    => FALSE,
	'fakeField'  => "I'm not in your database...",
] );

echo PHP_EOL;
foreach ( $newCharacter as $key => $value )
{
	$type = gettype( $value );
	echo "Prop: $key ($type): " . print_r( $value, TRUE ) . PHP_EOL;
}

echo PHP_EOL;
// Retrieves the fields from the database,
// then filters the properties on the object
$newCharacter->removePropsNotInDatabase();
foreach ( $newUser as $key => $value )
{
	$type = gettype( $value );
	echo "Prop: $key ($type): " . print_r( $value, TRUE ) . PHP_EOL;
}
echo PHP_EOL;

// Model::fetchMany returns a collection
Character::fetchMany( "LIMIT 5" )
	    ->prepend( Character::fetch( 655 ) )
		->forPage( 2, 3 )
		->each( function ( Model $model, $index )
		{
			echo $model->get( 'id' ) . PHP_EOL;
		} )
	    ->reverse()
	    ->findWhere( function ( Model $model, $index )
	    {
		    return ! empty( $model->get( 'password' ) );
	    } );
```

## Dependencies

This library uses the following dependencies directly:

- [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv)
	- environment variable declaration
- [twig/twig](https://github.com/twigphp/Twig)
	- template engine for container
- [twig/extensions](https://github.com/twigphp/Twig-extensions)
	- extra features for template engine
- [slim/pdo](https://github.com/FaaPz/Slim-PDO)
	- PDO extension classes for OOP queries
	- Mostly implemented under the hood in the model classes
- [nesbot/carbon](https://github.com/briannesbitt/Carbon)
	- used for timestamping models
