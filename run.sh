#!/usr/bin/env bash
#
# IPCam Viewer - Avvio rapido per Linux/macOS
# =============================================
# Usa --port per cambiare porta (default: 8001)
# Usa --no-browser per non aprire il browser automaticamente
#

set -e

# Verifica che Python sia installato
if ! command -v python3 &> /dev/null; then
    echo "================================================================"
    echo "[ERROR] Python 3 non e' installato."
    echo ""
    echo "Installalo con il package manager della tua distribuzione:"
    echo "  Debian/Ubuntu: sudo apt install python3"
    echo "  Fedora:        sudo dnf install python3"
    echo "  macOS:         brew install python3"
    echo "================================================================"
    exit 1
fi

cd "$(dirname "$0")"

echo "================================================================"
echo " Avvio IPCam Viewer Server..."
echo "================================================================"

python3 server.py "$@"
