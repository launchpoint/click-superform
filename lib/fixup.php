<?

function superform_fixup_sections_structure($obj)
{
  if(!isset($obj->superform_sections)) return;
  $sections = array();
  foreach($obj->superform_sections as $section_name=>$fields)
  {
    if(is_numeric($section_name)) dprint("Section keys must have names.");
    $sections[$section_name] = superform_fixup_fields_structure($obj, $fields);
  }
  $obj->superform_sections = $sections;
  return $sections;
}


function superform_fixup_fields_structure($obj, $farr)
{
  global $superform_settings;
  
  $fields = array();
  foreach($farr as $field_name=>$field_info)
  {
    if(is_numeric($field_name))
    {
      $field_name = $field_info;
      $field_info = array();
    }
    $at = eval("return {$obj->klass}::\$attribute_types;");
    if(isset($at[$field_name]))
    {
      $field_info = array_merge($at[$field_name], $field_info);
    }
    if(!isset($field_info['type'])) $field_info['type'] = 'text';
    switch($field_info['type'])
    {
      case 'subsection':
        if(!isset($field_info['fields'])) click_error("Subsection must have a 'fields' array.");
        $field_info['fields'] = superform_fixup_fields_structure($obj, $field_info['fields']);
        break;
      default:
        if(!isset($field_info['required'])) $field_info['required'] = false;
        if(!isset($field_info['readonly'])) $field_info['readonly'] = false;
        if(!isset($field_info['autopostback'])) $field_info['autopostback'] = false;
        if(!isset($field_info['enabled'])) $field_info['enabled']=true;
        if(!isset($field_info['filters'])) $field_info['filters']=array();
        if(!isset($field_info['validators'])) $field_info['validators'] = array();
        if($field_info['required']==true) $field_info['validators'][] = array('type'=>'regex', 'method'=>'/^.+$/m', 'message'=>'is required.');
        if(isset($superform_settings['field_defaults'][$field_info['type']]))
        {
          $d = $superform_settings['field_defaults'][$field_info['type']];
          foreach(array('validators', 'filters') as $k)
          {
            if(!isset($d[$k])) continue;
            $field_info[$k] = array_merge($field_info[$k], $d[$k]);
          }
        }
        switch($field_info['type'])
        {
          case 'select':
            if(!isset($field_info['disable_when_empty'])) $field_info['disable_when_empty'] = true;
            if(!isset($field_info['value_field'])) $field_info['value_field'] = 'id';
            if(!isset($field_info['display_field'])) $field_info['display_field'] = 'name';
            if(!isset($field_info['item_array'])) $field_info['item_array'] = array();
            if($field_info['value_field'] == $field_info['display_field']) click_error("Value field and display field must not be the same for $section_name::$field_name");
            break;
          case 'mutex':
            if(!isset($field_info['value_field'])) $field_info['value_field'] = 'id';
            if(!isset($field_info['display_field'])) $field_info['display_field'] = 'name';
            if(!isset($field_info['unselected_item_array'])) $field_info['unselected_item_array'] = array();
            if(!isset($field_info['selected_item_array'])) $field_info['selected_item_array'] = array();
            if($field_info['value_field'] == $field_info['display_field']) click_error("Value field and display field must not be the same for $section_name::$field_name");
        }
    }
    $data = event('superform_fixup', array('field_name'=>$field_name, 'field_info'=>&$field_info));
    $fields[$field_name] = $field_info;
  }
  return $fields;
}
