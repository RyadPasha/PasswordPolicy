<?php

/**
 * @project: PasswordPolicy
 *
 * @purpose: This class provides a password validation against the standard password requirements rules
 * @version: 1.0
 *
 *
 * @author: Mohamed Riyad
 * @created on: 21 March, 2022
 *
 * @url: http://ryadpasha.com
 * @email: m@ryad.me
 * @license: MIT License
 *
 * @see: https://github.com/ryadpasha/passwordpolicy
 */
class PasswordPolicy
{

  private $hashedPassword;

  private $rules = [];
  private $default_rules = [
    'minLength'      => 8,
    'minDigit'       => 1,
    'minSpecialChar' => 1,
    'minUpperCase'   => 1,
    'minUpperCase'   => 1,
    'occurrences'    => 3,
    'sequential'     => 3,
  ];

  private $errors = [];
  private $error_messages = [
    'minLength'      => 'Password must be at least %s character%s long',
    'maxLength'      => 'Password must be at most %s character%s long',
    'minDigit'       => 'Password must contain at least %s digit%s',
    'minSpecialChar' => 'Password must contain at least %s special character%s',
    'minUpperCase'   => 'Password must contain at least %s uppercase character%s',
    'minUpperCase'   => 'Password must contain at least %s lowercase character%s',
    'occurrences'    => 'Password can not contain %s occurrence%s of the same character',
    'sequential'     => 'Password can not contain %s sequential%s letters or numbers',
    'cantContain'    => 'Password can not contain `%s`',
    'blackList'      => 'Password contains a blacklisted word',
    'notIn'          => 'You can not reuse a previous password',
    'default'        => 'Password is not strong enough'
  ];

  /**
   * @return array
   */
  public function getErrors() {
    return $this->errors;
  }

  public function storeRuleError($rule, $value = '') {
    $this->errors[] = !empty($this->error_messages[$rule]) ? sprintf($this->error_messages[$rule], $value, $value && is_numeric($value) && $value > 1 ? 's' : '') : $this->error_messages['default'];
  }

  /**
   * @param $value
   * @return this
   */
  public function minLength($value) {
    $this->rules['minLength'] = $value;
    return $this;
  }

  /**
   * @param $value
   * @return this
   */
  public function maxLength($value) {
    $this->rules['maxLength'] = $value;
    return $this;
  }

  /**
   * @param $value
   * @return this
   */
  public function minDigit($value) {
    $this->rules['minDigit'] = $value;
    return $this;
  }

  /**
   * @param $value
   * @return this
   */
  public function specialCharacter($value) {
    $this->rules['minSpecialChar'] = $value;
    return $this;
  }

  /**
   * @param $value
   * @return this
   */
  public function upperCase($value) {
    $this->rules['minUpperCase'] = $value;
    return $this;
  }

  /**
   * @param $value
   * @return this
   */
  public function lowerCase($value) {
    $this->rules['minLowerCase'] = $value;
    return $this;
  }

  /**
   * @param $value
   * @return this
   */
  public function cantContain($value) {
    $this->rules['cantContain'] = $value;
    return $this;
  }

  /**
   * @param $value
   * @return this
   */
  public function blackList($value) {
    $this->rules['blackList'] = $value;
    return $this;
  }

  /**
   * @param $needle
   * @param $hashed
   * @return this
   */
  public function notIn($needle, $hashedPassword = null) {
    $this->rules['notIn'] = $needle;
    $this->hashedPassword = $hashedPassword;
    return $this;
  }

  /**
   * @param $value
   * @return this
   */
  public function occurrences($value) {
    $this->rules['occurrences'] = $value;
    return $this;
  }

  /**
   * @param $value
   * @return this
   */
  public function sequential($value) {
    $this->rules['sequential'] = $value;
    return $this;
  }

  /**
   * Check the given string against a pattern for minimum occurrence
   *
   * @param $string
   * @param $pattern
   * @param $min
   *
   * @return bool
   */
  private function check($string, $pattern, $min) {
    $matchingCharacters = preg_replace($pattern, '', $string);
    if (strlen($matchingCharacters) < $min) return true;

    return false;
  }

  /**
   * Check the given string against a pattern
   *
   * @param $string
   * @param $pattern
   *
   * @return bool
   */
  public function checkRegex($string, $pattern) {
    if (preg_match('/' . $pattern . '/xi', $string)) return true;

    return false;
  }

  /**
   * Check if string contains a value in array
   *
   * @param $string
   * @param $needle
   *
   * @return mixed
   */
  private function needleContains($string, $needle) {
    foreach ($needle as $each) {
      if (strpos($string, $each) !== FALSE) {
        return $each;
      }
    }
    return false;
  }

  /**
   * Generate a pattern for sequential characters
   *
   * @param $string
   * @param $length
   *
   * @return string
   */
  public function sequentialRegexPattern($string, $length) {
    while (strlen($string) > $length - 1) {
      $chunks[] = substr($string, 0, $length); // Cut away the first chunk
      $string   = substr($string, 1);          // Keep the rest together
    }
    return implode('|', $chunks);
  }

  /**
   * Check if string has sequential characters
   *
   * @param $string
   * @param $length
   *
   * @return string
   */
  public function containsSequentialChars($string, $length) {
    $string     = str_replace(' ', '', $string);
    $EN_alpha   = 'abcdefghijklmnopqrstyvwxyz'; // implode('', range('a', 'z'));
    $AR_alpha1  = 'أبتثجحخدذرزسشصضطظعغفقكلمنهوي';
    $AR_alpha2  = 'أبجدهوزحطيكلمنسعفصقرشت';
    $EN_nums_re = '(?:٠(?=١|\b)|١(?=٢|\b)|٢(?=٣|\b)|٣(?=٤|\b)|٤(?=٥|\b)|٥(?=٦|\b)|٦(?=٧|\b)|٧(?=٨|\b)|٨(?=٩|\b)|٩\b){' . ($length - 1 ? $length - 1 : $length) . ',}';
    $AR_nums_re = '(?:0(?=1|\b)|1(?=2|\b)|2(?=3|\b)|3(?=4|\b)|4(?=5|\b)|5(?=6|\b)|6(?=7|\b)|7(?=8|\b)|8(?=9|\b)|9\b){' . $length . ',}';

    return $this->checkRegex($string, $this->sequentialRegexPattern($EN_alpha, $length))
      || $this->checkRegex($string, $this->sequentialRegexPattern($AR_alpha1, $length))
      || $this->checkRegex($string, $this->sequentialRegexPattern($AR_alpha2, $length))
      || $this->checkRegex($string, $AR_nums_re)
      || $this->checkRegex($string, $EN_nums_re);
  }

  /**
   * Check the given password against the rules
   *
   * @param $password
   *
   * @return bool
   */
  public function checkPassword($password) {
    $rules = empty($this->rules) ? $this->default_rules : $this->rules;
    foreach ($rules as $rule => $rule_value) {
      switch ($rule) {
        case 'minLength':
          if ($rule_value && strlen($password) < $rule_value) $this->storeRuleError($rule, $rule_value);
          break;

        case 'maxLength':
          if ($rule_value && strlen($password) > $rule_value) $this->storeRuleError($rule, $rule_value);
          break;

        case 'minDigit':
          if ($rule_value && $this->check($password, '/[^0-9]/', $rule_value)) $this->storeRuleError($rule, $rule_value);
          break;

        case 'minSpecialChar':
          if ($rule_value && $this->check($password, '/[\w\d\s]/', $rule_value)) $this->storeRuleError($rule, $rule_value);
          break;

        case 'minUpperCase':
          if ($rule_value && $this->check($password, '/[^A-Z]/', $rule_value)) $this->storeRuleError($rule, $rule_value);
          break;

        case 'minLowerCase':
          if ($rule_value && $this->check($password, '/[^a-z]/', $rule_value)) $this->storeRuleError($rule, $rule_value);
          break;

        case 'occurrences':
          if ($rule_value && preg_match('/(.)\1{' . ((int) $rule_value) . '}/x', $password)) $this->storeRuleError($rule, $rule_value);
          break;

        case 'sequential':
          if ($rule_value && $this->containsSequentialChars($password, $rule_value)) $this->storeRuleError($rule, $rule_value);
          break;

        case 'cantContain':
          if (!empty($rule_value) && is_array($rule_value) && ($matched = $this->needleContains($password, $rule_value))) $this->storeRuleError($rule, $matched);
          break;

        case 'blackList':
          if (in_array($password, $rule_value)) $this->storeRuleError($rule);
          break;

        case 'notIn':
          if (in_array($password, empty($this->hashedPassword) ? $rule_value : $this->hashedPassword)) $this->storeRuleError($rule);
          break;
      }
    }

    if (!empty($this->errors)) {
      return false;
    }

    return true;
  }
}
