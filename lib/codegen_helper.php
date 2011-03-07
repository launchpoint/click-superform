<?

function codegen_superform_purge_old_associations($event_args, $event_data, $t)
{
  $obj = $event_args[$t];
  if(!isset($obj->superform_sections)) return;
  superform_fixup_sections_structure($obj);
  foreach($obj->superform_sections as $section=>$fields)
  {
    codegen_superform_purge_old_associations_r($obj,$fields);
  }
}

function codegen_superform_purge_old_associations_r($obj,$fields)
{
  foreach($fields as $key=>$data)
  {
    switch($data['type'])
    {
      case 'subsection':
        codegen_superform_purge_old_associations_r($obj,$data['fields']);
        break;
      case 'file':
      case 'image':
        $obj->purge($key);
        break;
    }
  }
}


function codegen_superform_validate($event_args, $event_data, $t)
{
  $obj = $event_args[$t];
  if(!isset($obj->superform_sections)) return;
  superform_fixup_sections_structure($obj);
  superform_validate($obj);
}

function codegen_superform_after_new($event_args, $event_data, $t)
{
  $obj = $event_args[$t];
  $obj->superform_vars = array();
  $obj->superform_sections = array();
  $obj->superform_cmd = 'commit';
  $obj->superform_action = null;
}

function codegen_superform_update_attributes($event_args, $event_data, $t)
{
  $obj = $event_args[$t];
  if(!isset($obj->superform_sections)) return;
  superform_fixup_sections_structure($obj);
  $params = $event_args['params'];
  foreach($params as $k=>$v)
  {
    $field = $obj->superform_field($k);
    switch($field['type'])
    {
      case 'currency':
        $obj->$k = preg_replace("/[^\d\.]/", '', $v);
        break;
      default:
    }
  }
}

function codegen_superform($obj, $sections, $vars)
{
  superform($obj, $sections, $vars);
}

function codegen_superform_field($obj, $needle)
{
  foreach($obj->superform_sections as $section_name=>$section)
  {
    $ret = superform_find_field_in_section($section, $needle);
    if($ret) return $ret;
  }
  return null;
}

function codegen_superform_after_update_attributes($event_args, $event_data, $t)
{
  codegen_superform_purge_old_associations($event_args, $event_data, $t);

  $obj = $event_args[$t];
  if(!isset($obj->superform_sections)) return;
  if(!$obj->is_valid) return;
  
  $params = $event_args['params'];

  foreach($obj->superform_sections as $section_name=>$fields)
  {
    codegen_superform_after_update_attributes_r($obj, $params, $fields);
  }
}

function codegen_superform_after_update_attributes_r($obj, $params, $fields)
{
  foreach($fields as $field_name=>$field_info)
  {
    switch($field_info['type'])
    {
      case 'mutex':
        $fn = "update_{$field_name}";
        $arr = array();
        if(isset($params[$field_name])) $arr = $params[$field_name];
        $obj->$fn($arr);
        
        break;
      case 'subsection':
        codegen_superform_after_update_attributes_r($obj, $params, $field_info['fields']);
        break;
    }
  }
}