<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentRegistration($id,$email,$name,$url,$logo);
?>
<div>{!! $content !!}</div>