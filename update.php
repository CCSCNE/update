<?php

$clones = array(
    // Add full paths to commits to update.
    '/location/of/clone',
);


function main() {
    global $clones;
    $results = update_clones($clones);
    report_results($results);
}


function update_clones($clones)
{
    $results = array();
    foreach ($clones as $clone) {
        $results[] = update_clone($clone);
    }
    return $results;
}


function update_clone($path) {
    $commands = get_update_commands($path);
    $exit = execute_commands($commands, $stdout, $stderr);
    return array(
        'path' => $path,
        'commands' => $commands,
        'exit' => $exit,
        'stdout' => $stdout,
        'stderr' => $stderr
    );
}


function get_update_commands($path) {
    return array("cd $path && git pull");
}


function execute_commands($commands, &$stdout, &$stderr) {
    $stdout = array();
    $stderr = array();
    foreach ($commands as $command) {
        $stdout[] = $command;
        $stderr[] = $command;
        $exit = cmd_exec($command, $local_stdout, $local_stderr);
        $stdout = array_merge($stdout, $local_stdout);
        $stderr = array_merge($stderr, $local_stderr);
        if ($exit > 0) {
            return $exit;
        }
    }
}


/**
 * taken from: http://php.net/manual/en/function.shell-exec.php#67183
 */
function cmd_exec($cmd, &$stdout, &$stderr)
{
    $outfile = tempnam(".", "cmd");
    $errfile = tempnam(".", "cmd");
    $descriptorspec = array(
        0 => array("pipe", "r"),
        1 => array("file", $outfile, "w"),
        2 => array("file", $errfile, "w")
    );  
    $proc = proc_open($cmd, $descriptorspec, $pipes);

    if (!is_resource($proc)) return 255;

    fclose($pipes[0]);    //Don't really want to give any input

    $exit = proc_close($proc);
    $stdout = file($outfile);
    $stderr = file($errfile);

    unlink($outfile);
    unlink($errfile);
    return $exit;
}


function report_results($results) {
    print_html_header();
    foreach ($results as $result) {
        report_path($result);
        report_exit($result, 'exit');
        report_output($result, 'stdout');
        report_output($result, 'stderr');
    }
    print_html_footer();
}


function print_html_header() {
    print("<!doctype html>\n<html lang=\"en\"><meta charset=\"utf-8\"><title>Updates</title>\n");
    print("<style>.failed{color:red}.success{color:green}</style>\n");
    print("</head><body>\n");
    print("<h1>Updater</h1>\n");
}


function print_html_footer() {
    print("</body></html>\n");
}


function report_path($result) {
    $outcome = failed($result) ? 'failed' : 'success';
    echo "<h3>{$result['path']} <span class=\"$outcome\">$outcome</span></h3>\n";
}


function failed($result) {
    return is_exit_error($result['exit']);
}


function is_exit_error($exit_value) {
    return $exit_value > 0;
}


function report_exit($result) {
    echo "<h4>Exit code: {$result['exit']}</h4>\n";
}


function report_output($result, $channel) {
    echo "<h4>$channel</h4>\n";
    echo "<pre>";
    foreach ($result[$channel] as $line) {
        echo "$line\n";
    }
    echo "</pre>\n";
}


main();
