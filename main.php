<?php
/**
########################################################
#                                                      #
#   CODE  : OpenLoop Bot v1.0 (Exstension 0.1.2)       #
#   PHP   : PHP 8.4.3 (cli), Zend Engine v4.4.3        #
#   Author: Furqonflynn (cmalf)                        #
#   TG    : https://t.me/furqonflynn                   #
#   GH    : https://github.com/cmalf                   #
#                                                      #
########################################################
*/
/**
 * This code is open-source and welcomes contributions!
 *
 * If you'd like to add features or improve this code, please follow these steps:
 * 1. Fork this repository to your own GitHub account.
 * 2. Make your changes in your forked repository.
 * 3. Submit a pull request to the original repository.
 *
 * This allows the original author to review your contributions and ensure the codebase maintains high quality.
 *
 * Let's work together to improve this project!
 *
 * P.S. Remember to always respect the original author's work and avoid plagiarism.
 * Let's build a community of ethical and collaborative developers.
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define ANSI color codes
$Colors = [
    'bl'    => "\x1b[38;5;27m",
    'gl'    => "\x1b[38;5;46m",
    'gr'    => "\x1b[32m",
    'gb'    => "\x1b[4m",
    'br'    => "\x1b[34m",
    'st'    => "\x1b[9m",
    'yl'    => "\x1b[33m",
    'am'    => "\x1b[38;5;198m",
    'rd'    => "\x1b[31m",
    'ug'    => "\x1b[38;5;165m",
    'rt'    => "\x1b[0m",
    'Green' => "\x1b[32m",
    'Red'   => "\x1b[31m",
    'Yellow'=> "\x1b[93m",
    'Teal'  => "\x1b[38;5;51m",
    'Neon'  => "\x1b[38;5;198m",
    'Gold'  => "\x1b[38;5;220m",
    'Dim'   => "\x1b[2m",
    'RESET' => "\x1b[0m"
];

// Check if cURL extension is installed
if (!extension_loaded('curl')) {
    $installInstructions = "Error: PHP cURL extension is not installed.\n\n";
    $installInstructions .= "Installation instructions:\n\n";
    $installInstructions .= "For Windows:\n";
    $installInstructions .= "1. Open php.ini file\n";
    $installInstructions .= "2. Uncomment extension=curl\n";
    $installInstructions .= "3. Restart your web server\n\n";
    $installInstructions .= "For Linux (Ubuntu/Debian):\n";
    $installInstructions .= "sudo apt-get install php-curl\n";
    $installInstructions .= "sudo service apache2 restart\n\n";
    $installInstructions .= "For macOS:\n";
    $installInstructions .= "1. Using Homebrew: brew install php@8.x\n";
    $installInstructions .= "2. Or modify php.ini to enable curl extension\n";
    die($Colors['Red'] . $installInstructions . $Colors['rt']);
}

$CoderMarkPrinted = false;

// File paths
$ACCOUNTS_FILE = __DIR__ . '/accounts.json';
$PROXY_FILE    = __DIR__ . '/proxy.txt';
$TOKEN_FILE    = __DIR__ . '/data_token.json';

// API Endpoints
$LOGIN_URL = 'https://api.openloop.so/users/login';
$BANDWIDTH_SHARE_URL = 'https://api.openloop.so/bandwidth/share';
function TASK_URL($missionId) {
    return "https://api.openloop.so/missions/{$missionId}/complete";
}

// User Agents Array
$userAgents = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Edge/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Edge/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Edge/120.0.0.0",
    "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2.1 Safari/605.1.15",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:133.0) Gecko/20100101 Firefox/133.",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.3",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36 OPR/114.0.0.",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.3",
    "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.3",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 AtContent/95.5.5462.5",
];

// Create common headers
$COMMON_HEADERS = [
    'User-Agent'      => $userAgents[array_rand($userAgents)],
    'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7,zh-TW;q=0.6,zh;q=0.5'
];

/**
 * Utility function to format date as "yyyy-MM-dd HH:mm:ss"
 */
function formatDate($date) {
    return $date->format('Y-m-d H:i:s');
}

/**
 * Log message with color and timestamp.
 * messageType can be: success, error, warning, process, info.
 */
function logMessage($message = "", $messageType = "info", $currentAccount = 0, $totalAccounts = 0) {
    global $Colors;
    $timestamp = formatDate(new DateTime());

    $colors = [
        'success' => $Colors['Green'],
        'error'   => $Colors['Red'],
        'warning' => $Colors['Yellow'],
        'process' => $Colors['Neon'],
        'info'    => $Colors['Teal']
    ];

    $logColor = isset($colors[$messageType]) ? $colors[$messageType] : $colors['info'];
    $line = str_repeat('―', 70);
    echo $line . PHP_EOL;
    echo "{$Colors['Dim']}[{$timestamp}] {$Colors['RESET']}{$Colors['Gold']}> {$logColor}{$message}{$Colors['RESET']}" . PHP_EOL;
    echo $line . PHP_EOL;
}

/**
 * Display CoderMark (banner)
 */
function CoderMark() {
    global $CoderMarkPrinted, $Colors;
    if (!$CoderMarkPrinted) {
        echo "
╭━━━╮╱╱╱╱╱╱╱╱╱╱╱╱╱╭━━━┳╮
┃╭━━╯╱╱╱╱╱╱╱╱╱╱╱╱╱┃╭━━┫┃{$Colors['gr']}
┃╰━━┳╮╭┳━┳━━┳━━┳━╮┃╰━━┫┃╭╮╱╭┳━╮╭━╮
┃╭━━┫┃┃┃╭┫╭╮┃╭╮┃╭╮┫╭━━┫┃┃┃╱┃┃╭╮┫╭╮╮{$Colors['br']}
┃┃╱╱┃╰╯┃┃┃╰╯┃╰╯┃┃┃┃┃╱╱┃╰┫╰━╯┃┃┃┃┃┃┃
╰╯╱╱╰━━┻╯╰━╮┣━━┻╯╰┻╯╱╱╰━┻━╮╭┻╯╰┻╯╰╯{$Colors['rt']}
╱╱╱╱╱╱╱╱╱╱╱┃┃╱╱╱╱╱╱╱╱╱╱╱╭━╯┃{$Colors['am']}{{$Colors['rt']}cmalf{$Colors['am']}}{$Colors['rt']}
╱╱╱╱╱╱╱╱╱╱╱╰╯╱╱╱╱╱╱╱╱╱╱╱╰━━╯
\n{$Colors['rt']}{$Colors['gb']} OpenLoop Bot {$Colors['gl']}PHP {$Colors['bl']}v1.0 {$Colors['rt']}
    \n{$Colors['gr']}――――――――――――――――――――――――――――――――――――――――――――――――――――――――――――
    \n{$Colors['yl']}[+]{$Colors['rt']} DM : {$Colors['bl']}https://t.me/furqonflynn
    \n{$Colors['yl']}[+]{$Colors['rt']} GH : {$Colors['bl']}https://github.com/cmalf/
    \n{$Colors['gr']}――――――――――――――――――――――――――――――――――――――――――――――――――
    \n{$Colors['yl']}]-> {$Colors['am']}{ {$Colors['rt']}OpenLoop Extension{$Colors['bl']} v0.1.2{$Colors['am']} } {$Colors['rt']}
    \n{$Colors['gr']}――――――――――――――――――――――――――――――――――――――――――――――――――{$Colors['rt']}
      " . PHP_EOL;
        $CoderMarkPrinted = true;
    }
}

/**
 * Hide parts of the email for privacy.
 */
function hideEmail($email) {
    if (empty($email) || strpos($email, '@') === false) {
        return $email;
    }
    list($local, $domain) = explode('@', $email, 2);
    $domainParts = explode('.', $domain);
    $tld = array_pop($domainParts);
    $hideDomain = str_repeat('*', strlen($domain) - strlen($tld) - 1) . '.' . $tld;
    $hideLocal = substr($local, 0, 3) . str_repeat('*', max(strlen($local) - 6, 3)) . substr($local, -3);
    return "{$hideLocal}@{$hideDomain}";
}

/**
 * Utility function to read proxies from file
 */
function loadProxies() {
    global $PROXY_FILE;
    if (!file_exists($PROXY_FILE)) {
        logMessage("Proxy file not found: {$PROXY_FILE}", "error");
        return [];
    }
    $content = file_get_contents($PROXY_FILE);
    $lines = explode("\n", $content);
    $proxies = [];
    foreach ($lines as $line) {
        $trim = trim($line);
        if (!empty($trim)) {
            $proxies[] = $trim;
        }
    }
    return $proxies;
}

/**
 * Utility function to get a random proxy from list
 */
function getRandomProxy($proxies) {
    if (empty($proxies)) {
        return null;
    }
    return $proxies[array_rand($proxies)];
}

/**
 * Utility function to get a random quality for bandwidth share
 */
function getRandomQuality() {
    $qualities = [68, 76, 88, 99];
    return $qualities[array_rand($qualities)];
}

/**
 * Read accounts.json file. Expecting an array of account objects.
 */
function loadAccounts() {
    global $ACCOUNTS_FILE;
    if (!file_exists($ACCOUNTS_FILE)) {
        logMessage("Accounts file not found: {$ACCOUNTS_FILE}", "error");
        return [];
    }
    $content = file_get_contents($ACCOUNTS_FILE);
    $accounts = json_decode($content, true);
    if ($accounts === null) {
        logMessage("Error decoding accounts file.", "error");
        return [];
    }
    return $accounts;
}

/**
 * Save token data to data_token.json file
 */
function saveTokenData($tokenData) {
    global $TOKEN_FILE;
    $jsonData = json_encode($tokenData, JSON_PRETTY_PRINT);
    if (file_put_contents($TOKEN_FILE, $jsonData) !== false) {
        logMessage("Token data saved to {$TOKEN_FILE}", "success");
    } else {
        logMessage("Error saving token data to {$TOKEN_FILE}", "error");
    }
}

/**
 * Load token data from data_token.json file
 */
function loadTokenData() {
    global $TOKEN_FILE;
    if (!file_exists($TOKEN_FILE)) {
        logMessage("Token file not found: {$TOKEN_FILE}", "error");
        return [];
    }
    $content = file_get_contents($TOKEN_FILE);
    $tokenData = json_decode($content, true);
    if ($tokenData === null) {
        logMessage("Error decoding token data.", "error");
        return [];
    }
    return $tokenData;
}

/**
 * Function for making HTTP requests with proxy support using cURL.
 */
function makeRequest($params) {
    $url    = isset($params['url']) ? $params['url'] : '';
    $method = isset($params['method']) ? strtoupper($params['method']) : 'GET';
    $headersArray = [];
    $headers = isset($params['headers']) ? $params['headers'] : [];
    foreach ($headers as $key => $value) {
        $headersArray[] = "{$key}: {$value}";
    }
    $data   = isset($params['data']) ? $params['data'] : null;
    $proxy  = isset($params['proxy']) ? $params['proxy'] : null;
    $timeout = 30;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headersArray);
/**
 * Enable this code if you have problems with SSL certificate issues.
 */    
    // Disable SSL peer verification to solve certificate issues.
    // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
    // Set method and data for POST
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data !== null) {
            $jsonData = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        }
    } else {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    }
    
    // Set proxy if provided
    if ($proxy !== null) {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
    }
    
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        logMessage("Request error ({$url}): " . curl_error($ch), "error");
        curl_close($ch);
        return null;
    }
    curl_close($ch);
    
    // Attempt to decode JSON. If not JSON, return raw response.
    $decoded = json_decode($response, true);
    return $decoded !== null ? $decoded : $response;
}

/**
 * Option 1: Login All Accounts
 */
function loginAllAccounts() {
    global $COMMON_HEADERS, $LOGIN_URL;
    logMessage('Logging in all accounts...', "info");
    $accounts = loadAccounts();
    $proxies  = loadProxies();
    $tokenData = [];

    foreach ($accounts as $account) {
        $proxy = getRandomProxy($proxies);
        $agentInfo = $proxy ? "(Using proxy: {$proxy})" : "(No proxy)";
        logMessage("Logging in: {$account['email']} {$agentInfo}", "process");

        // Merge common headers with additional headers
        $headers = array_merge($COMMON_HEADERS, [
            'accept'        => 'application/json',
            'content-type'  => 'application/json'
        ]);

        $payload = [
            'username' => $account['email'],
            'password' => $account['password']
        ];

        $response = makeRequest([
            'url'    => $LOGIN_URL,
            'method' => 'POST',
            'headers'=> $headers,
            'data'   => $payload,
            'proxy'  => $proxy
        ]);

        if ($response && isset($response['code']) && $response['code'] === 2000 && isset($response['data']['accessToken'])) {
            logMessage("Login success: {$account['email']}", "success");
            $tokenData[] = [
                'email'       => $account['email'],
                'accessToken' => $response['data']['accessToken']
            ];
        } else {
            $errMsg = isset($response['message']) ? $response['message'] : 'No response';
            logMessage("Login failed for {$account['email']}: {$errMsg}", "error");
        }
    }

    saveTokenData($tokenData);
}

/**
 * Complete Task All Accounts
 *
 * For each account, retrieve the missions list using GET request.
 * For every mission with status "available", perform:
 *  - A GET request to the mission's social link.
 *  - Then, complete the mission in two steps:
 *      1. First, send an OPTIONS request to the mission complete endpoint without the Authorization header.
 *         This request includes headers to simulate a CORS preflight and waits for 10 seconds.
 *      2. Second, send a GET request to the mission complete endpoint with the Authorization header.
 *         This finalizes the mission completion.
 *
 * Additionally, after processing all missions for an account:
 *  - If no mission is available from the start, log that all tasks are already complete.
 *  - If available missions were processed and all completed successfully, log that all tasks have been completed.
 *  - Otherwise, log the number of tasks that could not be completed.
 */
function completeTaskAllAccounts() {
    global $COMMON_HEADERS;
    logMessage('Completing tasks for all accounts...', "info");
    $tokenData = loadTokenData();
    $proxies = loadProxies();

    foreach ($tokenData as $tokenEntry) {
        $hiddenEmail = hideEmail($tokenEntry['email']);
        logMessage("Processing tasks for {$hiddenEmail}", "process");

        $proxy = getRandomProxy($proxies);

        // Prepare headers for missions list request with authorization
        $authHeaders = array_merge($COMMON_HEADERS, [
            'Accept'        => '*/*',
            'Origin'        => 'chrome-extension://effapmdildnpkiaeghlkicpfflpiambm',
            'Authorization' => "Bearer " . $tokenEntry['accessToken']
        ]);

        // Retrieve missions list
        $missionListResponse = makeRequest([
            'url'     => 'https://api.openloop.so/missions',
            'method'  => 'GET',
            'headers' => $authHeaders,
            'proxy'   => $proxy
        ]);

        if (!$missionListResponse || !isset($missionListResponse['code']) || $missionListResponse['code'] !== 2000) {
            logMessage("Failed to retrieve missions for {$hiddenEmail}.", "error");
            continue;
        }

        if (!isset($missionListResponse['data']['missions']) || !is_array($missionListResponse['data']['missions'])) {
            logMessage("No missions found for {$hiddenEmail}.", "warning");
            continue;
        }

        // Counters for tracking mission statuses
        $totalAvailable = 0;
        $completedCount = 0;

        // Process each available mission
        foreach ($missionListResponse['data']['missions'] as $mission) {
            if (isset($mission['status']) && $mission['status'] === "available") {
                $totalAvailable++;
                $missionId   = $mission['missionId'];
                $missionName = $mission['name'];
                $socialLink  = isset($mission['social']['link']) ? $mission['social']['link'] : '';

                logMessage("Account {$hiddenEmail}: Processing Mission {$missionId} - {$missionName}", "process");

                // Step 1: Access the social link if provided
                if (!empty($socialLink)) {
                    logMessage("Accessing social link: {$socialLink}", "info");
                    // Execute GET request to the social link; response is not used
                    makeRequest([
                        'url'     => $socialLink,
                        'method'  => 'GET',
                        'headers' => $COMMON_HEADERS,
                        'proxy'   => $proxy
                    ]);
                } else {
                    logMessage("Social link not provided for Mission {$missionId}", "warning");
                }

                // Step 2: First, send an OPTIONS request without the Authorization header.
                $optionsHeaders = array_merge($COMMON_HEADERS, [
                    'Accept'                         => '*/*',
                    'Origin'                         => 'chrome-extension://effapmdildnpkiaeghlkicpfflpiambm',
                    'Access-Control-Request-Method'  => 'GET',
                    'Access-Control-Request-Headers' => 'authorization'
                    // Authorization header is intentionally omitted.
                ]);

                $completeUrl = TASK_URL($missionId);
                logMessage("Sending preflight OPTIONS request for Mission {$missionId}", "info");
                $optionsResponse = makeRequest([
                    'url'     => $completeUrl,
                    'method'  => 'OPTIONS',
                    'headers' => $optionsHeaders,
                    'proxy'   => $proxy
                ]);

                // Wait for 10 seconds as per instructions to simulate delay after preflight.
                logMessage("Waiting for 10 seconds after OPTIONS request for Mission {$missionId}", "info");
                sleep(10);

                // Step 3: Send the actual GET request with Authorization header to complete the mission.
                logMessage("Sending final GET request (with Authorization) for Mission {$missionId}", "info");
                $completeResponse = makeRequest([
                    'url'     => $completeUrl,
                    'method'  => 'GET',
                    'headers' => $authHeaders,
                    'proxy'   => $proxy
                ]);

                if ($completeResponse && isset($completeResponse['code']) && $completeResponse['code'] === 2000) {
                    logMessage("{$hiddenEmail} Mission {$missionId} completed: " . $completeResponse['message'], "success");
                    $completedCount++;
                } else {
                    $errMsg = isset($completeResponse['message']) ? $completeResponse['message'] : 'No response';
                    logMessage("{$hiddenEmail} Mission {$missionId} failed: {$errMsg}", "error");
                }
            }
        }

        // Log mission completion summary for the account.
        if ($totalAvailable === 0) {
            logMessage("{$hiddenEmail}: All tasks have already been completed.", "success");
        } elseif ($completedCount === $totalAvailable) {
            logMessage("{$hiddenEmail}: All {$completedCount} available tasks have now been completed.", "success");
        } else {
            $remaining = $totalAvailable - $completedCount;
            logMessage("{$hiddenEmail}: {$completedCount} tasks completed, but {$remaining} task(s) still remain available.", "warning");
        }
    }
}

/**
 * Option 3: Run Bandwidth Share (runs every few minutes)
 */
function runBandwidthShare() {
    global $COMMON_HEADERS;
    logMessage('Starting Bandwidth Share process...', "info");
    $tokenData = loadTokenData();
    $proxies = loadProxies();

    foreach ($tokenData as $tokenEntry) {
        $proxy = getRandomProxy($proxies);
        $quality = getRandomQuality();
        $headers = array_merge($COMMON_HEADERS, [
            'Content-Type'  => 'application/json',
            'Accept'        => '*/*',
            'Origin'        => 'chrome-extension://effapmdildnpkiaeghlkicpfflpiambm',
            'Authorization' => "Bearer " . $tokenEntry['accessToken']
        ]);

        $payload = [
            'quality' => $quality
        ];

        $response = makeRequest([
            'url'    => $GLOBALS['BANDWIDTH_SHARE_URL'],
            'method' => 'POST',
            'headers'=> $headers,
            'data'   => $payload,
            'proxy'  => $proxy
        ]);

        if ($response && isset($response['code']) && $response['code'] === 2000) {
            $point = isset($response['data']['balances']['POINT']) ? $response['data']['balances']['POINT'] : 'N/A';
            // Using reset color for POINT output
            logMessage(hideEmail($tokenEntry['email']) . " " . $GLOBALS['Colors']['Neon'] . "POINT: {$point}", "success");
        } else {
            $errMsg = isset($response['message']) ? $response['message'] : 'No response';
            logMessage(hideEmail($tokenEntry['email']) . " Bandwidth Share failed: {$errMsg}", "error");
        }
    }
}

/**
 * Function to repeatedly run bandwidth share every few minutes.
 * This function will run indefinitely until interrupted.
 */
function startBandwidthShareInterval() {
    // Run initially
    runBandwidthShare();
    while (true) {
        // Sleep for 3 minutes (180 seconds)
        sleep(180);
        runBandwidthShare();
    }
}

/**
 * Display menu and process user input.
 */
function showMenu() {
    global $Colors;
    echo "{$Colors['Teal']}Menu Options:" . PHP_EOL . PHP_EOL;
    echo "{$Colors['RESET']}1. Login All Accounts" . PHP_EOL;
    echo "{$Colors['RESET']}2. Complete Task All Accounts" . PHP_EOL;
    echo "{$Colors['RESET']}3. Run Bandwidth Share" . PHP_EOL;
    echo "{$Colors['RESET']}4. {$Colors['Red']}Exit{$Colors['RESET']}" . PHP_EOL . PHP_EOL;
    echo "Choose an option: ";
}

/**
 * Main function to run the CLI interactive menu.
 */
function main() {
    // Clear console
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        system('cls');
    } else {
        system('clear');
    }
    CoderMark();

    while (true) {
        showMenu();
        $input = trim(fgets(STDIN));
        switch ($input) {
            case '1':
                loginAllAccounts();
                break;
            case '2':
                completeTaskAllAccounts();
                break;
            case '3':
                // Option 3 will run indefinitely.
                logMessage($GLOBALS['Colors']['Neon'] . "Bandwidth share process. " . $GLOBALS['Colors']['RESET'] . "(Press Ctrl+C to exit)", "info");
                startBandwidthShareInterval();
                // This point will not be reached unless an interrupt occurs.
                break;
            case '4':
                logMessage("Exiting...", "info");
                exit(0);
                break;
            default:
                logMessage("Invalid option. Please select 1, 2, 3, or 4.", "warning");
                break;
        }
    }
}

main();
?>
