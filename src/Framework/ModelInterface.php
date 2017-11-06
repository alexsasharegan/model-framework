<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/27/16
 * Time: 5:46 PM
 */

namespace Framework;

use Slim\PDO\Statement\SelectStatement;

/**
 * Interface ModelInterface
 * @package Framework
 */
interface ModelInterface {

  /**
   * Fetch a model from the database by id.
   *
   * @param $id
   *
   * @return ModelInterface
   */
  public static function fetch($id);

  /**
   * Fetch a model from the database.
   * Method appends "LIMIT 1" to underlying query.
   *
   * @return SelectStatement
   */
  public static function fetchWhere();

  /**
   * @return SelectStatement
   */
  public static function fetchMany();

  /**
   * @param bool $asCollection
   *
   * @return array|Collection
   */
  public static function fetchAll($asCollection = TRUE);

  /**
   * @return ModelInterface
   */
  public static function instance();

  /**
   * Get a property by name.
   *
   * @param $prop
   *
   * @return mixed
   */
  public function get($prop);

  /**
   * <p>Safely attempt to access nested array or array access elements using dot notation.</p>
   * <p>Example: $model->getNested('customer.paymentInfo.ccToken') ==
   * $model['customer']['paymentInfo']['ccToken']</p>
   *
   * @param $dotNotationString
   *
   * @return array|mixed|null
   */
  public function getNested($dotNotationString);

  /**
   * Get all the object data as an associative array.
   *
   * @return array
   */
  public function getAll();

  /**
   * Set a property by name.
   *
   * @param $prop
   * @param $value
   *
   * @return ModelInterface
   */
  public function set($prop, $value);

  /**
   * Initialize all the data on the model.
   *
   * @param array $data
   *
   * @return ModelInterface
   */
  public function setAll(array $data = []);

  /**
   * Unset a property on the model.
   *
   * @param $prop
   *
   * @return ModelInterface
   */
  public function remove($prop);

  /**
   * Set any number of associative arrays of data on the model merging it with existing attributes.
   *
   * @param array $data
   *
   * @return ModelInterface
   */
  public function mergeData(...$data);

  /**
   * Returns whether or not any data is set on the model
   *
   * @return bool
   */
  public function isEmpty();

  /**
   * Returns true if the model does not have an id.
   *
   * @return bool
   */
  public function isNew();

  /**
   * @param $prop
   * @param $value
   *
   * @return mixed
   */
  public function parse($prop, $value);

  /**
   * @param      $json
   * @param bool $parseAsArray
   *
   * @return array|object
   */
  public function parseJSON($json, $parseAsArray = TRUE);

  /**
   * @param $boolean
   *
   * @return bool
   */
  public function parseBool($boolean);

  /**
   * @param $int
   *
   * @return int
   */
  public function parseInt($int);

  /**
   * @param $float
   *
   * @return float
   */
  public function parseFloat($float);

  /**
   * @param $price
   *
   * @return float
   */
  public function parsePrice($price);

  /**
   * Inserts the model in the database and returns the insert id.
   * Should also check for the static::CREATED_AT field's presence,
   * and timestamp a value if not present.
   *
   * @return int
   */
  public function create();

  /**
   * Intelligently inserts or updates the model based on
   * whether or not an id is present in the model data.
   *
   * @return int
   */
  public function save();

  /**
   * Delete a model from the database (requires id to be present).
   * If $useSoftDeletes is TRUE, updates the $softDeleteFieldName.
   *
   * @return bool|ModelInterface
   */
  public function delete();

  /**
   * @return ModelInterface
   */
  public function softDelete();

  /**
   * Converts the internal model data array to json.
   *
   * @param int $options
   * @param int $depth
   *
   * @return string
   */
  public function toJson($options = 0, $depth = 512);

  /**
   * Converts the internal model data array to a plain array.
   * @return array
   */
  public function toArray();

  /**
   * Converts the internal model data array to a collection.
   * @return Collection
   */
  public function toCollection();

}
