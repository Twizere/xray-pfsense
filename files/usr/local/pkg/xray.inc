<?php
// Function to restart the xray service
function xray_restart() {
    exec("service xray restart", $output, $return_var);
    return [
        'output' => $output,
        'status' => $return_var === 0 ? "Restarted successfully" : "Failed to restart"
    ];
}

// Function to stop the xray service
function xray_stop() {
    exec("service xray stop", $output, $return_var);
    return [
        'output' => $output,
        'status' => $return_var === 0 ? "Stopped successfully" : "Failed to stop"
    ];
}

// Function to start the xray service
function xray_start() {
    exec("service xray start", $output, $return_var);
    return [
        'output' => $output,
        'status' => $return_var === 0 ? "Started successfully" : "Failed to start"
    ];
}

// Function to check the status of the xray service
function xray_status() {
    exec("service xray status", $output, $return_var);
    $is_running = strpos(implode("\n", $output), 'active (running)') !== false;

    return [
        'output' => $output,
        'status' => $is_running ? "Service is running" : "Service is not running"
    ];
}
?>
