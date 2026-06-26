import os
import json
import shutil
import datetime
import http.server
import socketserver
import webbrowser
import socket
import argparse
import sys
from urllib.parse import urlparse, parse_qs
from concurrent.futures import ThreadPoolExecutor

def check_port(ip, port, timeout=0.5):
    try:
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.settimeout(timeout)
        result = s.connect_ex((ip, port))
        s.close()
        return port if result == 0 else None
    except Exception:
        return None

def check_ip(ip, port, timeout=0.5):
    try:
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.settimeout(timeout)
        result = s.connect_ex((ip, port))
        s.close()
        return ip if result == 0 else None
    except Exception:
        return None

class DualStackServer(http.server.SimpleHTTPRequestHandler):
    def guess_type(self, path):
        if path.endswith('.php'):
            return 'text/html'
        return super().guess_type(path)

    def do_OPTIONS(self):
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type')
        self.end_headers()

    def do_GET(self):
        parsed_url = urlparse(self.path)
        path = parsed_url.path
        
        if path == '/api.php':
            query_params = parse_qs(parsed_url.query)
            if 'action' in query_params and query_params['action'][0] == 'scan':
                self.handle_api_scan(query_params)
            else:
                self.handle_api()
        else:
            if path == '/' or path == '/ipcam.php':
                self.path = '/ipcam.php'
            super().do_GET()

    def do_POST(self):
        parsed_url = urlparse(self.path)
        path = parsed_url.path
        
        if path == '/api.php':
            self.handle_api_save()
        else:
            self.send_response(404)
            self.end_headers()
            
    def handle_api_save(self):
        try:
            content_length = int(self.headers.get('Content-Length', 0))
            post_data = self.rfile.read(content_length)
            data = json.loads(post_data.decode('utf-8'))
            
            if 'server' not in data or 'disks' not in data or 'cameras' not in data:
                self.send_response(400)
                self.send_header('Content-Type', 'application/json')
                self.send_header('Access-Control-Allow-Origin', '*')
                self.end_headers()
                self.wfile.write(json.dumps({'error': 'Struttura dati non valida.'}).encode('utf-8'))
                return
                
            config_path = os.path.join(os.getcwd(), 'config.json')
            with open(config_path, 'w', encoding='utf-8') as f:
                json.dump(data, f, indent=2, ensure_ascii=False)
                
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(json.dumps({'success': True}).encode('utf-8'))
            
        except Exception as e:
            self.send_response(500)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(json.dumps({'error': f"Impossibile salvare: {str(e)}"}).encode('utf-8'))

    def handle_api_scan(self, query_params):
        try:
            mode = query_params.get('mode', ['host'])[0]
            ip = query_params.get('ip', [''])[0]
            
            if not ip:
                self.send_response(400)
                self.send_header('Content-Type', 'application/json')
                self.send_header('Access-Control-Allow-Origin', '*')
                self.end_headers()
                self.wfile.write(json.dumps({'error': 'IP target mancante.'}).encode('utf-8'))
                return

            response_data = {}

            if mode == 'host':
                ports_str = query_params.get('ports', ['80,8080,8081,8082,8083,8084,8085,8086'])[0]
                ports = [int(p) for p in ports_str.split(',') if p.strip().isdigit()]
                
                open_ports = []
                with ThreadPoolExecutor(max_workers=20) as executor:
                    futures = [executor.submit(check_port, ip, port) for port in ports]
                    for future in futures:
                        res = future.result()
                        if res is not None:
                            open_ports.append(res)
                open_ports.sort()
                
                response_data = {
                    'mode': 'host',
                    'ip': ip,
                    'open_ports': open_ports
                }

            elif mode == 'subnet':
                port = int(query_params.get('port', ['80'])[0])
                
                if ip.endswith('.'):
                    subnet = ip
                else:
                    parts = ip.split('.')
                    if len(parts) >= 3:
                        subnet = f"{parts[0]}.{parts[1]}.{parts[2]}."
                    else:
                        self.send_response(400)
                        self.send_header('Content-Type', 'application/json')
                        self.send_header('Access-Control-Allow-Origin', '*')
                        self.end_headers()
                        self.wfile.write(json.dumps({'error': 'IP target non valido per la subnet.'}).encode('utf-8'))
                        return

                active_ips = []
                with ThreadPoolExecutor(max_workers=50) as executor:
                    futures = [executor.submit(check_ip, f"{subnet}{i}", port) for i in range(1, 255)]
                    for future in futures:
                        res = future.result()
                        if res is not None:
                            active_ips.append(res)
                active_ips.sort()
                
                response_data = {
                    'mode': 'subnet',
                    'subnet': subnet,
                    'port': port,
                    'active_ips': active_ips
                }

            else:
                self.send_response(400)
                self.send_header('Content-Type', 'application/json')
                self.send_header('Access-Control-Allow-Origin', '*')
                self.end_headers()
                self.wfile.write(json.dumps({'error': 'Modalità di scansione non valida.'}).encode('utf-8'))
                return

            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(json.dumps(response_data).encode('utf-8'))

        except Exception as e:
            self.send_response(500)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(json.dumps({'error': f"Errore scansione: {str(e)}"}).encode('utf-8'))
            
    def handle_api(self):
        try:
            config_path = os.path.join(os.getcwd(), 'config.json')
            if not os.path.exists(config_path):
                self.send_error_response("File config.json non trovato.")
                return
                
            with open(config_path, 'r', encoding='utf-8') as f:
                config = json.load(f)
                
            client_ip = self.client_address[0]
            if client_ip == '::1':
                client_ip = '127.0.0.1'
                
            subnet_prefixes = config.get('server', {}).get('subnet_prefixes', ['192.168.', '10.', '127.0.0.1', 'localhost'])
            is_local = any(client_ip.startswith(prefix) for prefix in subnet_prefixes) or client_ip == '127.0.0.1'
            
            if is_local:
                resolved_host = config.get('server', {}).get('local_host', 'localhost')
                if not resolved_host:
                    resolved_host = 'localhost'
            else:
                resolved_host = config.get('server', {}).get('remote_host', '')
                if not resolved_host:
                    host_header = self.headers.get('Host', '')
                    resolved_host = host_header.split(':')[0] if host_header else 'localhost'
                    
            disk_stats = []
            for disk in config.get('disks', []):
                name = disk.get('name', 'Disco')
                path = disk.get('path', 'C:\\')
                try:
                    total, used, free = shutil.disk_usage(path)
                    disk_stats.append({
                        'name': name,
                        'path': path,
                        'total': self.format_size(total),
                        'used': self.format_size(used),
                        'free': self.format_size(free),
                        'percentage': round((used / total) * 100, 2),
                        'ok': True
                    })
                except Exception:
                    disk_stats.append({
                        'name': name,
                        'path': path,
                        'total': 'N/D',
                        'used': 'N/D',
                        'free': 'N/D',
                        'percentage': 0,
                        'ok': False
                    })
                    
            cameras = []
            for cam in config.get('cameras', []):
                cam_copy = cam.copy()
                cam_copy['stream_url'] = cam_copy.get('stream_url', '').replace('{host}', resolved_host)
                cameras.append(cam_copy)
                
            now = datetime.datetime.now()
            days = ["Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato"]
            months = ["", "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"]
            
            wday_idx = (now.weekday() + 1) % 7
            date_str = f"{days[wday_idx]} {now.day} {months[now.month]} {now.year}"
            time_str = now.strftime("%H:%M:%S")
            
            response_data = {
                'server_time': {
                    'date': date_str,
                    'time': time_str
                },
                'connection': {
                    'client_ip': client_ip,
                    'is_local': is_local,
                    'resolved_host': resolved_host,
                    'type': 'Locale' if is_local else 'Remota'
                },
                'disks': disk_stats,
                'cameras': cameras,
                'audio_enabled_default': bool(config.get('server', {}).get('audio_enabled_default', True)),
                'raw_config': config
            }
            
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(json.dumps(response_data).encode('utf-8'))
            
        except Exception as e:
            self.send_error_response(f"Errore interno del server: {str(e)}")
            
    def send_error_response(self, message):
        self.send_response(500)
        self.send_header('Content-Type', 'application/json')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.end_headers()
        self.wfile.write(json.dumps({'error': message}).encode('utf-8'))
        
    def format_size(self, bytes_size):
        types = ['B', 'KB', 'MB', 'GB', 'TB']
        i = 0
        dbl_size = float(bytes_size)
        while dbl_size >= 1024.0 and i < len(types) - 1:
            dbl_size /= 1024.0
            i += 1
        return f"{round(dbl_size, 2)} {types[i]}"

socketserver.TCPServer.allow_reuse_address = True

if __name__ == '__main__':
    parser = argparse.ArgumentParser(description='IPCam Viewer - Server di sviluppo/emulatore API')
    parser.add_argument('--port', type=int, default=8001, help='Porta su cui avviare il server (default: 8001)')
    parser.add_argument('--host', type=str, default='0.0.0.0', help='Host su cui bindare il server (default: 0.0.0.0)')
    parser.add_argument('--no-browser', action='store_true', help='Non aprire il browser all\'avvio')
    args = parser.parse_args()

    PORT = args.port
    HOST = args.host

    print("================================================================")
    print(f" Avvio Server di sviluppo per IPCam Viewer")
    print(f" Indirizzo: http://{HOST}:{PORT}/")
    print(" Premere Ctrl+C in questo terminale per arrestare il server.")
    print("================================================================")
    
    if not args.no_browser:
        webbrowser.open(f"http://{HOST}:{PORT}/")

    try:
        with socketserver.TCPServer((HOST, PORT), DualStackServer) as httpd:
            print(f"\n Server in ascolto su http://{HOST}:{PORT}/")
            print(" Per arrestare: Ctrl+C\n")
            httpd.serve_forever()
    except OSError as e:
        print(f"\n[ERRORE] Impossibile avviare il server sulla porta {PORT}: {e}")
        print(" Usa --port per specificare una porta diversa.")
        sys.exit(1)
    except KeyboardInterrupt:
        print("\nServer arrestato correttamente.")
