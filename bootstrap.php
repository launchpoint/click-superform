<?

if(!isset($superform_settings))
{
  $superform_settings = array(
    'field_defaults'=>array(
      'email_address'=>array(
        'validators'=>array(
          array('type'=>'function', 'method'=>'is_valid_email', 'message'=>'is not a valid email address format.'),
        ),
        'filters'=>array(
        ),
      ),
      'phone_number'=>array(
        'validators'=>array(
          array('type'=>'function', 'method'=>'is_phone_number_format', 'message'=>'is not a valid phone number format.'),
        ),
        'filters'=>array(
        ),
      ),
      'zip_code'=>array(
        'validators'=>array(
          array('type'=>'regex', 'method'=>'/^\d{5}$/', 'message'=>'must be exactly 5 digits.'),
        ),
        'filters'=>array(
        ),
      ),
      'integer'=>array(
        'validators'=>array(
          array('type'=>'regex', 'method'=>'/^-?\d+$/', 'message'=>'must be an integer.'),
        ),
        'filters'=>array(
        ),
      ),
      'float'=>array(
        'validators'=>array(
          array('type'=>'regex', 'method'=>'/^-?[0-9]*\.?[0-9]+$/', 'message'=>'must be an integer.'),
        ),
        'filters'=>array(
        ),
      ),
      'color'=>array(
        'validators'=>array(
          array('type'=>'regex', 'method'=>'/^[0-9A-Fa-f]{6}$/', 'message'=>'must be a 6 digit hexidecimal value.'),
        ),
        'filters'=>array(
        ),
      ),
      'currency'=>array(
        'validators'=>array(
          array('type'=>'regex', 'method'=>'/^\$?\d+(?:\.\d{2})?$/', 'message'=>'must be a $xx.xx format.'),    
        ),
        'filters'=>array(
          array('type'=>'regex', 'method'=>'/^\$?(\d+(?:\.\d{2})?)$/')      
        ),
      ),
    ),
  );

}