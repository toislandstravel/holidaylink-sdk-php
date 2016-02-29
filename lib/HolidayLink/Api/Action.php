<?php

namespace HolidayLink\Api;

use HolidayLink\Auth\Credentials;
use HolidayLink\Transport\JsonCall;
use HolidayLink\Transport\SimpleCall;
use HolidayLink\Transport\XmlCall;

/**
 * Class User
 * @package HolidayLink\Api
 */
class Action extends Model {

  public static $fields = [
    'id',
    'status',
    'type',
    'amount',
    'accommodationUnit',
    'visible_from',
    'visible_to',
    'applicable_from',
    'applicable_to',
    'created_at',
    'updated_at',
  ];

  /**
   * accommodation_unit_id options:
   *  - use id keys from AccommodationUnits::allFromXML()
   *
   * status options:
   *  - const STATUS_ACTIVE = 'active'
   *  - const STATUS_DISABLED = 'disabled';
   *
   * visible_from, visible_to, applicable_from, applicable_to date format:
   *  - use Y-m-d (2016-01-01)
   *
   * amount_value format:
   *  - use
   *
   * amount_unit options:
   *  -
   *
   * @var array
   */
  public static $requiredFields = [
    'accommodation_unit_id',
    'status',
    'visible_from',
    'visible_to',
    'applicable_from',
    'applicable_to',
    'amount_value',
    'amount_unit',
  ];

  /**
   * Action amount units
   */
  const AMOUNT_UNIT_PERCENTAGE = 'percentage';
  const AMOUNT_UNIT_ABSOLUTE = 'absolute';

  /************************ Additional options **************************
   *
   * Action types
   */
  const TYPE_SPECIAL = 'special';
  const TYPE_FIRST_MINUTE = 'first_minute';
  const TYPE_LAST_MINUTE = 'last_minute';

  /**
   * Retrieve single action matching the $code filter
   *
   * @param  string $code
   * @param  array $params
   * @param  Credentials $credentials API credentials
   *
   * @return self  the retrieved action
   */
  public static function singleFromXML ($code, array $params = null, Credentials $credentials = null) {
    if (empty($params)) {
      $params = array();
    }
    if (!empty($credentials)) {
      self::setCredentials($credentials);
    }

    $allowedParams = array(
      'expand' => 1,
    );

    $wrongParams = array_diff_key($params, $allowedParams);
    if (!empty($wrongParams)) {
      throw new \InvalidArgumentException('Invalid $params filter: ' . implode(', ', array_keys($wrongParams)));
    }

    $call = new XmlCall($credentials);
    $sxe = $call->execute('actions/' . $code, 'GET', array_intersect_key($params, $allowedParams));

    $ret = new self();
    $ret->fromXML($sxe);

    return $ret;
  }

  /**
   * Create single action from array of key => value params
   *
   * @param  array $params
   * @param  array $data
   * @param  Credentials $credentials API credentials
   *
   * @return self
   */
  public static function createSingle (array $params = [], array $data= [], Credentials $credentials = null) {
    if (!empty($credentials)) {
      self::setCredentials($credentials);
    }

    $allowedParams = array(
      'expand' => 1,
    );

    $wrongParams = array_diff_key($params, $allowedParams);
    if (!empty($wrongParams)) {
      throw new \InvalidArgumentException('Invalid $params filter: ' . implode(', ', array_keys($wrongParams)));
    }

    $requiredParams = array_diff(self::$requiredFields, array_keys($data));
    if (!empty($requiredParams)) {
      throw new \InvalidArgumentException('Required params: ' . implode(', ', $requiredParams));
    }

    $call = new JsonCall($credentials);
    $sxe = $call->execute('actions', 'POST', array_intersect_key($params, $allowedParams), $data);

    return $sxe;
  }

  /**
   * Update single action matching the $code filter and array of key => value params
   *
   * @param  string $code
   * @param  array $params
   * @param  array $data
   * @param  Credentials $credentials API credentials
   *
   * @return self  the updated action
   */
  public static function updateSingle ($code, array $params = [], array $data= [], Credentials $credentials = null) {
    if (!empty($credentials)) {
      self::setCredentials($credentials);
    }

    $allowedParams = array(
      'expand' => 1,
    );

    $wrongParams = array_diff_key($params, $allowedParams);
    if (!empty($wrongParams)) {
      throw new \InvalidArgumentException('Invalid $params filter: ' . implode(', ', array_keys($wrongParams)));
    }

    $call = new JsonCall($credentials);
    $sxe = $call->execute('actions/' . $code, 'PUT', array_intersect_key($params, $allowedParams), $data);

    return $sxe;
  }

  /**
   * Delete single action matching the $code filter
   *
   * @param  string $code
   * @param  Credentials $credentials API credentials
   *
   * @return mixed
   */
  public static function deleteSingle ($code, Credentials $credentials = null) {

    if (!empty($credentials)) {
      self::setCredentials($credentials);
    }

    $call = new SimpleCall($credentials);
    $response = $call->execute('actions/' . $code, 'DELETE');

    return $response;
  }
}
