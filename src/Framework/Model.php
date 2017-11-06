<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/27/16
 * Time: 5:44 PM
 */

namespace Framework;

use ArrayAccess;
use Carbon\Carbon;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use PDO;

/**
 * Class Model
 * @package Framework
 */
abstract class Model implements ModelInterface, IteratorAggregate, JsonSerializable, ArrayAccess, Countable {

  const CAST_FROM_JSON_TO_ARRAY = 'CAST_FROM_JSON_TO_ARRAY';
  const CAST_FROM_JSON_TO_OBJECT = 'CAST_FROM_JSON_TO_OBJECT';
  const CAST_TO_INT = 'CAST_TO_INT';
  const CAST_TO_FLOAT = 'CAST_TO_FLOAT';
  const CAST_TO_PRICE = 'CAST_TO_PRICE';
  const CAST_TO_BOOL = 'CAST_TO_BOOL';

  const DB_DATE_FORMAT = 'Y-m-d H:i:s';

  const COLUMN_SELECT = "SELECT column_name FROM information_schema.columns WHERE table_name = ? AND table_schema = ?";

  /**
   * Use this constant to define the model's database table
   */
  const TABLE = __CLASS__;
  /**
   * Fill with any default values for a new model.
   * @var array
   */
  protected $defaults = [];
  /**
   * Define as true if model should not be deleted from database.
   * @var bool
   */
  protected $useSoftDeletes = FALSE;
  /**
   * Define with name of soft-delete field in database
   * if $this->useSoftDeletes === TRUE
   * @var string
   */
  protected $softDeleteFieldName = 'deleted';
  /**
   * Define with name of createdAt timestamp field
   * @var string
   */
  protected $timestampFieldName = 'created';
  /**
   * If true, enforces the $timestampFieldName has a value when calling create()
   * @var bool
   */
  protected $timestamp = TRUE;
  /**
   * An associative array of properties with casting instructions,
   * the values of which are constants on the base class.
   * @var array
   */
  protected $casts = [
    'id' => self::CAST_TO_INT,
  ];
  /**
   * List of props to exclude when converting to an array
   * using the method Model::toWhiteListArray
   *
   * Used if Model::$whiteList is empty
   *
   * @var array
   */
  protected $blackList = [];
  /**
   * List of props to include when converting to an array
   * using the method Model::toWhiteListArray
   *
   * Trumps Model::$blackList when not empty
   *
   * @var array
   */
  protected $whiteList = [];
  /**
   * List of prop names to allow NULL when using Model::$casts
   * @var array
   */
  protected $allowNull = [];
  /**
   * The underlying array of model data
   * @var array
   */
  private $data = [];

  /**
   * Model constructor.
   *
   * @param array $data
   */
  public function __construct(array $data = []) {
    // The container connects only to a MySQL database,
    // so we can be sure this field should be cast to an integer,
    // but we'll leave this flexible to be overwritten in an extended class
    $this->casts = array_merge(['id' => static::CAST_TO_INT,], $this->casts);

    // Merge the data on the model with any defaults
    $this->mergeData($this->defaults, $data);
  }

  /**
   * @inheritdoc
   * @return static
   */
  public function mergeData(...$data) {
    return $this->setAll(
      array_merge($this->getAll(), ...$data)
    );
  }

  /**
   * @inheritdoc
   * @return static
   */
  public function setAll(array $data = []) {
    $this->data = [];

    foreach ($data as $prop => $value) $this->set($prop, $value);

    return $this;
  }

  /**
   * @inheritdoc
   * @return static
   */
  public function set($prop, $value) {
    if ($this->isDotNotation($prop)) return $this->setNested($prop, $value);

    $this->data[ $prop ] = $this->parse($prop, $value);

    return $this;
  }

  private function isDotNotation($string) {
    return preg_match(
      "/^(?:([a-zA-Z_][a-zA-Z0-9_]*)(?:\.([a-zA-Z_][a-zA-Z0-9_]*))+)$/",
      $string
    );
  }

  public function setNested($propString, $value) {
    $movingTarget = &$this->data;
    $keys = explode('.', strval($propString));
    $length = count($keys);

    foreach ($keys as $i => $key) {
      $lastKey = $i === $length - 1;
      $isset = isset($movingTarget[ $key ]);

      if ($isset && !$lastKey && !is_array($movingTarget[ $key ])) {
        throw new \InvalidArgumentException(sprintf(
          "Attempted to set/access the property %s like an array, but is of type: %s",
          $key,
          gettype($movingTarget[ $key ])
        ));
      }

      if (!$isset || !is_array($movingTarget[ $key ])) $movingTarget[ $key ] = [];

      $movingTarget = &$movingTarget[ $key ];
    }

    $movingTarget = $value;

    return $this;
  }

  /**
   * @inheritdoc
   */
  public function parse($prop, $value) {
    if ($this->shouldNotParse($prop, $value)) return $value;

    switch ($this->casts[ $prop ]) {
      case static::CAST_FROM_JSON_TO_ARRAY:
        return $this->parseJSON($value, TRUE);

      case static::CAST_FROM_JSON_TO_OBJECT:
        return $this->parseJSON($value, FALSE);

      case static::CAST_TO_BOOL:
        return $this->parseBool($value);

      case static::CAST_TO_FLOAT:
        return $this->parseFloat($value);

      case static::CAST_TO_PRICE:
        return $this->parsePrice($value);

      case static::CAST_TO_INT:
        return $this->parseInt($value);

      default:
        return $value;
    }
  }

  /**
   * @param $prop
   * @param $value
   *
   * @return bool
   */
  public function shouldNotParse($prop, $value) {
    if (!array_key_exists($prop, $this->casts)) return TRUE;
    if (is_null($value) && in_array($prop, $this->allowNull)) return TRUE;

    return FALSE;
  }

  /**
   * @inheritdoc
   */
  public function parseJSON($json, $parseAsArray = TRUE) {
    if (!$parseAsArray) {
      while (gettype($json) == 'string') $json = json_decode($json);

      return $json;
    }

    while (gettype($json) == 'string') $json = json_decode($json, TRUE);

    if (is_null($json)) $json = [];

    if (!is_array($json)) {
      $type = gettype($json);
      throw new \InvalidArgumentException(
        "Could not parse JSON to array. Value: [{$json}] Type: [{$type}]"
      );
    }

    return $json;
  }

  /**
   * @inheritdoc
   */
  public function parseBool($boolean) {
    return (bool) $boolean;
  }

  /**
   * @inheritdoc
   */
  public function parseFloat($float) {
    return (float) $float;
  }

  /**
   * @inheritdoc
   */
  public function parsePrice($price) {
    return round((float) $price, 2);
  }

  /**
   * @inheritdoc
   */
  public function parseInt($int) {
    return (int) $int;
  }

  /**
   * @inheritdoc
   */
  public function getAll() {
    return $this->data;
  }

  /**
   * @inheritdoc
   * @return static
   */
  public static function fetch($id) {
    $stmt = Container::db()
                     ->select()
                     ->from(static::TABLE)
                     ->where('id', '=', (int) $id)
                     ->limit(1, 0)
                     ->execute();

    $instance = static::setFetchModeClass($stmt)->fetch();

    return $instance ?: static::instance();
  }

  /**
   * @param \PDOStatement $statement
   *
   * @return \PDOStatement
   */
  public static function setFetchModeClass(\PDOStatement $statement) {
    $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::class);

    return $statement;
  }

  /**
   * @inheritdoc
   * @return static
   */
  public static function instance(array $data = []) {
    return new static($data);
  }

  /**
   * @inheritdoc
   */
  public static function fetchWhere() {
    return Container::db()
                    ->select()
                    ->from(static::TABLE)
                    ->limit(1, 0);
  }

  /**
   * @inheritdoc
   */
  public static function fetchMany() {
    return Container::db()
                    ->select()
                    ->from(static::TABLE);
  }

  /**
   * @inheritdoc
   */
  public static function fetchAll($asCollection = TRUE) {
    $stmt = Container::db()
                     ->select()
                     ->from(static::TABLE)
                     ->execute();

    $all = static::setFetchModeClass($stmt)->fetchAll();
    $all = !!$all ? $all : [];

    return $asCollection ? Collection::instance($all) : $all;
  }

  /**
   * @param bool $modelStateIsTruth
   *
   * @return $this
   * @throws \Exception
   */
  public function reHydrate($modelStateIsTruth = FALSE) {
    if ($this->isNew()) {
      throw new \Exception(
        sprintf("Cannot re-hydrate a new model. No `id` property on model [%s].", static::class)
      );
    }

    $id = $this->get('id');

    $newData = Container::db()
                        ->select()
                        ->from(static::TABLE)
                        ->where('id', '=', $id)
                        ->limit(1, 0)
                        ->execute()
                        ->fetch(PDO::FETCH_ASSOC);

    $modelStateIsTruth
      ? $this->setAll(array_merge($newData, $this->getAll()))
      : $this->mergeData($newData);

    return $this;
  }

  /**
   * @inheritdoc
   */
  public function isNew() {
    return !$this->get('id');
  }

  /**
   * @inheritdoc
   */
  public function get($prop) {
    if ($this->isDotNotation($prop)) return $this->getNested($prop);

    return isset($this->data[ $prop ]) ? $this->data[ $prop ] : NULL;
  }

  /**
   * @inheritdoc
   */
  public function getNested($dotNotationString) {
    $indices = explode('.', strval($dotNotationString));
    $movingTarget = $this->getAll();

    foreach ($indices as $index) {
      $isArray = is_array($movingTarget) || $movingTarget instanceof ArrayAccess;

      if (!$isArray || !isset($movingTarget[ $index ])) return NULL;

      $movingTarget = $movingTarget[ $index ];
    }

    return $movingTarget;
  }

  /**
   * @inheritdoc
   */
  public function isEmpty() {
    return empty($this->getAll());
  }

  /**
   * Fetch the model fields from the database,
   * and remove all props on the model not in those fields.
   * @return static
   */
  public function removePropsNotInDatabase() {
    $databaseProps = static::fetchDatabaseFields();

    $filterFn = function($value, $prop) use ($databaseProps) { return in_array($prop, $databaseProps); };

    return $this->setAll(
      Collection::instance($this->getAll())->filter($filterFn)->toArray()
    );
  }

  /**
   * Returns an array of field names from the connected database.
   *
   * @return array
   */
  public static function fetchDatabaseFields() {
    $stmt = Container::db()->prepare(static::COLUMN_SELECT);
    $stmt->execute([static::TABLE, Container::$connectionParams['database']]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }

  /**
   * @inheritdoc
   */
  public function count() {
    return count($this->getAll());
  }

  /**
   * @inheritdoc
   */
  public function offsetExists($offset) {
    return array_key_exists($offset, $this->getAll());
  }

  /**
   * @inheritdoc
   */
  public function offsetGet($offset) {
    return $this->get($offset);
  }

  /**
   * @inheritdoc
   */
  public function offsetSet($offset, $value) {
    $this->set($offset, $value);
  }

  /**
   * @inheritdoc
   */
  public function offsetUnset($offset) {
    $this->remove($offset);
  }

  /**
   * @inheritdoc
   * @return static
   */
  public function remove($prop) {
    unset($this->data[ $prop ]);

    return $this;
  }

  /**
   * @inheritdoc
   */
  public function save() {
    if ($this->isNew()) {
      return $this->create();
    }
    else {
      $id = $this->parseInt($this->get('id'));

      Container::db()
               ->update($this->getPreparedData())
               ->table(static::TABLE)
               ->where('id', '=', $id)
               ->execute();

      return $id;
    }
  }

  /**
   * @inheritdoc
   */
  public function create() {
    if ($this->timestamp && empty($this->get($this->timestampFieldName))) {
      $this->set(
        $this->timestampFieldName,
        Carbon::now()->format(self::DB_DATE_FORMAT)
      );
    }

    // returns a string id
    $id = Container::db()
                   ->insert($this->getPreparedData())
                   ->into(static::TABLE)
                   ->execute(TRUE);
    // this will be parsed as an integer
    $this->set('id', $id);

    return $this->get('id');
  }

  /**
   * @return array
   */
  public function getPreparedData() {
    $data = $this->getAll();

    foreach ($data as $key => $datum) {
      if (is_object($datum) || is_array($datum)) {
        $data[ $key ] = json_encode($datum);
      }
      elseif (is_bool($datum)) {
        $data[ $key ] = $this->parseInt($datum);
      }
    }

    return $data;
  }

  /**
   * @inheritdoc
   */
  public function delete() {
    if ($this->useSoftDeletes)
      return $this->softDelete();

    return $this->hardDelete();
  }

  /**
   * @inheritdoc
   * @return static
   */
  public function softDelete() {
    $this->set($this->softDeleteFieldName, TRUE);

    Container::db()
             ->update([$this->softDeleteFieldName => TRUE])
             ->table(static::TABLE)
             ->where('id', '=', $this->parseInt($this->get('id')))
             ->execute();

    return $this;
  }

  /**
   * @return bool
   */
  public function hardDelete() {
    return $this->parseBool(
      Container::db()
               ->delete(static::TABLE)
               ->where('id', '=', $this->get('id'))
               ->execute()
    );
  }

  /**
   * @return array
   */
  public function toWhiteListArray() {
    switch (TRUE) {
      case empty($this->whiteList):
        $rejectFn = function($value, $key) { return in_array($key, $this->blackList); };

        return Collection::instance($this->toArray())->reject($rejectFn)->toArray();
      default:
        $filterFn = function($value, $key) { return in_array($key, $this->whiteList); };

        return Collection::instance($this->toArray())->filter($filterFn)->toArray();
    }
  }

  /**
   * @inheritdoc
   */
  public function toArray() {
    return $this->jsonSerialize();
  }

  /**
   * @inheritdoc
   */
  public function jsonSerialize() {
    return $this->getAll();
  }

  /**
   * @inheritdoc
   */
  public function toCollection() {
    return Collection::instance($this->toArray());
  }

  /**
   * @return string
   */
  public function getClass() {
    return static::getNamespace();
  }

  /**
   * @return string
   */
  public static function getNamespace() {
    return static::class;
  }

  /**
   * @return string
   */
  public function getFullyQualifiedClass() {
    return static::getFullyQualifiedNamespace();
  }

  /**
   * Gets the fully-qualified class name of the late-statically bound class
   *
   * @return string
   */
  public static function getFullyQualifiedNamespace() {
    return '\\'.ltrim(static::class, '\\');
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->toJson();
  }

  /**
   * @inheritdoc
   */
  public function toJson($options = 0, $depth = 512) {
    return json_encode($this, $options, $depth);
  }

  /**
   * @param $prop
   *
   * @return mixed|null
   */
  public function __get($prop) {
    return $this->get($prop);
  }

  /**
   * @param $prop
   * @param $value
   *
   * @return Model
   */
  public function __set($prop, $value) {
    return $this->set($prop, $value);
  }

  /**
   * @inheritdoc
   */
  public function getIterator() {
    return new \ArrayIterator($this->data);
  }
}
