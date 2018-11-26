<?php

return [

    'oauth' => [
        'callback'    => 'http://localhost/test',
        'consumer_key'      => 'EPFSC1CZJEYTQQPCXH71QMN6PKBPXV',
        'consumer_secret'   => 'DYFAXQBZWTVUUFVGEFTQOWKWH9P3VN',
        'rsa_private_key'  => file_get_contents('../certs/atp_private.pem'),
    ],
    'curl' => [
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => 0
    ]


];



