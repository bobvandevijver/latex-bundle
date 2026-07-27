#!/usr/bin/env python3

"""
Podman Runner Service
--------------------
A simple multithreaded service that listens on a local socket for requests
to execute podman commands.

Protocol:
- Request: JSON object with following keys (only "volumes" is optional):
  - "working_directory": Working directory for the execution on the host.
  - "volumes": List of volume binds (e.g., ["/src:/dest:ro"]).
  - "command": Either "pdflatex" or "bibtex".
  - "args": List of command arguments.
- Response: JSON object with "stdout", "stderr", and "status" (exit code).

PHP Example Usage:
```php
$request = json_encode([
    'working_directory' => '/home/user',
    'volumes' => ['/home/user/data:/data:rw'],
    'command' => 'pdflatex',
    'pdflatex_args' => ['-no-shell-escape', '<file>']
]);
$fp = fsockopen("unix:///var/run/bobv-latex-pdflatex-runner.sock", -1, $errno, $errstr, 30);
if ($fp) {
    fwrite($fp, $request);
    stream_socket_shutdown($fp, STREAM_SHUT_WR);
    $response = stream_get_contents($fp);
    fclose($fp);
    $data = json_decode($response, true);
}
```
"""

import socket
import threading
import subprocess
import json
import os
import sys
import logging
import signal

# Configuration
SOCKET_PATH = os.path.splitext(os.path.basename(__file__))[0] + '.sock'
CONTAINER_IMAGE = 'registry.drenso.dev/open/docker-images/pdflatex:latest'

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s [%(threadName)s] %(levelname)s: %(message)s',
    stream=sys.stdout
)

def handle_client(conn, addr):
    logging.info(f"Connected by {addr}")
    try:
        # Read until EOF
        raw_data = b""
        while True:
            chunk = conn.recv(4096)
            if not chunk:
                break
            raw_data += chunk

        if not raw_data:
            logging.warning("Received empty request")
            return

        try:
            request = json.loads(raw_data.decode('utf-8'))
        except json.JSONDecodeError as e:
            logging.error(f"Invalid JSON: {e}")
            conn.sendall(json.dumps({"error": f"Invalid JSON: {str(e)}"}).encode('utf-8'))
            return

        working_dir = request.get('working_directory')
        if not working_dir:
            logging.warning("No working dir provided in request")
            conn.sendall(json.dumps({"error": "No working_dir provided"}).encode('utf-8'))
            return

        command = request.get('command')
        if not command:
            logging.warning("No command provided in request")
            conn.sendall(json.dumps({"error": "No command provided"}).encode('utf-8'))
            return
        if command != 'pdflatex' and command != 'bibtex':
            logging.warning("Invalid command provided in request")
            conn.sendall(json.dumps({"error": "Invalid command provided, only pdflatex and bibtex allowed"}).encode('utf-8'))
            return

        args = request.get('args', [])
        if not args:
            logging.warning("No arguments provided in request")
            conn.sendall(json.dumps({"error": "No arguments provided"}).encode('utf-8'))
            return

        volumes = request.get('volumes', [])

        # Construct the command
        podman_cmd = [
          'podman',
          'run',
          '--rm',
          '--read-only',
          '--tmpfs', '/tmp:rw,size=32m,noexec,nosuid',
          '--cap-drop', 'ALL',
          '--security-opt', 'no-new-privileges',
          '--memory', '512m',
          '--pids-limit', '20',
          '--cgroup-manager', 'cgroupfs',
          '--workdir', working_dir
        ]

        # Add volume binds
        for volume in volumes:
          podman_cmd.extend(['-v', volume])

        # Add image
        podman_cmd.append(CONTAINER_IMAGE)

        # Add command
        podman_cmd.append(command)

        # Add the rest of the arguments
        podman_cmd.extend(args)

        logging.info(f"Executing: {' '.join(podman_cmd)}")

        try:
            # Execute the command
            result = subprocess.run(
                podman_cmd,
                capture_output=True,
                text=True,
            )
            response = {
                "stdout": result.stdout.strip(),
                "stderr": result.stderr.strip(),
                "status": result.returncode
            }
            logging.info(f"Command finished with status {result.returncode}")
        except FileNotFoundError:
            logging.error("podman command not found")
            response = {
                "error": "podman command not found",
                "status": 127
            }
        except Exception as e:
            logging.exception(f"Error executing command: {e}")
            response = {
                "error": str(e),
                "status": -1
            }

        conn.sendall(json.dumps(response).encode('utf-8'))
    except Exception as e:
        logging.exception(f"Exception handling client {addr}: {e}")
    finally:
        conn.close()
        logging.info(f"Connection with {addr} closed")

def run_service():
    # Ensure any previous socket file is removed
    if os.path.exists(SOCKET_PATH):
        try:
            os.remove(SOCKET_PATH)
        except OSError as e:
            logging.error(f"Failed to remove existing socket {SOCKET_PATH}: {e}")
            return

    server = socket.socket(socket.AF_UNIX, socket.SOCK_STREAM)

    try:
        server.bind(SOCKET_PATH)
        # Ensure the socket is accessible (e.g., by PHP-FPM)
        os.chmod(SOCKET_PATH, 0o666)
    except Exception as e:
        logging.error(f"Failed to bind to {SOCKET_PATH}: {e}")
        return

    server.listen()
    logging.info(f"Service listening on Unix socket {SOCKET_PATH}")

    def signal_handler(sig, frame):
        logging.info(f"Received signal {sig}, stopping...")
        sys.exit(0)

    signal.signal(signal.SIGINT, signal_handler)
    signal.signal(signal.SIGTERM, signal_handler)

    try:
        while True:
            conn, addr = server.accept()
            client_thread = threading.Thread(
                target=handle_client,
                args=(conn, addr or 'localsock'),
                daemon=True
            )
            client_thread.start()
    except (KeyboardInterrupt, SystemExit):
        logging.info("Service stopping...")
    except Exception as e:
        logging.error(f"Service error: {e}")
    finally:
        server.close()
        if os.path.exists(SOCKET_PATH):
            try:
                os.remove(SOCKET_PATH)
                logging.info(f"Removed socket {SOCKET_PATH}")
            except OSError as e:
                logging.error(f"Failed to remove socket {SOCKET_PATH}: {e}")

if __name__ == "__main__":
    run_service()
