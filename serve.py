#!/usr/bin/env python3
"""
Government Workflow OS — Zero-Dependency Local Web Server
Runs locally on http://localhost:8000 using Python's built-in HTTP server
"""

import http.server
import socketserver
import webbrowser
import os
import sys

PORT = 8000
DIRECTORY = os.path.dirname(os.path.abspath(__file__))

class GovernmentOSHandler(http.server.SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, directory=DIRECTORY, **kwargs)

    def do_GET(self):
        # Route root or index.php to index.html for static rendering
        if self.path in ('/', '/index.php', '/index.php/'):
            self.path = '/index.html'
        elif self.path in ('/login.php', '/login.php/'):
            self.path = '/login.html'
        elif self.path.startswith('/pages/') and self.path.endswith('.php'):
            self.path = self.path[:-4] + '.html'

        return super().do_GET()

    def guess_type(self, path):
        # Ensure proper UTF-8 content-types so browsers render instead of downloading
        mtype = super().guess_type(path)
        if path.endswith('.html') or path.endswith('.php'):
            return 'text/html; charset=utf-8'
        elif path.endswith('.css'):
            return 'text/css; charset=utf-8'
        elif path.endswith('.js'):
            return 'application/javascript; charset=utf-8'
        elif path.endswith('.svg'):
            return 'image/svg+xml'
        return mtype

def main():
    os.chdir(DIRECTORY)
    with socketserver.TCPServer(("", PORT), GovernmentOSHandler) as httpd:
        url = f"http://localhost:{PORT}/index.html"
        print("=" * 60)
        print("  GOVERNMENT WORKFLOW OS - LOCAL SERVER")
        print("=" * 60)
        print(f"  URL:    {url}")
        print(f"  Folder: {DIRECTORY}")
        print("  Status: Running (Press Ctrl+C to stop)")
        print("=" * 60)

        webbrowser.open(url)

        try:
            httpd.serve_forever()
        except KeyboardInterrupt:
            print("\nServer stopped.")
            sys.exit(0)

if __name__ == '__main__':
    main()
