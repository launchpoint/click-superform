<?

function superform_validate($obj)
{
  foreach($obj->superform_sections as $section=>$fields)
  {
    superform_validate_fields($obj, $fields);
  }  
}

function superform_validate_fields($obj, $fields)
{
  $fields = superform_fixup_fields_structure($obj, $fields);
  foreach($fields as $field=>$data)
  {
    if($data['type']=='subsection')
    {
      superform_validate_fields($obj,$data['fields']);
      continue;
    }
    if (isset($obj->errors['field'])) continue;
    switch($data['type'])
    {
      case 'mutex':
        $fn = "update_{$field}";
        if(!$obj->responds_to($fn))
        {
          click_error("Mutex {$field} requires that $fn() be defined.");
        }
        break;
    }
    if($data['required']==false)
    {
      switch($data['type'])
      {
        case 'file':
        case 'image':
          if(!$obj->$field) continue 2;
          break;
        case 'mutex':
          continue 2;
          break;
        default:
          if(trim($obj->$field)=='') continue 2;
      }
    }
    foreach($data['validators'] as $rule)
    {
      switch($rule['type'])
      {
        case 'regex':
          switch($data['type'])
          {
            case 'image':
              $prop = "{$field}_id";
              $is_valid = $obj->$prop != null;
              break;
            case 'mutex':
              $is_valid = count($obj->$field)>0;
              break;
            default:
              $is_valid = preg_match($rule['method'], $obj->$field);
              break;
          }
          break;
        case 'function':
          $is_valid = call_user_func($rule['method'], $obj, $field);
          break;
        default:
          click_error('Invalid validator rule', $data['validators']);
      }
      if (!$is_valid)
      {
        if(!isset($rule['message'])) $rule['message'] = 'is invalid';
        $obj->errors[$field] = $rule['message'];
      }
    }
    if (isset($obj->errors['field'])) continue;
    foreach($data['filters'] as $filter)
    {
      switch($filter['type'])
      {
        case 'regex':
          preg_match($filter['method'], $obj->$field, $matches);
          if(count($matches)>0) $obj->$field = $matches[1];
          break;
        default:
          click_error("Invalid field filter on $field", $data['filters']);
      }
    }
  }
}