<?php
// remove these checks if your website meets the requirements
if (version_compare(PHP_VERSION, '7.0') < 0) {
    throw new \Exception('Current PHP version: ' . phpversion() . '.  It needs to be equal to or higher than 7.0 for Anticus.', 404);
}

if (!is_writable(CACHE)) {
    $message = 'Your cache directory, '. CACHE . ' is not writable.' . PHP_EOL;
    $message .= 'The current permissions are: ' . substr(sprintf('%o', fileperms(CACHE)), -4);
    throw new \Exception($message, 404);
}

if (!is_writable(LOGS)) {
    $message = 'Your logs directory, '. LOGS . ' is not writable.' . PHP_EOL;
    $message .= 'The current permissions are: ' . substr(sprintf('%o', fileperms(LOGS)), -4);
    throw new \Exception($message, 404);
}
