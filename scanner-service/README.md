# Bill Scanner Service

A local HTTP service that enables scanner access for the Bill Software web application.

## Requirements

- **Node.js** (v14 or later) - Download from [nodejs.org](https://nodejs.org/)
- A TWAIN/WIA compatible scanner connected to your computer

## Quick Start

### Windows

1. Double-click `start-scanner-service.bat`
2. The first time, it will install dependencies automatically
3. Keep the window open while using the scanner feature

### Manual Start

```bash
# Install dependencies (first time only)
npm install

# Start the service
npm start
```

## How It Works

1. The service runs on `http://localhost:51234`
2. When you click the "Scanner" tab in Bill Software, it connects to this service
3. The service detects connected scanners and provides a list
4. When you click "Scan Receipt", the service triggers the scanner
5. The scanned image is sent back to the browser

## API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/status` | GET | Check if service is running |
| `/api/scanners` | GET | List available scanners |
| `/api/scan` | POST | Trigger a scan operation |

## Troubleshooting

### Scanner Not Detected

1. Make sure your scanner is connected and powered on
2. Check if the scanner driver is installed
3. Try scanning with another application first (like Paint) to verify the scanner works

### Service Not Starting

1. Make sure Node.js is installed: `node --version`
2. Try installing dependencies manually: `npm install`
3. Check if port 51234 is available

### Scan Fails

- Try a lower DPI setting (150 DPI is fastest)
- Make sure no other application is using the scanner
- Restart the scanner service

## Auto-Start on Windows Boot

To run the scanner service automatically when Windows starts:

1. Press `Win + R`, type `shell:startup`, press Enter
2. Create a shortcut to `start-scanner-service.bat` in this folder

## License

MIT License - Part of Bill Software
