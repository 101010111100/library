<?php

class Valid extends \Phalcon\Mvc\Model\Validator
{
    public function validate($model)
    {
        $field = $this->getOption('field');
        $value = $this->isSetOption('value') ? $this->getOption('value') : $model->$field;

        switch ($this->getOption('type'))
        {
            case 'alpha':
                $filtered = ctype_alpha($value);
                if ( ! $filtered && $value !== '')
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field must contain only letters", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "alpha");
                    return FALSE;
                }
            break;
            case 'alpha_dash':
                $filtered = preg_match('/^[-a-z0-9_]++$/iD', $value);;
                if ( ! $filtered && $value !== '')
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field must contain only numbers, letters and dashes", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "alpha_dash");
                    return FALSE;
                }
            break;
            case 'alpha_num':
                $filtered = ctype_alnum($value);
                if ( ! $filtered && $value !== '')
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field must contain only letters and numbers", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "alpha_num");
                    return FALSE;
                }
            break;
            case 'digit':
                $filtered = ctype_digit($value);
                if ( ! $filtered && $value !== '')
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field must be a digit", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "digit");
                    return FALSE;
                }
            break;
            case 'email':
                $filtered = filter_var($value, FILTER_VALIDATE_EMAIL);
                if ( ! $filtered && $value !== '')
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field must be a email address", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "email");
                    return FALSE;
                }
            break;
            case 'ip':
                $filtered = filter_var($value, FILTER_VALIDATE_IP);
                if ( ! $filtered && $value !== '')
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field must be an ip adderss", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "ip");
                    return FALSE;
                }
            break;
            case 'length':
                if (strlen($value) < $this->getOption('min'))
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field must be at least :min characters long", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>", ':min' => "<em>" . $this->getOption('min') . "</em>" )), $field, "length");
                    return FALSE;
                }
                if (strlen($value) > $this->getOption('max'))
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field must not exceed :max characters long", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>", ':max' => "<em>" . $this->getOption('max') . "</em>" )), $field, "length");
                    return FALSE;
                }
            break;
            case 'range':
                if ($value < $this->getOption('min') || $value > $this->getOption('max'))
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field must be within the range of :min to :max", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>", ':min' => "<em>" . $this->getOption('min') . "</em>", ':max' => "<em>" . $this->getOption('max') . "</em>" )), $field, "range");
                    return FALSE;
                }
            break;
            case 'regex':
                $filtered = preg_match($this->getOption('regex'), (string) $value);
                if ( ! $filtered && $value !== '')
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field does not match the required format", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "regex");
                    return FALSE;
                }
            break;
            case 'repeat':
                $repeat = $this->getOption('match');
                if ($value !== $model->$repeat)
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field must be the same as :repeat", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>", ':repeat' => "<em>" . ( $this->getOption('match_label') ? __($this->getOption('match_label')) : $repeat ) . "</em>" )), $field, "repeat");
                    return FALSE;
                }
            break;
            case 'unique':
                $filtered = $model::findFirst(array($field.'=:field:', 'bind' => array('field' => $value)));
                if ($filtered && $value !== '')
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field must be unique", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "unique");
                    return FALSE;
                }
            break;
            case 'url':
                $filtered = filter_var($value, FILTER_VALIDATE_URL);
                if ( ! $filtered && $value !== '')
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field must be a url", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "url");
                    return FALSE;
                }
            break;
            default:
                $filtered = in_array($value, array(NULL, FALSE, '', array()), TRUE);
                if ($filtered)
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field must not be empty", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "not_empty");
                    return FALSE;
                }
            break;
        }
        return TRUE;
    }
}