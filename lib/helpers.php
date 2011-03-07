<?

function superform($obj, $sections=null, $vars = null)
{
  if($sections) $obj->superform_sections = $sections;
  if($vars) $obj->superform_vars = $vars;
  if(count($obj->superform_sections)==0) click_error("No sections defined for form.");

  superform_fixup_sections_structure($obj);
  $params['obj'] = $obj;
  event('render_superform', $params);
}


function superform_create_options($obj, $data, $field_name)
{
  if(is_array($data[$field_name]))
  {
    $arr = $data[$field_name];
  } else {
    if($obj->responds_to($data[$field_name]))
    {
      $arr = $obj->$data[$field_name];
    } elseif (function_exists($data[$field_name]))
    {
      $arr = call_user_func($data[$field_name]);
    } else {
      $arr = split(',',$data[$field_name]);
    }
  }
  
  if(!is_array($arr))
  {
    click_error("{$obj->klass} does not respond to {$data[$field_name]}");
  }
  $options = array();
  foreach($arr as $k=>$option)
  {
    if(is_object($option))
    {
      $options[] = $option;
      continue;
    }
    $option = trim($option);
    if(is_numeric($k)) $k=$option;
    $options[] = (object)array('id'=>$k, 'name'=>$option);
  }
  return $options;
}

function superform_find_field_in_section($section, $needle)
{
  foreach($section as $field_name=>$field_info)
  {
    if($field_name==$needle) return $field_info;
    if(isset($field_info['type']) && $field_info['type']=='subsection')
    {
      $ret = superform_find_field_in_section($section, $field_name);
      if($ret) return $ret;
    }
  }
  return null;
}
