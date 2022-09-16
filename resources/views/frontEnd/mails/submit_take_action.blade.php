
<?php
use App\Helpers;

 $content = Helper::getEmailtemplateSubmitTakeAction($id,$take_action_name,$student_name,$student_email,$submit_date,$logo,$url);
?>
<div>{!! $content !!}</div>

