
<?php
use App\Helpers;

 $content = Helper::getEmailtemplateStudentTakeActionRecieve($id,$take_action_name,$messages,$logo);
?>
<div>{!! $content !!}</div>

