# IPCams Viewer v2.0

Pannello di controllo moderno, reattivo e basato su glassmorphism per il monitoraggio e la gestione di flussi video M-JPEG/RTSP provenienti da telecamere IP locali e remote.

| Grid View | Configuration & Scanner | Image Calibration |
| :---: | :---: | :---: |
| <img src="resources/dashboard.png" width="100%" alt="Grid View"> | <img src="resources/settings1.png" width="100%" alt="Scanner Settings"> | <img src="resources/settings2.png" width="100%" alt="Filters Preview"> |

---

## 🌟 Caratteristiche Principali

*   **Interfaccia UI/UX Ultra-Moderna (Glassmorphism)**: Layout reattivo a schermo intero senza barre di scorrimento, ottimizzato per schermi touch e monitor di sorveglianza CCTV.
*   **Adattamento Griglia Intelligente**: Risolutore geometrico che posiziona e dimensiona i riquadri delle telecamere in modo ottimale a seconda della grandezza dello schermo, mantenendo la proporzione nativa 16:10 senza deformare o tagliare i flussi video.
*   **Scanner di Rete Asincrono Integrato**:
    *   *Host Singolo (Scansione Porte)*: Rileva flussi e porte aperte (es. porte di MotionEye) su un determinato IP.
    *   *Classe Sottorete (Scansione Host)*: Esegue il ping parallelo ad alte prestazioni di 254 host in meno di 1 secondo su una specifica porta.
    *   *Anteprima e Integrazione Rapida*: Testa la telecamera nello scanner prima di importarla istantaneamente con un click nel form di configurazione.
*   **Calibrazione Immagine in Tempo Reale**: Sliders per la regolazione GPU-accelerata di Luminosità, Contrasto, Bianco e Nero (Grayscale) e Nitidezza per ciascuna telecamera. Include un'**Anteprima Dedicata** interna per calibrare le immagini anche quando la dashboard principale è sfocata dal modale impostazioni.
*   **Configurazione Centralizzata**: Gestione completa di telecamere, dischi e host tramite un singolo file [config.json](config.json) modificabile direttamente dalla GUI.
*   **Emulatore Locale Zero-Dependency**: File [server.py](server.py) in Python 3.8+ che emula completamente l'API di produzione PHP ([api.php](api.php)), consentendo lo sviluppo locale istantaneo senza necessità di un webserver.
*   **Monitoraggio Risorse**: Widget per lo stato dello storage dei dischi (con avvisi visivi di saturazione) e orologio digitale sincronizzato con il server.

---

## 🛠️ Requisiti di Sistema

### In Produzione (Server Web con PHP)
*   Webserver (Apache, Nginx, IIS) con supporto a **PHP 7.0+**.
*   Cartella con permessi di scrittura per consentire il salvataggio di `config.json` tramite `api.php`.

### In Produzione (Server Web + Python — Alternativa senza PHP)
*   Webserver (Nginx, Apache) con supporto proxy reverse.
*   **Python 3.8+**.
*   Servizio systemd (o equivalente) per mantenere attivo [server.py](server.py).

### In Sviluppo / Uso Locale (Standalone)
*   **Python 3.8+** (già pre-configurato nei launcher).

---

## 🚀 Installazione e Avvio Rapido

> **Prima di tutto**: copia il file di configurazione d'esempio e personalizzalo:
> ```bash
> cp config.json.example config.json
> # oppure su Windows:
> copy config.json.example config.json
> ```

### Avvio Locale su Windows (senza webserver)
1. Copia `config.json.example` come `config.json` e personalizzalo.
2. Fai doppio clic sul file launcher **`run.bat`** per verificare Python e avviare il server emulator.
3. Il browser si aprirà automaticamente all'indirizzo `http://localhost:8001/`.

### Avvio Locale su Linux/macOS (senza webserver)
1. Copia `config.json.example` come `config.json` e personalizzalo.
2. (Opzionale) Rendi eseguibile lo script: `chmod +x run.sh`
3. Avvia con: **`./run.sh`** (il browser si aprirà automaticamente).
4. Per personalizzare: `./run.sh --port 9000 --no-browser`

### Installazione su Server Web (Apache/Nginx con PHP)
1. Copia i file di progetto nella directory root (o in una sottocartella) del tuo server web.
2. Copia `config.json.example` come `config.json` e personalizza le impostazioni.
3. Assicurati che il file `config.json` sia modificabile dal server (es. `chmod 664 config.json`).
4. Accedi tramite browser al percorso `http://<IP_SERVER>/ipcam.php`.

### Installazione su Server Web (Nginx + Python — senza PHP)

#### 1. Configurazione iniziale
```bash
# Copia il file d'esempio e personalizzalo
cp config.json.example config.json

# Permessi
chmod 664 /path/to/ipcam-viewer/config.json
chown -R utente:www-data /path/to/ipcam-viewer/
```

#### 2. Configurazione Nginx
Crea `/etc/nginx/sites-available/ipcam-viewer`:

```nginx
server {
    listen 8084;
    server_name _;

    location /ipcam {
        # Proxy tutto al server Python (emula PHP + API + file statici)
        proxy_pass http://127.0.0.1:8001/;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;

        # Timeout prolungati per flussi video M-JPEG
        proxy_read_timeout 86400s;
        proxy_buffering off;
    }
}
```

Attiva il sito e ricarica Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/ipcam-viewer /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

> **Nota**: Se la porta 8084 è già occupata, scegline una libera e sostituiscila nel file di configurazione.

#### 3. Servizio systemd
Crea `/etc/systemd/system/ipcam-viewer.service`:

```ini
[Unit]
Description=IPCam Viewer - Server API Python (emulatore PHP)
Documentation=https://github.com/BalTac/ipcam-viewer
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/path/to/ipcam-viewer
ExecStart=/usr/bin/python3 /path/to/ipcam-viewer/server.py --port 8001 --no-browser
Restart=always
RestartSec=5

# Limit file descriptor per connessioni multiple
LimitNOFILE=4096

# Security hardening
NoNewPrivileges=true
PrivateTmp=true
ProtectSystem=full

[Install]
WantedBy=multi-user.target
```

Avvia e abilita il servizio:
```bash
sudo systemctl daemon-reload
sudo systemctl enable --now ipcam-viewer
sudo systemctl status ipcam-viewer
```

#### 4. Accedi
Apri il browser all'indirizzo configurato, es: `http://<IP_SERVER>:8084/ipcam/`

---

## 🐍 Server Python (`server.py`)

`server.py` è un emulatore completo dell'API PHP, utile sia per sviluppo locale che per produzione quando PHP non è disponibile.

### Argomenti da riga di comando

| Argomento | Default | Descrizione |
|---|---|---|
| `--port` | `8001` | Porta su cui avviare il server |
| `--host` | `0.0.0.0` | Host su cui bindare il server |
| `--no-browser` | `false` | Non aprire il browser all'avvio |

### Esempi

```bash
# Porta personalizzata, nessun browser
python3 server.py --port 9000 --no-browser

# Solo ascolto locale (sicuro per sviluppo)
python3 server.py --host 127.0.0.1

# Tutti i parametri
python3 server.py --host 127.0.0.1 --port 8080 --no-browser
```

### Script di avvio

- **Windows**: `run.bat` — avvia con `--port 8001`
- **Linux/macOS**: `run.sh` — accetta argomenti, es: `./run.sh --no-browser`

---

## 🔧 Configurazione alternativa: Apache con Proxy

Se usi Apache invece di Nginx, aggiungi al tuo VirtualHost:

```apache
<VirtualHost *:80>
    ServerName ipcam.local

    ProxyPreserveHost On
    ProxyPass /ipcam/ http://127.0.0.1:8001/
    ProxyPassReverse /ipcam/ http://127.0.0.1:8001/

    ProxyPass /ipcam/api.php http://127.0.0.1:8001/api.php
    ProxyPassReverse /ipcam/api.php http://127.0.0.1:8001/api.php
</VirtualHost>
```

Abilita i moduli necessari:
```bash
sudo a2enmod proxy proxy_http
sudo systemctl restart apache2
```

---

## ⚙️ Struttura della Configurazione (`config.json`)

Le impostazioni del server, dei dischi da monitorare e delle telecamere sono memorizzate nel file `config.json` (escluso dal repository Git — usa `config.json.example` come punto di partenza). Il file supporta la risoluzione dinamica dell'IP client tramite il placeholder `{host}`:

```json
{
  "server": {
    "local_host": "192.168.1.100",
    "remote_host": "mio-host.ddns.net",
    "subnet_prefixes": ["192.168.", "10.0.", "127.0.0.1", "localhost"],
    "audio_enabled_default": true
  },
  "disks": [
    {
      "name": "Storage Registrazioni",
      "path": "/mnt/nas/recordings"
    }
  ],
  "cameras": [
    {
      "id": "camera1",
      "name": "Camera Ingresso",
      "stream_url": "http://{host}:8081",
      "filters": {
        "brightness": 100,
        "contrast": 100,
        "grayscale": 0,
        "sharp": false
      }
    }
  ]
}
```

> **Permessi**: Assicurati che `config.json` sia scrivibile dal server web / servizio Python:
> ```bash
> chmod 664 /path/to/ipcam-viewer/config.json
> ```

---

## 🔍 Verifica dello stato

```bash
# Il servizio systemd è attivo?
sudo systemctl status ipcam-viewer

# Le porte sono in ascolto?
sudo ss -tlnp | grep -E '(8001|8084)'

# Test rapido API
curl -s http://localhost:8084/ipcam/api.php | python3 -m json.tool | head -20

# Log del servizio
sudo journalctl -u ipcam-viewer -f
```

---

## 📄 Licenza e Crediti

Sviluppato originariamente nel 2016 e aggiornato completamente nel 2026.
*   **Copyright**: &copy; 2016-2026 BalTac
*   **Versione**: 2.0
*   **Repository**: [github.com/BalTac/ipcam-viewer](https://github.com/BalTac/ipcam-viewer)
*   Rilasciato sotto licenza MIT.
