<?php

$headers = array(
    "Content-Security-Policy: default-src 'self'; img-src * 'self' data: https:",
    "X-XSS-Protection: 1; mode=block",
    "X-Frame-Options: SAMEORIGIN",
    "X-Content-Type-Options: nosniff"
);

foreach($headers as $h){header($h);}
?>