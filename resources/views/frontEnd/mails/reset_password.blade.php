<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentResetpassword($id,$name,$link,$logo);
?>
<div>{!! $content !!}</div>

