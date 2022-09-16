<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentReportAgent($id, $language_id, $email, $user_email, $name, $phone, $country_code, $report_message, $agent_id, $url, $logo);
?>
<div>{!! $content !!}</div>