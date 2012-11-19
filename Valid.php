<?php

class Valid extends \Phalcon\Mvc\Model\Validator
{
    public function validate($model = NULL)
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
            case 'file_image':
                try
                {
                    // Get the width and height from the uploaded image
                    list($tmp_width, $tmp_height) = getimagesize($_FILES[$field]['tmp_name']);
                }
                catch (Exception $e){}

                if (empty($tmp_width) OR empty($tmp_height))
                    // Cannot get image size, cannot validate
                    return FALSE;
                
                // No limit, use the image width
                $min_width = $this->isSetOption('min_width') ? $this->getOption('min_width') : 1;
                $max_width = $this->isSetOption('max_width') ? $this->getOption('max_width') : $tmp_width;

                // No limit, use the image height
                $min_height = $this->isSetOption('min_height') ? $this->getOption('min_height') : 1;
                $max_height = $this->isSetOption('max_height') ? $this->getOption('max_height') : $tmp_width;

                
                if ($this->getOption('exactly'))
                {
                    // Check if dimensions match exactly
                    $filtered = ($tmp_width === $max_width AND $tmp_height === $max_height) ? TRUE : FALSE;
                }
                else
                {
                    // Check if size is within minimum and maximum dimensions
                    $filtered = ($tmp_width >= $min_width AND $tmp_height >= $min_height AND $tmp_width <= $max_width AND $tmp_height <= $max_height) ? TRUE : FALSE;
                }
                
                if ( ! $filtered)
                {
                    $min = ($this->isSetOption('min_width') ? $this->getOption('min_width') : __('ANY')).'x'.($this->isSetOption('min_height') ? $this->getOption('min_height') : __('ANY')).'px';
                    $max = ($this->isSetOption('max_width') ? $this->getOption('max_width') : __('ANY')).'x'.($this->isSetOption('max_height') ? $this->getOption('max_height') : __('ANY')).'px';
                    
                    if ($this->getOption('exactly'))
                        $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Resolution of file :field must be exactly :resolution", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>", ':resolution' => '<em>'.$max.'</em>' )), $field, "file_image");
                    else
                        $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Resolution of file :field is not valid,:resolution", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>", ':resolution' => (($this->isSetOption('min_width') || $this->isSetOption('min_height')) ? ' min:<em>'.$min.'</em>' : '').(($this->isSetOption('max_width') || $this->isSetOption('max_height')) ? ' max:<em>'.$max.'</em>' : '') )), $field, "file_image");
                    return FALSE;
                }
            break;
            case 'file_not_empty':
                $filtered = isset($_FILES[$field]['error']) AND isset($_FILES[$field]['tmp_name']) AND $_FILES[$field]['error'] === UPLOAD_ERR_OK AND is_uploaded_file($_FILES[$field]['tmp_name']) ? TRUE : FALSE ;
                if ( ! $filtered)
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("File :field must not be empty", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "file_not_empty");
                    return FALSE;
                }
            break;
            case 'file_size':
                if ($_FILES[$field]['error'] === UPLOAD_ERR_INI_SIZE)
                    // Upload is larger than PHP allowed size (upload_max_filesize)
                    return FALSE;
                
                if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK)
                    // The upload failed, no size to check
                    return TRUE;
                
                $byte_units = array('B' => 0, 'K' => 10, 'M' => 20, 'G' => 30, 'T' => 40,
                                              'KB'=> 10, 'MB'=> 20, 'GB'=> 30, 'TB'=> 40);
                $max = trim( (string) $this->getOption('max'));
                preg_match('/^([0-9]+(?:\.[0-9]+)?)('.implode('|', array_keys($byte_units)).')?$/Di', $max, $matches);
                $bytes = (float) $matches[1] * pow(2, $byte_units[Arr::get($matches, 2, 'B')]);
                
                if ( ! $_FILES[$field]['size'] <= $bytes)
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Max size of file :field is :max", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>" , ':max' => '<em>'.$max.'</em>')), $field, "file_size");
                    return FALSE;
                }
            break;
            case 'file_type':
                if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK)
                    return TRUE;
                
                $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
                $filtered = in_array($ext, $this->getOption('allowed'));
                if ( ! $filtered)
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Type of file :field is not valid", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "file_type");
                    return FALSE;
                }
            break;
            case 'file_valid':
                $filtered = isset($_FILES[$field]['error']) AND isset($_FILES[$field]['name']) AND isset($_FILES[$field]['type']) AND isset($_FILES[$field]['tmp_name']) AND isset($_FILES[$field]['size']) ? TRUE : FALSE ;
                if ( ! $filtered)
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("File :field is not valid", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>" )), $field, "file_valid");
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
                if ($this->isSetOption('min') && strlen($value) < $this->getOption('min'))
                {
                    $this->appendMessage($this->isSetOption('message') ? $this->getOption('message') : __("Field :field must be at least :min characters long", array(':field' => "<em>" . ( $this->isSetOption('label') ? __($this->getOption('label')) : $field ) . "</em>", ':min' => "<em>" . $this->getOption('min') . "</em>" )), $field, "length");
                    return FALSE;
                }
                if ($this->isSetOption('max') && strlen($value) > $this->getOption('max'))
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