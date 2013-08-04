<?php

function getUrl($url, $postData) {
    //echo "postData: ".$postData;

    $agent = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.3) Gecko/20100404 Ubuntu/10.04 (lucid) Firefox/3.6.3';
    $header[] = 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*'.'/*;q=0.5';
    $header[] = 'Accept-Language: cs,en-us;q=0.7,en;q=0.3';
    $header[] = 'Accept-Charset: windows-1250,utf-8;q=0.7,*;q=0.7';

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1); ########### debug
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    if ($postData != NULL):
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);        
    endif;

    $tmp = curl_exec ($ch);

    curl_close ($ch);
    return $tmp;
}

function startsWith($prefix, $str){
    return substr($str,0,strlen($prefix)) == $prefix;
}

?>