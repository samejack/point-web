<?php

namespace point\web;

class Validate_Validator
{
    /**
     * result messages
     *
     * @var array
     */
    private $_messages = array();

    /**
     * Check columns by rules
     *
     * @see Rule
     * @param array   $columns
     * @param array   $columnRules
     * @param boolean [$columnBreak] default is false [optional]<br>
     *  if break set ture, the validator will break at any rule deny first.
     * @param boolean [$ruleBreak] default is true [optional]<br>
     *  if break set ture, the rule as validator will be break at any rule deny first.
     * @return array
     */
    public function check(array &$columns, array $columnRules, $columnBreak = false, $ruleBreak = true)
    {
        $this->_messages = array();
        foreach ($columnRules as $id => $rules) {
            //run rule by step
            if (!$this->_runRules($columns, $id, $rules, $ruleBreak) && $columnBreak) {
                break;
            }
        }
        return $this->_messages;
    }

    /**
     * Validate rules
     *
     * @param array $columns
     * @param string $id
     * @param array $rules
     * @param boolean $ruleBreak
     * @throws \Exception
     * @return boolean
     */
    private function _runRules (array &$columns, $id, array &$rules, $ruleBreak=true)
    {
        // optional
        foreach ($rules as &$rule) {
            if ($rule instanceof Validate_Rule_Optional) {
                // 如果「非必填」的話，沒有傳入值不需要驗證
                if (!isset($columns[$id]) || empty($columns[$id])) {
                    return true;
                }
            }
        }

        $result = true;
        foreach ($rules as &$rule) {
            //check rule exist
            if (!($rule instanceof Validate_Interface)) {
                throw new \Exception(get_class($rule) . ' not instance of Validate_Interface.');
            }

            $result &= $this->_runRule($columns, $id, $rule);
            if (!$result && $ruleBreak) {
                return false;
            }
        }
        return $result;
    }

    /**
     * Validate rule
     *
     * @param array $columns
     * @param string $id
     * @param Validate_Interface $rule
     * @return boolean
     */
    private function _runRule (array &$columns, $id, Validate_Interface &$rule)
    {
        // process array columns
        $columnValue = '';
        if (isset($columns[$id])) {
            $columnValue = $columns[$id];
        }

        if ($rule instanceof Validate_ArrayRule) {
            $result = $rule->validate($columnValue, $columns);
        } else {
            $result = $this->_nestValidate($rule, $columnValue, $columns);
        }
        if (!$result) {
            $this->_messages[] = array(
                'id' => $id ,
                'rule' => $rule ,
                'message' => $rule->getMessage()
            );
        }
        return $result;
    }

    private function _nestValidate(&$rule, &$columnValue, &$columns)
    {
        if (is_array($columnValue)) {
            $result = true;
            foreach ($columnValue as &$value) {
                $result &= $this->_nestValidate($rule, $value, $columns);
                if (!$result)  return false;
            }
            return true;
        } else {
            return $rule->validate($columnValue, $columns);
        }
    }

    private function _convertValue (array $args, array $columns)
    {
        $key = array_pop(array_keys($args));
        $value = array_pop($args);
        if (!isset($columns[$key])) {
            return '';
        }
        if (is_array($columns[$key])&&is_array($value)) {
            return $this->_convertValue($value, $columns[$key]);
        }
        return $this->checkValues($columns[$key], array($value));
    }

}
