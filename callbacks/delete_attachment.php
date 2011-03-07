<?

$attachment = Attachment::find_by_id($params['id']);
$attachment->delete();