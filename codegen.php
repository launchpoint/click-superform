<?

foreach($models as $model_klass)
{
  $t = singularize(tableize($model_klass));
  $table_name = eval("return $model_klass::\$table_name;");
  $code = <<<PHP
function superform_{$t}_validate(\$event_args, \$event_data)
{
  codegen_superform_validate(\$event_args, \$event_data, '$t');
}

function superform_{$t}_after_new(\$event_args, \$event_data)
{
  return codegen_superform_after_new(\$event_args, \$event_data, '$t');
}

function superform_{$t}_update_attributes(\$event_args, \$event_data)
{
  return codegen_superform_update_attributes(\$event_args, \$event_data, '$t');
}

function superform_{$t}_after_update_attributes(\$event_args, \$event_data)
{
  return codegen_superform_after_update_attributes(\$event_args, \$event_data, '$t');
}

function {$t}_superform(\$obj, \$sections=null, \$vars=null)
{
  return codegen_superform(\$obj, \$sections, \$vars);
}

function {$t}_superform_field(\$obj, \$field_name)
{
  return codegen_superform_field(\$obj, \$field_name);
}
PHP;
  $codegen[] = $code;
}

