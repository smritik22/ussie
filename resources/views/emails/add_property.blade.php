<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentAddProperty($id, $language_id, $agent_email, $full_name, $phone, $country_code, $property);
?>
<div>{!! $content !!}</div>