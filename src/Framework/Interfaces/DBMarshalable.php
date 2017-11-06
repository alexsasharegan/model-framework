<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/6/17
 * Time: 12:42 PM
 */

namespace Framework\Interfaces;

interface DBMarshalable {

  /**
   * Returns an associative array of transformed data to be used in DB queries.
   * @return array
   */
  public function dbMarshal();

  /**
   * Given a property key and its value,
   * returns a tuple (array length=2) of the transformed data for DB usage.
   * Can be used to transform property names and/or their values
   * for database marshaling.
   *
   * Example:
   * -
   * ```
   * dbSerialize('json_data', ['db_type' => 'string'])
   * // ['json_data', '{"db_type": "string"}']
   *
   * dbSerialize('camelCase', false)
   * // ['snake_case', 0)
   * ```
   *
   * @param string $key
   * @param mixed  $value
   *
   * @return array
   */
  public function dbSerialize($key, $value);

  /**
   * Returns an associative array of raw data to be transformed.
   * @return array
   */
  public function dbRaw();
}
