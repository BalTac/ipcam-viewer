<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IPCams Viewer</title>
    <link rel="icon" type="image/png" href="images/ipcam.png" />
    
    <!-- Google Fonts: Inter per UI e Orbitron per orologio/dati scientifici -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: #030712;
            --bg-gradient: radial-gradient(circle at 50% 50%, #111827 0%, #030712 100%);
            --glass-bg: rgba(17, 24, 39, 0.6);
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-hover-border: rgba(56, 189, 248, 0.4);
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --accent-cyan: #0ea5e9;
            --accent-glow: rgba(14, 165, 233, 0.3);
            --accent-green: #10b981;
            --accent-green-glow: rgba(16, 185, 129, 0.3);
            --accent-red: #ef4444;
            --accent-red-glow: rgba(239, 68, 68, 0.3);
            --card-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-color);
            background-image: var(--bg-gradient);
            color: var(--text-primary);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Blocca lo scroll del body */
        }

        /* Contenitore principale */
        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem;
            height: 100vh;
            max-height: 100vh;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            overflow: hidden;
        }

        /* Header Glassmorphism */
        header {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 0.8rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: nowrap;
            gap: 1rem;
            box-shadow: var(--card-shadow);
            flex-shrink: 0; /* Non si riduce */
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .brand-logo {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .brand-text h1 {
            font-size: 1.25rem;
            font-weight: 700;
            background: linear-gradient(135deg, #fff 0%, #9ca3af 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.025em;
        }

        .brand-text p {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .status-indicators {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .status-badge {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            padding: 0.4rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-weight: 500;
        }

        .indicator-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .indicator-dot.active {
            background-color: var(--accent-green);
            box-shadow: 0 0 8px var(--accent-green);
            animation: pulse-dot 2s infinite;
        }

        .indicator-dot.remote {
            background-color: var(--accent-cyan);
            box-shadow: 0 0 8px var(--accent-cyan);
            animation: pulse-dot-cyan 2s infinite;
        }

        .indicator-dot.error {
            background-color: var(--accent-red);
            box-shadow: 0 0 8px var(--accent-red);
        }

        /* Orologio digitale moderno */
        .clock-widget {
            text-align: right;
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }

        .clock-time {
            font-family: 'Orbitron', monospace;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent-cyan);
            text-shadow: 0 0 10px var(--accent-glow);
            letter-spacing: 1px;
        }

        .clock-date {
            font-size: 0.7rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Griglia delle telecamere dinamica per occupare lo spazio a disposizione */
        .camera-grid {
            flex-grow: 1;
            height: 0; /* Forza il flexbox a calcolare l'altezza rimanente */
            display: grid;
            gap: 1rem;
            justify-content: center;
            align-content: center;
            overflow: hidden;
        }

        .camera-card {
            background: var(--glass-bg);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            box-shadow: var(--card-shadow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }

        .camera-card:hover {
            transform: scale(1.015);
            border-color: var(--glass-hover-border);
            box-shadow: 0 12px 40px 0 rgba(14, 165, 233, 0.15);
            z-index: 10;
        }

        .camera-header {
            height: 38px;
            padding: 0 1.25rem;
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .camera-title {
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: -0.01em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .camera-status {
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            color: var(--text-secondary);
        }

        .camera-view {
            position: relative;
            flex-grow: 1;
            height: 0; /* Molto importante per il comportamento flex */
            background: #000;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .camera-stream {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Riempie il box mantenendo le proporzioni senza allungamenti */
        }

        /* Overlay con dettagli stream */
        .camera-overlay {
            position: absolute;
            top: 8px;
            left: 8px;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-family: 'Orbitron', monospace;
            font-size: 0.65rem;
            color: var(--accent-cyan);
            pointer-events: none;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            z-index: 2;
        }

        .pulse-indicator {
            width: 5px;
            height: 5px;
            background: var(--accent-cyan);
            border-radius: 50%;
            box-shadow: 0 0 6px var(--accent-cyan);
        }

        .pulse-active {
            animation: pulse-stream 0.5s ease infinite alternate;
        }

        /* Barra delle impostazioni globali e dischi */
        .footer-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 0.8rem 1.5rem;
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 1.5rem;
            box-shadow: var(--card-shadow);
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .footer-panel {
                grid-template-columns: 1fr;
            }
        }

        /* Monitoraggio dischi */
        .disks-section {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .disk-item {
            flex-grow: 1;
            min-width: 200px;
            max-width: 350px;
        }

        .disk-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            color: var(--text-secondary);
            margin-bottom: 0.3rem;
            font-weight: 500;
        }

        .disk-name {
            color: var(--text-primary);
            font-weight: 600;
        }

        .disk-progress-container {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 9999px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .disk-progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--accent-cyan) 0%, #38bdf8 100%);
            box-shadow: 0 0 8px var(--accent-glow);
            border-radius: 9999px;
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .disk-progress-bar.warning {
            background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
            box-shadow: 0 0 8px rgba(245, 158, 11, 0.4);
        }

        .disk-progress-bar.danger {
            background: linear-gradient(90deg, var(--accent-red) 0%, #f87171 100%);
            box-shadow: 0 0 8px var(--accent-red-glow);
        }

        /* Controlli UI */
        .ui-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .control-btn {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .control-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .control-btn.active {
            background: rgba(14, 165, 233, 0.15);
            border-color: var(--accent-cyan);
            color: #38bdf8;
        }

        /* Lightbox Modale Nativo */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(3, 7, 18, 0.9);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            padding: 1.5rem;
        }

        .modal.open {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-content {
            position: relative;
            width: 100%;
            max-width: 1100px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transform: scale(0.95);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            display: flex;
            flex-direction: column;
        }

        .modal.open .modal-content {
            transform: scale(1);
        }

        .modal-header {
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.01);
            border-bottom: 1px solid var(--glass-border);
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .close-btn {
            background: rgba(255, 255, 255, 0.05);
            border: none;
            color: var(--text-primary);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.2s ease;
            user-select: none;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.1);
        }

        .modal-body {
            aspect-ratio: 16/9;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-stream {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Custom styling for range inputs */
        input[type="range"] {
            -webkit-appearance: none;
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            outline: none;
            transition: background 0.3s;
        }

        input[type="range"]:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--accent-cyan);
            box-shadow: 0 0 8px var(--accent-glow);
            cursor: pointer;
            transition: transform 0.1s, background-color 0.2s;
        }

        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.25);
            background: #38bdf8;
        }

        input[type="range"]::-moz-range-thumb {
            width: 14px;
            height: 14px;
            border: none;
            border-radius: 50%;
            background: var(--accent-cyan);
            box-shadow: 0 0 8px var(--accent-glow);
            cursor: pointer;
            transition: transform 0.1s, background-color 0.2s;
        }

        input[type="range"]::-moz-range-thumb:hover {
            transform: scale(1.25);
            background: #38bdf8;
        }

        /* Stili per il pannello Impostazioni */
        .settings-form-group {
            margin-bottom: 1rem;
        }
        .settings-form-label {
            display: block;
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 0.3rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .settings-form-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            padding: 0.6rem 0.8rem;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        .settings-form-input:focus {
            outline: none;
            border-color: var(--accent-cyan);
            background: rgba(255, 255, 255, 0.06);
            box-shadow: 0 0 8px var(--accent-glow);
        }
        .settings-section-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 2rem 0 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding-bottom: 0.5rem;
            color: var(--accent-cyan);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .disk-row, .cam-row {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            background: rgba(255, 255, 255, 0.01);
            border: 1px solid var(--glass-border);
            padding: 0.6rem;
            border-radius: 10px;
        }
        .delete-row-btn {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.2rem;
            transition: all 0.2s ease;
            user-select: none;
        }
        .delete-row-btn:hover {
            background: rgba(239, 68, 68, 0.25);
            border-color: var(--accent-red);
            transform: scale(1.05);
        }

        /* Notifica di salvataggio */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: rgba(16, 185, 129, 0.9);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #fff;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 10000;
        }
        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* Stili per Scanner Risultati */
        .scan-result-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            padding: 0.5rem 0.8rem;
            border-radius: 8px;
            font-size: 0.85rem;
        }
        .scan-result-actions {
            display: flex;
            gap: 0.4rem;
        }
        .scan-btn-small {
            padding: 0.3rem 0.6rem;
            font-size: 0.75rem;
            border-radius: 6px;
            font-weight: 600;
        }

        @keyframes pulse-dot {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        @keyframes pulse-dot-cyan {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(14, 165, 233, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 8px rgba(14, 165, 233, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(14, 165, 233, 0); }
        }

        @keyframes pulse-stream {
            0% { opacity: 0.3; }
            100% { opacity: 1; }
        }

        .cam-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(17, 24, 39, 0.9);
            color: var(--text-secondary);
            gap: 0.5rem;
            font-size: 0.85rem;
            z-index: 5;
        }

        .cam-placeholder svg {
            width: 36px;
            height: 36px;
            opacity: 0.5;
        }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- Header -->
        <header>
            <div class="brand-section">
                <img src="images/ipcam.png" alt="Logo" class="brand-logo" onerror="this.style.display='none'">
                <div class="brand-text">
                    <h1>IPCams Viewer</h1>
                    <p>Pannello di Controllo Domestico</p>
                </div>
            </div>

            <div class="status-indicators">
                <div class="status-badge" id="host-badge">
                    <span class="indicator-dot error" id="host-dot"></span>
                    <span id="host-text">Rilevamento in corso...</span>
                </div>
                
                <div class="clock-widget">
                    <span class="clock-time" id="clock-time">--:--:--</span>
                    <span class="clock-date" id="clock-date">Caricamento data...</span>
                </div>
            </div>
        </header>

        <!-- Griglia Telecamere dinamica (senza scroll e con ridimensionamento) -->
        <main class="camera-grid" id="camera-grid">
            <!-- Popolato via JS -->
        </main>

        <!-- Pannello Footer (Stato Dischi & Controlli) -->
        <div class="footer-panel">
            <div class="disks-section" id="disks-section">
                <!-- Popolato via JS -->
            </div>

            <div class="ui-controls">
                <button class="control-btn" id="audio-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" id="audio-icon">
                        <path d="M11.536 14.01A8.47 8.47 0 0 0 14.026 8a8.47 8.47 0 0 0-2.49-6.01l-.708.707A7.48 7.48 0 0 1 13.025 8c0 2.071-.84 3.946-2.197 5.303z"/>
                        <path d="M10.121 12.596A6.48 6.48 0 0 0 12.025 8a6.48 6.48 0 0 0-1.904-4.596l-.707.707A5.48 5.48 0 0 1 11.025 8a5.48 5.48 0 0 1-1.61 3.89z"/>
                        <path d="M8.707 11.182A4.5 4.5 0 0 0 10.025 8a4.5 4.5 0 0 0-1.318-3.182L8 5.525A3.5 3.5 0 0 1 9.025 8 3.5 3.5 0 0 1 8 10.475z"/>
                        <path d="M6.717 3.55A.5.5 0 0 1 7 4v8a.5.5 0 0 1-.812.39L3.825 10.5H1.5A.5.5 0 0 1 1 10V6a.5.5 0 0 1 .5-.5h2.325l2.363-1.89a.5.5 0 0 1 .529-.06"/>
                    </svg>
                    <span id="audio-text">Audio Abilitato</span>
                </button>

                <button class="control-btn" id="settings-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.86z"/>
                    </svg>
                    <span>Impostazioni</span>
                </button>
            </div>
        </div>

    </div>

    <!-- Modale Zoom Telecamera -->
    <div class="modal" id="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title" id="modal-title">Dettaglio Telecamera</span>
                <button class="close-btn" id="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <img src="" alt="Stream Ingrandito" class="modal-stream" id="modal-stream">
            </div>
        </div>
    </div>

    <!-- Modale Impostazioni -->
    <div class="modal" id="settings-modal">
        <div class="modal-content" style="max-width: 750px;">
            <div class="modal-header">
                <span class="modal-title">Pannello di Configurazione</span>
                <button class="close-btn" id="settings-close">&times;</button>
            </div>
            <div class="modal-body" style="aspect-ratio: auto; max-height: 70vh; overflow-y: auto; padding: 1.5rem; display: block; text-align: left;">
                
                <!-- Scanner di Rete -->
                <div class="settings-section-title" style="margin-top: 0;">Scanner di Rete</div>
                <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--glass-border); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                        <div class="settings-form-group" style="margin-bottom: 0;">
                            <label class="settings-form-label">Modalità Scansione</label>
                            <select class="settings-form-input" id="scan-mode" style="cursor: pointer;">
                                <option value="host">Host Singolo (Scansione Porte)</option>
                                <option value="subnet">Classe Sottorete (Scansione Port Singola)</option>
                            </select>
                        </div>
                        <div class="settings-form-group" style="margin-bottom: 0;">
                            <label class="settings-form-label" id="scan-target-label">IP Target</label>
                            <input type="text" class="settings-form-input" id="scan-target-ip" placeholder="es. 192.168.50.180">
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; align-items: flex-end; margin-bottom: 1rem; flex-wrap: wrap;">
                        <div class="settings-form-group" style="margin-bottom: 0;">
                            <label class="settings-form-label" id="scan-param-label">Porte da Testare (separate da virgola)</label>
                            <input type="text" class="settings-form-input" id="scan-param" value="80,8080,8081,8082,8083,8084,8085,8086,8087,8088">
                        </div>
                        <button type="button" class="control-btn active" id="run-scan-btn" style="height: 38px; width: 100%; display: flex; justify-content: center; align-items: center; background: rgba(14, 165, 233, 0.25);">
                            Avvia Scansione
                        </button>
                    </div>

                    <!-- Anteprima dello scanner integrata -->
                    <div id="scan-preview-wrapper" style="display: none; margin-bottom: 1rem; background: rgba(0, 0, 0, 0.4); border: 1px solid var(--glass-border); padding: 0.75rem; border-radius: 10px; text-align: center;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; user-select: none;">
                            <span style="font-size: 0.75rem; font-weight: 600; color: var(--accent-cyan);">Anteprima Flusso Rilevato</span>
                            <span id="close-scan-preview" style="cursor: pointer; color: var(--text-secondary); font-size: 1.1rem; font-weight: bold;">&times;</span>
                        </div>
                        <div class="camera-view" style="height: 200px; max-height: 220px; aspect-ratio: 16/10; margin: 0 auto; border-radius: 8px; flex-grow: 0;">
                            <img id="scan-preview-img" src="" alt="Anteprima Stream" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                    </div>

                    <!-- Elenco risultati -->
                    <div id="scan-results-container" style="display: none;">
                        <span class="settings-form-label" style="margin-bottom: 0.5rem;">Risultati Trovati:</span>
                        <div id="scan-results-list" style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 180px; overflow-y: auto; padding-right: 0.25rem;">
                            <!-- Popolato da JS -->
                        </div>
                    </div>
                </div>

                <!-- Server -->
                <div class="settings-section-title">Impostazioni Server</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div class="settings-form-group">
                        <label class="settings-form-label">Host Locale (IP o Dominio)</label>
                        <input type="text" class="settings-form-input" id="cfg-local-host" placeholder="es. 192.168.1.100 o localhost">
                    </div>
                    <div class="settings-form-group">
                        <label class="settings-form-label">Host Remoto (DDNS o IP Esterno)</label>
                        <input type="text" class="settings-form-input" id="cfg-remote-host" placeholder="es. mia-casa.duckdns.org (opzionale)">
                    </div>
                </div>
                <div class="settings-form-group">
                    <label class="settings-form-label">Prefissi Subnet per Client Locale (separati da virgola)</label>
                    <input type="text" class="settings-form-input" id="cfg-subnet-prefixes" placeholder="es. 192.168., 10., 127.0.0.1, localhost">
                </div>
                <div class="settings-form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; user-select: none;">
                    <input type="checkbox" id="cfg-audio-enabled" style="cursor: pointer; width: 18px; height: 18px;">
                    <label for="cfg-audio-enabled" style="font-size: 0.85rem; cursor: pointer; font-weight: 500;">Abilita effetti audio all'avvio</label>
                </div>

                <!-- Dischi -->
                <div class="settings-section-title">Dischi da Monitorare</div>
                <div id="cfg-disks-container" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <!-- Elenco dinamico -->
                </div>
                <button type="button" class="control-btn" id="cfg-add-disk" style="margin-top: 0.75rem; font-size: 0.75rem; padding: 0.45rem 1rem;">
                    + Aggiungi Disco
                </button>

                <!-- Telecamere -->
                <div class="settings-section-title">Telecamere</div>
                <div id="cfg-cameras-container" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <!-- Elenco dinamico -->
                </div>
                <button type="button" class="control-btn" id="cfg-add-camera" style="margin-top: 0.75rem; font-size: 0.75rem; padding: 0.45rem 1rem;">
                    + Aggiungi Telecamera
                </button>

            </div>
            <div class="modal-header" style="border-top: 1px solid var(--glass-border); border-bottom: none; justify-content: flex-end; gap: 1rem; padding: 1rem 1.5rem;">
                <button class="control-btn" id="settings-cancel" style="background: rgba(239, 68, 68, 0.05); border-color: rgba(239, 68, 68, 0.2); color: #f87171;">Annulla</button>
                <button class="control-btn active" id="settings-save" style="background: rgba(16, 185, 129, 0.15); border-color: var(--accent-green); color: #34d399;">Salva Configurazione</button>
            </div>
        </div>
    </div>

    <!-- Notifica Toast -->
    <div class="toast" id="toast">Configurazione salvata con successo!</div>

    <!-- Elementi Audio -->
    <audio id="audio-hover" preload="auto">
        <source src="audio/button-46.wav">
    </audio>
    <audio id="audio-click" preload="auto">
        <source src="audio/click.mp3">
    </audio>

    <script type="text/javascript">
        // Stato dell'applicazione
        const state = {
            audioEnabled: true,
            cameras: [],
            disks: [],
            serverTimeDiff: 0,
            fpsCounters: {},
            rawConfig: null,
            detectedHostIp: '127.0.0.1'
        };

        // Elementi DOM
        const el = {
            cameraGrid: document.getElementById('camera-grid'),
            disksSection: document.getElementById('disks-section'),
            clockTime: document.getElementById('clock-time'),
            clockDate: document.getElementById('clock-date'),
            hostBadge: document.getElementById('host-badge'),
            hostDot: document.getElementById('host-dot'),
            hostText: document.getElementById('host-text'),
            audioToggle: document.getElementById('audio-toggle'),
            audioIcon: document.getElementById('audio-icon'),
            audioText: document.getElementById('audio-text'),
            audioHover: document.getElementById('audio-hover'),
            audioClick: document.getElementById('audio-click'),
            modal: document.getElementById('modal'),
            modalTitle: document.getElementById('modal-title'),
            modalStream: document.getElementById('modal-stream'),
            modalClose: document.getElementById('modal-close'),
            
            // Impostazioni
            settingsToggle: document.getElementById('settings-toggle'),
            settingsModal: document.getElementById('settings-modal'),
            settingsClose: document.getElementById('settings-close'),
            settingsCancel: document.getElementById('settings-cancel'),
            settingsSave: document.getElementById('settings-save'),
            cfgLocalHost: document.getElementById('cfg-local-host'),
            cfgRemoteHost: document.getElementById('cfg-remote-host'),
            cfgSubnetPrefixes: document.getElementById('cfg-subnet-prefixes'),
            cfgAudioEnabled: document.getElementById('cfg-audio-enabled'),
            cfgDisksContainer: document.getElementById('cfg-disks-container'),
            cfgCamerasContainer: document.getElementById('cfg-cameras-container'),
            cfgAddDisk: document.getElementById('cfg-add-disk'),
            cfgAddCamera: document.getElementById('cfg-add-camera'),
            toast: document.getElementById('toast'),

            // Scanner
            scanMode: document.getElementById('scan-mode'),
            scanTargetLabel: document.getElementById('scan-target-label'),
            scanTargetIp: document.getElementById('scan-target-ip'),
            scanParamLabel: document.getElementById('scan-param-label'),
            scanParam: document.getElementById('scan-param'),
            runScanBtn: document.getElementById('run-scan-btn'),
            scanPreviewWrapper: document.getElementById('scan-preview-wrapper'),
            scanPreviewImg: document.getElementById('scan-preview-img'),
            closeScanPreview: document.getElementById('close-scan-preview'),
            scanResultsContainer: document.getElementById('scan-results-container'),
            scanResultsList: document.getElementById('scan-results-list')
        };

        // Rileva impostazioni audio da localStorage
        const storedAudio = localStorage.getItem('ipcam_audio_enabled');
        if (storedAudio !== null) {
            state.audioEnabled = storedAudio === 'true';
        }
        updateAudioButtonUI();

        // Inizializzazione
        document.addEventListener('DOMContentLoaded', () => {
            fetchData();
            
            setInterval(fetchData, 15000);
            setInterval(updateClock, 1000);

            // Ricalcola il layout quando cambia la dimensione della finestra
            window.addEventListener('resize', updateGridLayout);

            let pageFocus = true;
            window.onblur = () => pageFocus = false;
            window.onfocus = () => {
                if (!pageFocus) {
                    location.reload(true);
                }
                pageFocus = true;
            };

            el.audioToggle.addEventListener('click', () => {
                state.audioEnabled = !state.audioEnabled;
                localStorage.setItem('ipcam_audio_enabled', state.audioEnabled);
                updateAudioButtonUI();
                playAudio('click');
            });

            el.modalClose.addEventListener('click', closeModal);
            el.modal.addEventListener('click', (e) => {
                if (e.target === el.modal) closeModal();
            });

            // Impostazioni Modale
            el.settingsToggle.addEventListener('click', () => {
                playAudio('click');
                openSettingsModal();
            });
            el.settingsClose.addEventListener('click', closeSettingsModal);
            el.settingsCancel.addEventListener('click', closeSettingsModal);
            el.settingsModal.addEventListener('click', (e) => {
                if (e.target === el.settingsModal) closeSettingsModal();
            });

            el.cfgAddDisk.addEventListener('click', () => {
                addDiskRowUI('', '');
            });
            el.cfgAddCamera.addEventListener('click', () => {
                addCameraRowUI('', '', 'http://{host}:');
            });

            el.settingsSave.addEventListener('click', saveSettings);

            // Scanner
            el.scanMode.addEventListener('change', () => {
                const mode = el.scanMode.value;
                if (mode === 'host') {
                    el.scanTargetLabel.textContent = 'IP Target (es. 192.168.50.180)';
                    el.scanTargetIp.placeholder = 'es. 192.168.50.180';
                    el.scanTargetIp.value = state.detectedHostIp;
                    el.scanParamLabel.textContent = 'Porte da Testare (separate da virgola)';
                    el.scanParam.value = '80,8080,8081,8082,8083,8084,8085,8086,8087,8088';
                } else {
                    el.scanTargetLabel.textContent = 'Classe Subnet / IP di Riferimento';
                    el.scanTargetIp.placeholder = 'es. 192.168.50.180 o 192.168.50.';
                    el.scanTargetIp.value = state.detectedHostIp;
                    el.scanParamLabel.textContent = 'Porta da Testare (singola)';
                    el.scanParam.value = '8081';
                }
            });

            el.runScanBtn.addEventListener('click', runNetworkScan);
            el.closeScanPreview.addEventListener('click', () => {
                el.scanPreviewWrapper.style.display = 'none';
                el.scanPreviewImg.src = '';
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeModal();
                    closeSettingsModal();
                }
            });
        });

        // Calcolo dinamico del layout della griglia (CCTV monitor grid style)
        function updateGridLayout() {
            const grid = el.cameraGrid;
            const cards = grid.querySelectorAll('.camera-card');
            const N = cards.length;
            if (N === 0) return;

            // Prende l'area disponibile del contenitore
            const W = grid.clientWidth;
            const H = grid.clientHeight;
            const aspect = 1.6; // Rapporto di aspetto fisso del flusso video (16:10)
            const headerH = 38; // Altezza occupata dall'header testuale della card

            let bestCols = 1;
            let bestRows = 1;
            let bestWidth = 0;
            let bestHeight = 0;
            let maxArea = 0;

            // Itera sulle colonne per trovare il layout ottimale che massimizza le dimensioni delle card
            for (let c = 1; c <= N; c++) {
                const r = Math.ceil(N / c);
                
                // Tiene conto del gap di 1rem (16px) tra le celle della griglia
                const gapX = 16 * (c - 1);
                const gapY = 16 * (r - 1);
                const maxCellW = (W - gapX) / c;
                const maxCellH = (H - gapY) / r;

                if (maxCellW <= 0 || maxCellH <= 0) continue;

                let cardW, cardH;
                
                // h_card = (w_card / aspect) + headerH
                const h_card_calc = (maxCellW / aspect) + headerH;
                
                if (h_card_calc <= maxCellH) {
                    // La card si adatta alla cella limitata in larghezza
                    cardW = maxCellW;
                    cardH = h_card_calc;
                } else {
                    // La card si adatta alla cella limitata in altezza
                    const videoH = maxCellH - headerH;
                    if (videoH > 0) {
                        cardH = maxCellH;
                        cardW = videoH * aspect;
                    } else {
                        continue;
                    }
                }

                const area = cardW * cardH;
                if (area > maxArea) {
                    maxArea = area;
                    bestCols = c;
                    bestRows = r;
                    bestWidth = cardW;
                    bestHeight = cardH;
                }
            }

            // Imposta dinamicamente le colonne e le righe nel CSS Grid
            grid.style.gridTemplateColumns = `repeat(${bestCols}, auto)`;
            grid.style.gridTemplateRows = `repeat(${bestRows}, auto)`;

            // Forza le dimensioni esatte calcolate su ogni card per centrarle senza ritagli
            cards.forEach(card => {
                card.style.width = `${Math.floor(bestWidth)}px`;
                card.style.height = `${Math.floor(bestHeight)}px`;
                card.style.margin = '0 auto';
            });
        }

        // Recupera dati dal backend
        function fetchData() {
            fetch('api.php')
                .then(res => {
                    if (!res.ok) throw new Error('Network error');
                    return res.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error('Errore API:', data.error);
                        showErrorState(data.error);
                        return;
                    }
                    processServerData(data);
                })
                .catch(err => {
                    console.error('Errore nel recupero dati:', err);
                    showErrorState('Impossibile connettersi al server.');
                });
        }

        function showErrorState(msg) {
            el.hostDot.className = 'indicator-dot error';
            el.hostText.textContent = msg;
        }

        function processServerData(data) {
            state.rawConfig = data.raw_config;
            state.detectedHostIp = data.connection.resolved_host === 'localhost' ? '127.0.0.1' : data.connection.resolved_host;

            el.hostDot.className = 'indicator-dot ' + (data.connection.is_local ? 'active' : 'remote');
            el.hostText.textContent = `IP Client: ${data.connection.client_ip} (${data.connection.type})`;
            el.hostBadge.title = `Host risolto: ${data.connection.resolved_host}`;

            const serverDateObj = new Date();
            const timeParts = data.server_time.time.split(':');
            serverDateObj.setHours(parseInt(timeParts[0], 10));
            serverDateObj.setMinutes(parseInt(timeParts[1], 10));
            serverDateObj.setSeconds(parseInt(timeParts[2], 10));
            state.serverTimeDiff = serverDateObj.getTime() - Date.now();
            
            el.clockDate.textContent = data.server_time.date;
            updateClock();

            if (JSON.stringify(state.cameras) !== JSON.stringify(data.cameras)) {
                state.cameras = data.cameras;
                renderCameras();
            }

            state.disks = data.disks;
            renderDisks();
        }

        function updateClock() {
            const serverTime = new Date(Date.now() + state.serverTimeDiff);
            const h = String(serverTime.getHours()).padStart(2, '0');
            const m = String(serverTime.getMinutes()).padStart(2, '0');
            const s = String(serverTime.getSeconds()).padStart(2, '0');
            el.clockTime.textContent = `${h}:${m}:${s}`;
        }

        function renderCameras() {
            el.cameraGrid.innerHTML = '';
            
            if (state.cameras.length === 0) {
                el.cameraGrid.innerHTML = '<div class="status-badge" style="grid-column: 1/-1; justify-content: center; padding: 2rem; border-style: dashed; background: transparent;">Nessuna telecamera configurata. Premi su Impostazioni per aggiungerne una.</div>';
                return;
            }

            state.cameras.forEach(cam => {
                const card = document.createElement('div');
                card.className = 'camera-card';
                card.dataset.id = cam.id;
                card.dataset.name = cam.name;
                card.dataset.streamUrl = cam.stream_url;

                const filters = cam.filters || { brightness: 100, contrast: 100, grayscale: 0, sharp: false };
                const filterStr = `brightness(${filters.brightness || 100}%) contrast(${filters.contrast || 100}%) grayscale(${filters.grayscale || 0}%)`;
                const sharpStyle = filters.sharp ? 'image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges; image-rendering: pixelated;' : '';

                card.innerHTML = `
                    <div class="camera-header">
                        <span class="camera-title">${cam.name}</span>
                        <span class="camera-status">
                            <span class="indicator-dot active" id="dot-${cam.id}"></span>
                            Live
                        </span>
                    </div>
                    <div class="camera-view">
                        <div class="camera-overlay">
                            <span class="pulse-indicator pulse-active" id="pulse-${cam.id}"></span>
                            <span id="fps-${cam.id}">Calcolo...</span>
                        </div>
                        <img class="camera-stream" id="img-${cam.id}" src="${cam.stream_url}" style="filter: ${filterStr}; ${sharpStyle}" alt="Stream ${cam.name}">
                    </div>
                `;

                card.addEventListener('mouseenter', () => {
                    playAudio('hover');
                });

                card.addEventListener('click', () => {
                    playAudio('click');
                    openModal(cam.name, cam.stream_url, cam.filters);
                });

                el.cameraGrid.appendChild(card);
                setupFPSCounter(cam.id);
            });

            // Calcola il layout corretto per le card appena renderizzate
            setTimeout(updateGridLayout, 50);
        }

        function setupFPSCounter(camId) {
            const img = document.getElementById(`img-${camId}`);
            if (!img) return;
            const fpsText = document.getElementById(`fps-${camId}`);
            const dot = document.getElementById(`dot-${camId}`);
            const pulse = document.getElementById(`pulse-${camId}`);
            
            let lastTime = performance.now();
            let frameTimes = [];
            let loadedOnce = false;

            // Timeout iniziale di 8 secondi: se l'immagine non si carica affatto, la consideriamo offline
            let initialTimeout = setTimeout(() => {
                if (!loadedOnce) {
                    setOffline();
                }
            }, 8000);

            function setOffline() {
                if (fpsText) fpsText.textContent = 'OFFLINE';
                if (dot) dot.className = 'indicator-dot error';
                if (pulse) pulse.className = 'pulse-indicator';
                
                if (img.parentNode && !img.parentNode.querySelector('.cam-placeholder')) {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'cam-placeholder';
                    placeholder.innerHTML = `
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <span>Segnale Assente</span>
                    `;
                    img.parentNode.appendChild(placeholder);
                }
            }

            function setOnline() {
                loadedOnce = true;
                if (initialTimeout) clearTimeout(initialTimeout);
                
                const placeholder = img.parentNode.querySelector('.cam-placeholder');
                if (placeholder) placeholder.remove();

                if (dot) dot.className = 'indicator-dot active';
                if (pulse) pulse.className = 'pulse-indicator pulse-active';
            }

            img.addEventListener('load', () => {
                const now = performance.now();
                const delta = now - lastTime;
                lastTime = now;

                setOnline();

                // Calcolo FPS (funziona solo se il browser attiva l'evento load a ogni frame dell'MJPEG)
                if (delta > 0) {
                    frameTimes.push(1000 / delta);
                    if (frameTimes.length > 15) {
                        frameTimes.shift();
                    }
                    const avgFps = frameTimes.reduce((a, b) => a + b, 0) / frameTimes.length;
                    if (fpsText) {
                        fpsText.textContent = `FPS: ${avgFps.toFixed(1)}`;
                        fpsText.style.display = 'inline-block';
                    }
                }
            });

            // Se dopo 3 secondi dal primo load non abbiamo ricevuto altri eventi load, 
            // significa che l'MJPEG non scatena l'evento ripetutamente in questo browser.
            // Sostituiamo il testo "Calcolo..." con "LIVE" per evitare che rimanga bloccato.
            img.addEventListener('load', function tempListener() {
                setTimeout(() => {
                    if (frameTimes.length <= 1 && loadedOnce) {
                        if (fpsText) fpsText.textContent = 'LIVE';
                    }
                }, 3000);
                img.removeEventListener('load', tempListener);
            });

            img.addEventListener('error', () => {
                if (initialTimeout) clearTimeout(initialTimeout);
                setOffline();
            });
        }

        function renderDisks() {
            el.disksSection.innerHTML = '';
            
            if (state.disks.length === 0) {
                el.disksSection.innerHTML = '<span class="clock-date" style="border-style: dashed; padding: 0.5rem 1rem; border-radius: 8px; background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border);">Nessun disco monitorato.</span>';
                return;
            }

            state.disks.forEach(disk => {
                const diskDiv = document.createElement('div');
                diskDiv.className = 'disk-item';

                let progressClass = '';
                if (disk.percentage > 90) {
                    progressClass = 'danger';
                } else if (disk.percentage > 75) {
                    progressClass = 'warning';
                }

                diskDiv.innerHTML = `
                    <div class="disk-info">
                        <span class="disk-name">${disk.name}</span>
                        <span>Usato: ${disk.used} / ${disk.total} (${disk.percentage}%)</span>
                    </div>
                    <div class="disk-progress-container">
                        <div class="disk-progress-bar ${progressClass}" style="width: ${disk.percentage}%"></div>
                    </div>
                `;
                el.disksSection.appendChild(diskDiv);
            });
        }

        function openModal(name, url, filters) {
            el.modalTitle.textContent = name;
            el.modalStream.src = url;
            
            const f = filters || { brightness: 100, contrast: 100, grayscale: 0, sharp: false };
            const filterStr = `brightness(${f.brightness || 100}%) contrast(${f.contrast || 100}%) grayscale(${f.grayscale || 0}%)`;
            el.modalStream.style.filter = filterStr;
            if (f.sharp) {
                el.modalStream.style.imageRendering = '-webkit-optimize-contrast';
            } else {
                el.modalStream.style.imageRendering = 'auto';
            }
            
            el.modal.classList.add('open');
        }

        function closeModal() {
            el.modal.classList.remove('open');
            el.modalStream.src = '';
            el.modalStream.style.filter = 'none';
            el.modalStream.style.imageRendering = 'auto';
        }

        // Impostazioni Modale
        function openSettingsModal() {
            if (!state.rawConfig) {
                alert('Errore: impossibile caricare i dati della configurazione dal server.');
                return;
            }

            el.scanTargetIp.value = state.detectedHostIp;

            el.cfgLocalHost.value = state.rawConfig.server.local_host || '';
            el.cfgRemoteHost.value = state.rawConfig.server.remote_host || '';
            el.cfgSubnetPrefixes.value = (state.rawConfig.server.subnet_prefixes || []).join(', ');
            el.cfgAudioEnabled.checked = state.rawConfig.server.audio_enabled_default !== false;

            el.cfgDisksContainer.innerHTML = '';
            (state.rawConfig.disks || []).forEach(disk => {
                addDiskRowUI(disk.name, disk.path);
            });

            el.cfgCamerasContainer.innerHTML = '';
            (state.rawConfig.cameras || []).forEach(cam => {
                addCameraRowUI(cam.id, cam.name, cam.stream_url, cam.filters);
            });

            el.settingsModal.classList.add('open');
        }

        function closeSettingsModal() {
            el.settingsModal.classList.remove('open');
            el.scanPreviewWrapper.style.display = 'none';
            el.scanPreviewImg.src = '';
            el.scanResultsContainer.style.display = 'none';
            el.scanResultsList.innerHTML = '';
            
            // Ferma tutte le anteprime dei filtri attive
            document.querySelectorAll('.filter-preview-img').forEach(img => {
                img.src = '';
            });
            document.querySelectorAll('.filter-preview-wrapper').forEach(w => {
                w.style.display = 'none';
            });
            document.querySelectorAll('.toggle-preview-btn').forEach(btn => {
                btn.textContent = 'Mostra Anteprima';
                btn.classList.remove('active');
            });
            
            // Forza il ricalcolo del layout quando chiude il modale in caso di variazioni
            setTimeout(updateGridLayout, 100);
        }

        function addDiskRowUI(name, path) {
            const row = document.createElement('div');
            row.className = 'disk-row';
            row.innerHTML = `
                <input type="text" class="settings-form-input disk-name-input" value="${name}" placeholder="Nome del disco" style="flex-grow: 1;">
                <input type="text" class="settings-form-input disk-path-input" value="${path}" placeholder="Percorso" style="flex-grow: 2;">
                <div class="delete-row-btn">&times;</div>
            `;
            
            row.querySelector('.delete-row-btn').addEventListener('click', () => {
                playAudio('click');
                row.remove();
            });
            el.cfgDisksContainer.appendChild(row);
        }

        function addCameraRowUI(id, name, url, filters) {
            const row = document.createElement('div');
            row.className = 'cam-row';
            row.style.flexDirection = 'column';
            row.style.alignItems = 'stretch';
            row.style.gap = '0.5rem';

            const f = filters || { brightness: 100, contrast: 100, grayscale: 0, sharp: false };
            const brightness = f.brightness !== undefined ? f.brightness : 100;
            const contrast = f.contrast !== undefined ? f.contrast : 100;
            const grayscale = f.grayscale !== undefined ? f.grayscale : 0;
            const sharp = !!f.sharp;

            row.innerHTML = `
                <div style="display: flex; gap: 0.5rem; align-items: center; width: 100%;">
                    <input type="text" class="settings-form-input cam-id-input" value="${id}" placeholder="ID (es. cam1)" style="width: 85px;">
                    <input type="text" class="settings-form-input cam-name-input" value="${name}" placeholder="Nome" style="width: 150px;">
                    <input type="text" class="settings-form-input cam-url-input" value="${url}" placeholder="URL Stream" style="flex-grow: 1;">
                    <button type="button" class="control-btn filter-toggle-btn" style="padding: 0.5rem 0.8rem; font-size: 0.75rem;">Filtri</button>
                    <div class="delete-row-btn">&times;</div>
                </div>
                <div class="cam-filters-panel" style="display: none; background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 8px; margin-top: 0.25rem;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 1rem; align-items: center; margin-bottom: 0.8rem;">
                        <div>
                            <label class="settings-form-label" style="font-size: 0.7rem; margin-bottom: 0.2rem;">Luminosità: <span class="val-brightness">${brightness}%</span></label>
                            <input type="range" class="filter-brightness" min="50" max="300" value="${brightness}" style="width: 100%; cursor: pointer;">
                        </div>
                        <div>
                            <label class="settings-form-label" style="font-size: 0.7rem; margin-bottom: 0.2rem;">Contrasto: <span class="val-contrast">${contrast}%</span></label>
                            <input type="range" class="filter-contrast" min="50" max="300" value="${contrast}" style="width: 100%; cursor: pointer;">
                        </div>
                        <div>
                            <label class="settings-form-label" style="font-size: 0.7rem; margin-bottom: 0.2rem;">B&amp;W (Grigio): <span class="val-grayscale">${grayscale}%</span></label>
                            <input type="range" class="filter-grayscale" min="0" max="100" value="${grayscale}" style="width: 100%; cursor: pointer;">
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.4rem; user-select: none;">
                            <input type="checkbox" class="filter-sharp" id="sharp-${id || Math.random()}" ${sharp ? 'checked' : ''} style="width: 16px; height: 16px; cursor: pointer;">
                            <label for="sharp-${id || Math.random()}" style="font-size: 0.75rem; font-weight: 600; cursor: pointer; color: var(--text-secondary);">Nitidezza</label>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 0.5rem;">
                        <span style="font-size: 0.7rem; color: var(--text-secondary);">Usa l'anteprima per regolare i parametri in tempo reale</span>
                        <button type="button" class="control-btn toggle-preview-btn" style="padding: 0.35rem 0.75rem; font-size: 0.7rem;">Mostra Anteprima</button>
                    </div>
                    
                    <div class="filter-preview-wrapper" style="display: none; margin-top: 0.75rem; background: #000; border: 1px solid var(--glass-border); border-radius: 8px; overflow: hidden; max-width: 280px; aspect-ratio: 16/10; margin: 0.75rem auto 0 auto; height: 175px;">
                        <img class="filter-preview-img" src="" alt="Anteprima Filtri" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                </div>
            `;

            // Toggle dei filtri
            const toggleBtn = row.querySelector('.filter-toggle-btn');
            const panel = row.querySelector('.cam-filters-panel');
            toggleBtn.addEventListener('click', () => {
                playAudio('click');
                const isHidden = panel.style.display === 'none';
                panel.style.display = isHidden ? 'block' : 'none';
                toggleBtn.classList.toggle('active', isHidden);
            });

            // Toggle dell'anteprima filtri interna
            const prevToggleBtn = row.querySelector('.toggle-preview-btn');
            const prevWrapper = row.querySelector('.filter-preview-wrapper');
            const prevImg = row.querySelector('.filter-preview-img');

            prevToggleBtn.addEventListener('click', () => {
                playAudio('click');
                const isHidden = prevWrapper.style.display === 'none';
                if (isHidden) {
                    let rawUrl = row.querySelector('.cam-url-input').value.trim();
                    let resolvedUrl = rawUrl.replace('{host}', state.detectedHostIp);
                    
                    prevImg.src = resolvedUrl;
                    prevWrapper.style.display = 'block';
                    prevToggleBtn.textContent = 'Nascondi Anteprima';
                    prevToggleBtn.classList.add('active');
                    
                    applyRealtime();
                } else {
                    prevImg.src = '';
                    prevWrapper.style.display = 'none';
                    prevToggleBtn.textContent = 'Mostra Anteprima';
                    prevToggleBtn.classList.remove('active');
                }
            });

            // Funzione per applicare i filtri in tempo reale
            const applyRealtime = () => {
                const bVal = row.querySelector('.filter-brightness').value;
                const cVal = row.querySelector('.filter-contrast').value;
                const gVal = row.querySelector('.filter-grayscale').value;
                const sVal = row.querySelector('.filter-sharp').checked;
                
                row.querySelector('.val-brightness').textContent = bVal + '%';
                row.querySelector('.val-contrast').textContent = cVal + '%';
                row.querySelector('.val-grayscale').textContent = gVal + '%';

                const camIdVal = row.querySelector('.cam-id-input').value.trim();
                if (camIdVal) {
                    const img = document.getElementById(`img-${camIdVal}`);
                    if (img) {
                        img.style.filter = `brightness(${bVal}%) contrast(${cVal}%) grayscale(${gVal}%)`;
                        if (sVal) {
                            img.style.imageRendering = 'pixelated';
                        } else {
                            img.style.imageRendering = 'auto';
                        }
                    }
                }

                // Applica anche all'anteprima interna
                if (prevWrapper.style.display !== 'none') {
                    prevImg.style.filter = `brightness(${bVal}%) contrast(${cVal}%) grayscale(${gVal}%)`;
                    if (sVal) {
                        prevImg.style.imageRendering = 'pixelated';
                    } else {
                        prevImg.style.imageRendering = 'auto';
                    }
                }
            };

            row.querySelector('.filter-brightness').addEventListener('input', applyRealtime);
            row.querySelector('.filter-contrast').addEventListener('input', applyRealtime);
            row.querySelector('.filter-grayscale').addEventListener('input', applyRealtime);
            row.querySelector('.filter-sharp').addEventListener('change', applyRealtime);

            row.querySelector('.delete-row-btn').addEventListener('click', () => {
                playAudio('click');
                row.remove();
            });
            el.cfgCamerasContainer.appendChild(row);
        }

        function runNetworkScan() {
            playAudio('click');
            const mode = el.scanMode.value;
            const targetIp = el.scanTargetIp.value.trim();
            const param = el.scanParam.value.trim();

            if (!targetIp) {
                alert('Inserisci un indirizzo IP o subnet di target per la scansione.');
                return;
            }

            el.runScanBtn.disabled = true;
            el.runScanBtn.textContent = 'Scansione...';
            el.scanResultsContainer.style.display = 'none';
            el.scanResultsList.innerHTML = '';
            el.scanPreviewWrapper.style.display = 'none';
            el.scanPreviewImg.src = '';

            let url = `api.php?action=scan&mode=${mode}&ip=${targetIp}`;
            if (mode === 'host') {
                url += `&ports=${param}`;
            } else {
                url += `&port=${param}`;
            }

            fetch(url)
                .then(res => {
                    if (!res.ok) throw new Error('Errore durante la scansione');
                    return res.json();
                })
                .then(data => {
                    el.scanResultsContainer.style.display = 'block';
                    if (data.error) {
                        el.scanResultsList.innerHTML = `<span class="clock-date" style="color: var(--accent-red);">${data.error}</span>`;
                        return;
                    }
                    displayScanResults(data, targetIp);
                })
                .catch(err => {
                    console.error(err);
                    el.scanResultsContainer.style.display = 'block';
                    el.scanResultsList.innerHTML = `<span class="clock-date" style="color: var(--accent-red);">Connessione fallita.</span>`;
                })
                .finally(() => {
                    el.runScanBtn.disabled = false;
                    el.runScanBtn.textContent = 'Avvia Scansione';
                });
        }

        function displayScanResults(data, targetIp) {
            el.scanResultsList.innerHTML = '';

            if (data.mode === 'host') {
                const openPorts = data.open_ports || [];
                if (openPorts.length === 0) {
                    el.scanResultsList.innerHTML = '<span class="clock-date">Nessuna porta aperta rilevata.</span>';
                    return;
                }

                openPorts.forEach(port => {
                    const row = document.createElement('div');
                    row.className = 'scan-result-row';
                    
                    const localHostVal = el.cfgLocalHost.value.trim();
                    const isLocalTarget = (targetIp === '127.0.0.1' || targetIp === 'localhost' || targetIp === localHostVal || targetIp === state.detectedHostIp);
                    const streamHost = isLocalTarget ? '{host}' : targetIp;
                    const absoluteStreamUrl = `http://${targetIp}:${port}`;
                    const configStreamUrl = `http://${streamHost}:${port}`;

                    row.innerHTML = `
                        <span>Porta <strong>${port}</strong> Aperta (${targetIp}:${port})</span>
                        <div class="scan-result-actions">
                            <button type="button" class="control-btn scan-btn-small" id="prev-${port}">Anteprima</button>
                            <button type="button" class="control-btn active scan-btn-small" id="add-${port}">Aggiungi</button>
                        </div>
                    `;

                    row.querySelector(`#prev-${port}`).addEventListener('click', () => {
                        playAudio('click');
                        el.scanPreviewImg.src = `http://${targetIp}:${port}`;
                        el.scanPreviewWrapper.style.display = 'block';
                    });

                    row.querySelector(`#add-${port}`).addEventListener('click', () => {
                        playAudio('click');
                        addCameraRowUI(`cam_${port}`, `Camera Porta ${port}`, configStreamUrl);
                        showToast();
                    });

                    el.scanResultsList.appendChild(row);
                });
            } else {
                const activeIps = data.active_ips || [];
                const port = data.port;
                if (activeIps.length === 0) {
                    el.scanResultsList.innerHTML = `<span class="clock-date">Nessun host attivo rilevato sulla porta ${port}.</span>`;
                    return;
                }

                activeIps.forEach(ip => {
                    const row = document.createElement('div');
                    row.className = 'scan-result-row';
                    
                    const localHostVal = el.cfgLocalHost.value.trim();
                    const isLocalTarget = (ip === '127.0.0.1' || ip === 'localhost' || ip === localHostVal || ip === state.detectedHostIp);
                    const streamHost = isLocalTarget ? '{host}' : ip;
                    const absoluteStreamUrl = `http://${ip}:${port}`;
                    const configStreamUrl = `http://${streamHost}:${port}`;

                    row.innerHTML = `
                        <span>Host <strong>${ip}</strong> attivo su porta ${port}</span>
                        <div class="scan-result-actions">
                            <button type="button" class="control-btn scan-btn-small" id="prev-${ip.replace(/\./g, '-')}">Anteprima</button>
                            <button type="button" class="control-btn active scan-btn-small" id="add-${ip.replace(/\./g, '-')}">Aggiungi</button>
                        </div>
                    `;

                    row.querySelector(`#prev-${ip.replace(/\./g, '-')}`).addEventListener('click', () => {
                        playAudio('click');
                        el.scanPreviewImg.src = absoluteStreamUrl;
                        el.scanPreviewWrapper.style.display = 'block';
                    });

                    row.querySelector(`#add-${ip.replace(/\./g, '-')}`).addEventListener('click', () => {
                        playAudio('click');
                        const lastOctet = ip.split('.').pop();
                        addCameraRowUI(`cam_${lastOctet}`, `Camera ${ip}`, configStreamUrl);
                        showToast();
                    });

                    el.scanResultsList.appendChild(row);
                });
            }
        }

        // Salva configurazione
        function saveSettings() {
            playAudio('click');

            const subnetInput = el.cfgSubnetPrefixes.value;
            const subnetPrefixes = subnetInput.split(',')
                .map(p => p.trim())
                .filter(p => p.length > 0);

            const disks = [];
            document.querySelectorAll('.disk-row').forEach(row => {
                const name = row.querySelector('.disk-name-input').value.trim();
                const path = row.querySelector('.disk-path-input').value.trim();
                if (name && path) {
                    disks.push({ name, path });
                }
            });

            const cameras = [];
            document.querySelectorAll('.cam-row').forEach(row => {
                const id = row.querySelector('.cam-id-input').value.trim();
                const name = row.querySelector('.cam-name-input').value.trim();
                const url = row.querySelector('.cam-url-input').value.trim();
                
                const brightness = parseInt(row.querySelector('.filter-brightness').value, 10);
                const contrast = parseInt(row.querySelector('.filter-contrast').value, 10);
                const grayscale = parseInt(row.querySelector('.filter-grayscale').value, 10);
                const sharp = row.querySelector('.filter-sharp').checked;

                if (id && name && url) {
                    cameras.push({ 
                        id, 
                        name, 
                        stream_url: url,
                        filters: { brightness, contrast, grayscale, sharp }
                    });
                }
            });

            const updatedConfig = {
                server: {
                    local_host: el.cfgLocalHost.value.trim(),
                    remote_host: el.cfgRemoteHost.value.trim(),
                    subnet_prefixes: subnetPrefixes,
                    audio_enabled_default: el.cfgAudioEnabled.checked
                },
                disks: disks,
                cameras: cameras
            };

            el.settingsSave.disabled = true;
            el.settingsSave.textContent = 'Salvataggio...';

            fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(updatedConfig)
            })
            .then(res => {
                if (!res.ok) throw new Error('Errore nel salvataggio dei dati');
                return res.json();
            })
            .then(data => {
                if (data.error) {
                    alert('Errore: ' + data.error);
                } else {
                    closeSettingsModal();
                    showToast();
                    fetchData();
                }
            })
            .catch(err => {
                console.error(err);
                alert('Impossibile salvare la configurazione. Controlla che il server sia raggiungibile.');
            })
            .finally(() => {
                el.settingsSave.disabled = false;
                el.settingsSave.textContent = 'Salva Configurazione';
            });
        }

        function showToast() {
            el.toast.classList.add('show');
            setTimeout(() => {
                el.toast.classList.remove('show');
            }, 3000);
        }

        function playAudio(type) {
            if (!state.audioEnabled) return;
            
            if (type === 'hover') {
                el.audioHover.currentTime = 0;
                el.audioHover.play().catch(() => {});
            } else if (type === 'click') {
                el.audioClick.currentTime = 0;
                el.audioClick.play().catch(() => {});
            }
        }

        function updateAudioButtonUI() {
            if (state.audioEnabled) {
                el.audioToggle.classList.add('active');
                el.audioText.textContent = "Audio Attivo";
                el.audioIcon.innerHTML = `
                    <path d="M11.536 14.01A8.47 8.47 0 0 0 14.026 8a8.47 8.47 0 0 0-2.49-6.01l-.708.707A7.48 7.48 0 0 1 13.025 8c0 2.071-.84 3.946-2.197 5.303z"/>
                    <path d="M10.121 12.596A6.48 6.48 0 0 0 12.025 8a6.48 6.48 0 0 0-1.904-4.596l-.707.707A5.48 5.48 0 0 1 11.025 8a5.48 5.48 0 0 1-1.61 3.89z"/>
                    <path d="M8.707 11.182A4.5 4.5 0 0 0 10.025 8a4.5 4.5 0 0 0-1.318-3.182L8 5.525A3.5 3.5 0 0 1 9.025 8 3.5 3.5 0 0 1 8 10.475z"/>
                    <path d="M6.717 3.55A.5.5 0 0 1 7 4v8a.5.5 0 0 1-.812.39L3.825 10.5H1.5A.5.5 0 0 1 1 10V6a.5.5 0 0 1 .5-.5h2.325l2.363-1.89a.5.5 0 0 1 .529-.06"/>
                `;
            } else {
                el.audioToggle.classList.remove('active');
                el.audioText.textContent = "Audio Disattivato";
                el.audioIcon.innerHTML = `
                    <path d="M6.717 3.55A.5.5 0 0 1 7 4v8a.5.5 0 0 1-.812.39L3.825 10.5H1.5A.5.5 0 0 1 1 10V6a.5.5 0 0 1 .5-.5h2.325l2.363-1.89a.5.5 0 0 1 .529-.06zm7.137 2.096a.5.5 0 0 1 0 .708L12.207 8l1.647 1.646a.5.5 0 0 1-.708.708L11.5 8.707l-1.646 1.647a.5.5 0 0 1-.708-.708L10.793 8 9.146 6.354a.5.5 0 1 1 .708-.708L11.5 7.293l1.646-1.647a.5.5 0 0 1 .708 0z"/>
                `;
            }
        }
    </script>
</body>
</html>
