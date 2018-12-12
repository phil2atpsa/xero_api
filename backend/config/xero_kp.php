<?php

set_include_path(get_include_path().
    PATH_SEPARATOR.dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'certs'.DIRECTORY_SEPARATOR);


return [

    'oauth' => [
        'callback'    => 'http://localhost/test',
       // 'consumer_key'      => 'EPFSC1CZJEYTQQPCXH71QMN6PKBPXV',
        'consumer_key' => 'AFORISYAFEQPEVPXPFADLBRIYHRST7',
        //'consumer_secret'   => 'DYFAXQBZWTVUUFVGEFTQOWKWH9P3VN',
        'consumer_secret'   => 'UIK64ZUTINLOYFTXSBOHWRMCEF8LFP',
        'rsa_private_key'  => file_get_contents( 'atp.pem', true),
    ],
    'curl' => [
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => 0
    ]


];



