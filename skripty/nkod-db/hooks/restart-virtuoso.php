<?php
// where to log errors and successful requests
define('LOGFILE', '/data/gh-logs/virtuoso.log');

// what command to execute upon retrieval of a valid push event
$cmd = 'sudo /etc/init.d/virtuoso-opensource01 stop && sudo /etc/init.d/virtuoso-opensource01 start';

function log_msg($msg) {
        if(LOGFILE != '') {
                file_put_contents(LOGFILE, $msg . "\n", FILE_APPEND);
        }
}

log_msg("=== Received request from {$_SERVER['REMOTE_ADDR']} ===");
header("Content-Type: text/plain");
passthru($cmd);
?>