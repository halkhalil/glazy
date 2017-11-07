<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |

     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
     | to accept any value.
     |
     */
    'supportsCredentials' => true,
    'allowedOrigins' => ['*'],
    'allowedHeaders' => ['*'],
//    'allowedHeaders' => ['Authorization', 'Content-Type'],
    'allowedMethods' => ['*'],
//    'allowedMethods' => ['GET','PUT','POST','DELETE','OPTIONS','PATCH','HEAD'],
    'exposedHeaders' => [],
    'maxAge' => 0,
    'hosts' => [],
];

