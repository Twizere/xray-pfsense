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
    $is_running = false;

    // Check if the output contains the text indicating the service is running
    foreach ($output as $line) {
        if (strpos($line, 'xray is running') !== false) {
            $is_running = true;
            break;
        }
    }

    return [
        'output' => $output,
        'status' => $is_running ? "Service is running" : "Service is not running"
    ];
}


// Function to generate a UUID
function xray_generate_uuid() {
    exec("/usr/local/bin/xray uuid", $output, $return_var);
    return $return_var === 0 ? trim($output[0]) : null;
}

?>
