<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentPropertyInquiry($id, $language_id, $email, $user_email, $name, $phone, $country_code, $inquiry_message, $agent_id, $property_id, $url, $logo);
?>
<div>{!! $content !!}</div>