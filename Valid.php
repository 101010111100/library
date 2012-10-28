<?php

class Valid extends \Phalcon\Mvc\Model\Validator
{
    public $data = array();
    
    public function validate($model)
    {
        $field = $this->getOption('field');
        $value = $this->isSetOption('value') ? $this->getOption('value') : $model->$field;

        switch ($this->getOption('type'))
        {
            case 'email':
                $filtered = filter_var($value, FILTER_VALIDATE_EMAIL);
                if ( ! $filtered && $value !== '')
                {
                    $this->appendMessage($this->getOption('message') ? $this->getOption('message') : __("Field :field must be a email address", array(':field' => "<em>" . ( $this->getOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "email");
                    return FALSE;
                }
            break;
            case 'length':
                if (strlen($value) < $this->getOption('min'))
                {
                    $this->appendMessage($this->getOption('message') ? $this->getOption('message') : __("Field :field must be at least :min characters long", array(':field' => "<em>" . ( $this->getOption('label') ? __($this->getOption('label')) : $field ) . "</em>", ':min' => "<em>" . $this->getOption('min') . "</em>" )), $field, "length");
                    return FALSE;
                }
                if (strlen($value) > $this->getOption('max'))
                {
                    $this->appendMessage($this->getOption('message') ? $this->getOption('message') : __("Field :field must not exceed :max characters long", array(':field' => "<em>" . ( $this->getOption('label') ? __($this->getOption('label')) : $field ) . "</em>", ':max' => "<em>" . $this->getOption('max') . "</em>" )), $field, "length");
                    return FALSE;
                }
            break;
            case 'repeat':
                $repeat = $this->getOption('match');
                if ($value !== $model->$repeat)
                {
                    $this->appendMessage($this->getOption('message') ? $this->getOption('message') : __("Field :field must be the same as :repeat", array(':field' => "<em>" . ( $this->getOption('label') ? __($this->getOption('label')) : $field ) . "</em>", ':repeat' => "<em>" . ( $this->getOption('match_label') ? __($this->getOption('match_label')) : $repeat ) . "</em>" )), $field, "repeat");
                    return FALSE;
                }
            break;
            case 'url':
                $filtered = filter_var($value, FILTER_VALIDATE_URL);
                if ( ! $filtered && $value !== '')
                {
                    $this->appendMessage($this->getOption('message') ? $this->getOption('message') : __("Field :field must be a url", array(':field' => "<em>" . ( $this->getOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "url");
                    return FALSE;
                }
            break;
            default:
                return FALSE;
            break;
        }
        return TRUE;
    }
}