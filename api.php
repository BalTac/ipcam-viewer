<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Imposta la timezone corretta
date_default_timezone_set('Europe/Rome');

// Carica la configurazione
$configFile = __DIR__ . '/config.json';
if (!file_exists($configFile)) {
    echo json_encode([
        'error' => 'File di configurazione non trovato. Rinomina o crea config.json.'
    ]);
    exit;
}

$config = json_decode(file_get_contents($configFile), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'error' => 'Errore nel parsing di config.json: ' . json_last_error_msg()
    ]);
    exit;
}

// Funzioni per lo Scanner di Rete
function scanHost($ip, $ports, $timeout = 0.5) {
    $sockets = [];
    $results = [];
    foreach ($ports as $port) {
        $s = @stream_socket_client("tcp://$ip:$port", $errno, $errstr, $timeout, STREAM_CLIENT_ASYNC_CONNECT | STREAM_CLIENT_CONNECT);
        if ($s) {
            $sockets[$port] = $s;
        }
    }
    
    $write = array_values($sockets);
    $read = $except = NULL;
    if (count($write) > 0) {
        $num = @stream_select($read, $write, $except, 0, (int)($timeout * 1000000));
        if ($num > 0) {
            foreach ($sockets as $port => $s) {
                if (in_array($s, $write)) {
                    if (@fwrite($s, "") !== false) {
                        $results[] = (int)$port;
                    }
                }
                @fclose($s);
            }
        } else {
            foreach ($sockets as $s) {
                @fclose($s);
            }
        }
    }
    sort($results);
    return $results;
}

function scanSubnet($subnet, $port, $timeout = 0.5) {
    $sockets = [];
    $results = [];
    for ($i = 1; $i <= 254; $i++) {
        $ip = $subnet . $i;
        $s = @stream_socket_client("tcp://$ip:$port", $errno, $errstr, $timeout, STREAM_CLIENT_ASYNC_CONNECT | STREAM_CLIENT_CONNECT);
        if ($s) {
            $sockets[$ip] = $s;
        }
    }
    
    $write = array_values($sockets);
    $read = $except = NULL;
    if (count($write) > 0) {
        $num = @stream_select($read, $write, $except, 0, (int)($timeout * 1000000));
        if ($num > 0) {
            foreach ($sockets as $ip => $s) {
                if (in_array($s, $write)) {
                    if (@fwrite($s, "") !== false) {
                        $results[] = $ip;
                    }
                }
                @fclose($s);
            }
        } else {
            foreach ($sockets as $s) {
                @fclose($s);
            }
        }
    }
    sort($results);
    return $results;
}

// Gestione dello Scanner di Rete (GET action=scan)
if (isset($_GET['action']) && $_GET['action'] === 'scan') {
    $mode = $_GET['mode'] ?? 'host';
    $ip = $_GET['ip'] ?? '';
    
    if (empty($ip)) {
        http_response_code(400);
        echo json_encode(['error' => 'IP target mancante.']);
        exit;
    }
    
    if ($mode === 'host') {
        $ports_str = $_GET['ports'] ?? '80,8080,8081,8082,8083,8084,8085,8086';
        $ports = array_filter(array_map('intval', explode(',', $ports_str)));
        
        $open_ports = scanHost($ip, $ports);
        echo json_encode([
            'mode' => 'host',
            'ip' => $ip,
            'open_ports' => $open_ports
        ]);
        exit;
    } elseif ($mode === 'subnet') {
        $port = intval($_GET['port'] ?? 80);
        
        if (substr($ip, -1) === '.') {
            $subnet = $ip;
        } else {
            $parts = explode('.', $ip);
            if (count($parts) >= 3) {
                $subnet = $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.';
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'IP target non valido per la scansione subnet.']);
                exit;
            }
        }
        
        $active_ips = scanSubnet($subnet, $port);
        echo json_encode([
            'mode' => 'subnet',
            'subnet' => $subnet,
            'port' => $port,
            'active_ips' => $active_ips
        ]);
        exit;
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Modalità di scansione non valida.']);
        exit;
    }
}

// Gestione del salvataggio configurazione (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'JSON non valido: ' . json_last_error_msg()]);
        exit;
    }
    
    // Validazione base
    if (!isset($data['server']) || !isset($data['disks']) || !isset($data['cameras'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Struttura dati non valida. Richiesti server, disks e cameras.']);
        exit;
    }
    
    // Scrittura su file
    $success = file_put_contents($configFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    if ($success === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Impossibile scrivere il file di configurazione su disco. Verificare i permessi.']);
        exit;
    }
    
    echo json_encode(['success' => true]);
    exit;
}

// Funzioni Helper
function getClientIp() {
    $keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        $ip = getenv($key);
        if ($ip && strcasecmp($ip, 'unknown') !== 0) {
            if ($key === 'HTTP_X_FORWARDED_FOR') {
                $ips = explode(',', $ip);
                return trim($ips[0]);
            }
            return $ip;
        }
    }
    return '127.0.0.1';
}

function isLocalIp($client_ip, $prefixes) {
    if ($client_ip === '::1' || $client_ip === 'localhost') {
        return true;
    }
    foreach ($prefixes as $prefix) {
        if (strpos($client_ip, $prefix) === 0) {
            return true;
        }
    }
    return false;
}

function extIp() {
    $public_ip = "";
    $count = 0;
    do {
        $count++;
        $ctx = stream_context_create(['http' => ['timeout' => 2]]);
        $ec = @file_get_contents("https://api.ipify.org", false, $ctx);
        if ($ec) {
            $public_ip = trim($ec);
        }
    } while (empty($public_ip) && $count < 2);

    if (empty($public_ip)) {
        $public_ip = $_SERVER['SERVER_NAME'] ?? $_SERVER['SERVER_ADDR'] ?? 'localhost';
    }
    return $public_ip;
}

function formatSize($bytes) {
    $types = ['B', 'KB', 'MB', 'GB', 'TB'];
    for ($i = 0; $bytes >= 1024 && $i < (count($types) - 1); $bytes /= 1024, $i++);
    return round($bytes, 2) . " " . $types[$i];
}

// 1. Rileva client e host di connessione
$client_ip = getClientIp();
$subnet_prefixes = $config['server']['subnet_prefixes'] ?? ['192.168.', '10.', '127.0.0.1', 'localhost'];
$is_local = isLocalIp($client_ip, $subnet_prefixes);

if ($is_local) {
    $resolved_host = !empty($config['server']['local_host']) ? $config['server']['local_host'] : ($_SERVER['SERVER_ADDR'] ?? '127.0.0.1');
    if ($resolved_host === '127.0.0.1' || $resolved_host === '::1') {
        $resolved_host = 'localhost';
    }
} else {
    $resolved_host = !empty($config['server']['remote_host']) ? $config['server']['remote_host'] : extIp();
}

// 2. Calcola spazio dischi
$disk_stats = [];
if (!empty($config['disks']) && is_array($config['disks'])) {
    foreach ($config['disks'] as $disk) {
        $path = $disk['path'];
        $dt = @disk_total_space($path);
        $df = @disk_free_space($path);
        if ($dt !== false && $df !== false && $dt > 0) {
            $du = $dt - $df;
            $dp = round(($du / $dt) * 100, 2);
            $disk_stats[] = [
                'name' => $disk['name'],
                'path' => $path,
                'total' => formatSize($dt),
                'used' => formatSize($du),
                'free' => formatSize($df),
                'percentage' => $dp,
                'ok' => true
            ];
        } else {
            $disk_stats[] = [
                'name' => $disk['name'],
                'path' => $path,
                'total' => 'N/D',
                'used' => 'N/D',
                'free' => 'N/D',
                'percentage' => 0,
                'ok' => false
            ];
        }
    }
}

// 3. Risolvi i link delle camere
$cameras = [];
if (!empty($config['cameras']) && is_array($config['cameras'])) {
    foreach ($config['cameras'] as $cam) {
        $cam['stream_url'] = str_replace('{host}', $resolved_host, $cam['stream_url']);
        $cameras[] = $cam;
    }
}

// 4. Calcola ora del server
$days = ["Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato"];
$months = ["", "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"];

$wday = (int)date('w');
$mday = date('j');
$month = (int)date('n');
$year = date('Y');

$date_str = $days[$wday] . " " . $mday . " " . $months[$month] . " " . $year;
$time_str = date('H:i:s');

// 5. Output JSON finale
echo json_encode([
    'server_time' => [
        'date' => $date_str,
        'time' => $time_str
    ],
    'connection' => [
        'client_ip' => $client_ip,
        'is_local' => $is_local,
        'resolved_host' => $resolved_host,
        'type' => $is_local ? 'Locale' : 'Remota'
    ],
    'disks' => $disk_stats,
    'cameras' => $cameras,
    'audio_enabled_default' => (bool)($config['server']['audio_enabled_default'] ?? true),
    'raw_config' => $config
]);
