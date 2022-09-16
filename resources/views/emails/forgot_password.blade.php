<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentForgotpassword($id,$email,$name,$url,$logo);
?>
<div>{!! $content !!}</div>
