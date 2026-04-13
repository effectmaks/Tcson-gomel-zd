<?php

return array(
    'host' => getenv('DB_HOST') ?: 'db',
    'user' => getenv('DB_USER') ?: 'tcsonrw_gomel_site',
    'password' => getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : 'tcsonrw_gomel_local',
    'name' => getenv('DB_NAME') ?: 'tcsonrw_gomel_site',
    'port' => (int) (getenv('DB_PORT') ?: 3306),
);
