/**
 * Bill Scanner Service v1.7
 * Higher quality - target 400-600 KB
 */

const express = require('express');
const cors = require('cors');
const path = require('path');
const fs = require('fs');
const { exec } = require('child_process');

const app = express();
const PORT = 51234;

app.use(cors());
app.use(express.json({ limit: '50mb' }));

const SCAN_OUTPUT_DIR = path.join(__dirname, 'scans');
if (!fs.existsSync(SCAN_OUTPUT_DIR)) {
    fs.mkdirSync(SCAN_OUTPUT_DIR, { recursive: true });
}

app.get('/api/status', (req, res) => {
    res.json({ success: true, service: 'Bill Scanner Service', version: '1.7' });
});

app.get('/api/scanners', async (req, res) => {
    const scanners = await detectScanners();
    res.json({ success: true, scanners });
});

app.post('/api/scan', async (req, res) => {
    console.log('[SCAN] Request received');
    try {
        const result = await performScan();
        const sizeKB = Math.round(result.base64Image.length * 0.75 / 1024);
        console.log('[SCAN] Final size:', sizeKB, 'KB');
        res.json({
            success: true,
            message: 'Scan completed',
            image: result.base64Image,
            size: sizeKB * 1024
        });
    } catch (error) {
        console.error('[SCAN] Error:', error.message);
        res.status(500).json({ success: false, message: error.message });
    }
});

async function detectScanners() {
    const vbsPath = path.join(__dirname, 'detect.vbs');
    fs.writeFileSync(vbsPath, `On Error Resume Next
Set dm = CreateObject("WIA.DeviceManager")
For i = 1 To dm.DeviceInfos.Count
    Set di = dm.DeviceInfos.Item(i)
    If di.Type = 1 Then WScript.Echo "SCANNER:" & di.DeviceID & "|" & di.Properties("Name").Value
Next`);

    return new Promise((resolve) => {
        exec(`cscript //nologo "${vbsPath}"`, { timeout: 10000 }, (error, stdout) => {
            try { fs.unlinkSync(vbsPath); } catch (e) { }
            const scanners = [];
            (stdout || '').split('\n').forEach(line => {
                if (line.startsWith('SCANNER:')) {
                    const parts = line.substring(8).split('|');
                    if (parts.length >= 2) scanners.push({ id: parts[0].trim(), name: parts[1].trim() });
                }
            });
            if (scanners.length === 0) scanners.push({ id: 'default', name: 'Default Scanner' });
            resolve(scanners);
        });
    });
}

async function performScan() {
    const timestamp = Date.now();
    const rawFile = path.join(SCAN_OUTPUT_DIR, `raw_${timestamp}.jpg`);
    const compressedFile = path.join(SCAN_OUTPUT_DIR, `scan_${timestamp}.jpg`);

    console.log('[SCAN] Scanning...');

    const vbsPath = path.join(__dirname, 'scan.vbs');
    fs.writeFileSync(vbsPath, `On Error Resume Next
Set dm = CreateObject("WIA.DeviceManager")
Set dev = Nothing
For i = 1 To dm.DeviceInfos.Count
    Set di = dm.DeviceInfos.Item(i)
    If di.Type = 1 Then
        Set dev = di.Connect()
        Exit For
    End If
Next
If dev Is Nothing Then
    WScript.Echo "ERROR:No scanner"
    WScript.Quit 1
End If
Set item = dev.Items(1)
On Error Resume Next
item.Properties("6147").Value = 200
item.Properties("6148").Value = 200
item.Properties("6146").Value = 2
Set img = item.Transfer("{B96B3CAE-0728-11D3-9D7B-0000F81EF32E}")
If Err.Number <> 0 Then
    WScript.Echo "ERROR:" & Err.Description
    WScript.Quit 1
End If
img.SaveFile "${rawFile.replace(/\\/g, '\\\\')}"
WScript.Echo "SUCCESS"`);

    await new Promise((resolve, reject) => {
        exec(`cscript //nologo "${vbsPath}"`, { timeout: 120000 }, (error, stdout) => {
            try { fs.unlinkSync(vbsPath); } catch (e) { }
            if ((stdout || '').includes('ERROR:')) {
                reject(new Error((stdout.match(/ERROR:(.+)/) || [])[1] || 'Scan failed'));
            } else if (!fs.existsSync(rawFile)) {
                reject(new Error('Scan failed'));
            } else {
                resolve();
            }
        });
    });

    console.log('[SCAN] Raw size:', Math.round(fs.statSync(rawFile).size / 1024), 'KB');
    console.log('[SCAN] Compressing to 400-600 KB...');

    // Higher quality compression
    const psScript = path.join(__dirname, 'compress.ps1');
    fs.writeFileSync(psScript, `
Add-Type -AssemblyName System.Drawing

$img = [System.Drawing.Image]::FromFile("${rawFile.replace(/\\/g, '\\\\')}")

# Keep larger dimensions for better quality - max 2000px
$maxDim = 2000
$ratio = 1.0
if ($img.Width -gt $maxDim -or $img.Height -gt $maxDim) {
    $ratio = [Math]::Min($maxDim / $img.Width, $maxDim / $img.Height)
}
$newWidth = [int]($img.Width * $ratio)
$newHeight = [int]($img.Height * $ratio)

$bmp = New-Object System.Drawing.Bitmap($newWidth, $newHeight)
$g = [System.Drawing.Graphics]::FromImage($bmp)
$g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
$g.DrawImage($img, 0, 0, $newWidth, $newHeight)
$g.Dispose()
$img.Dispose()

$encoder = [System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders() | Where-Object { $_.MimeType -eq 'image/jpeg' }

# Higher quality - start at 85%
foreach ($quality in @(85, 80, 75, 70)) {
    $params = New-Object System.Drawing.Imaging.EncoderParameters(1)
    $params.Param[0] = New-Object System.Drawing.Imaging.EncoderParameter([System.Drawing.Imaging.Encoder]::Quality, [long]$quality)
    $bmp.Save("${compressedFile.replace(/\\/g, '\\\\')}", $encoder, $params)
    
    $sizeKB = [int]((Get-Item "${compressedFile.replace(/\\/g, '\\\\')}").Length / 1024)
    Write-Output "Quality $quality = $sizeKB KB"
    
    if ($sizeKB -le 900) { break }
}

$bmp.Dispose()
`);

    await new Promise((resolve) => {
        exec(`powershell -ExecutionPolicy Bypass -File "${psScript}"`, { timeout: 60000 }, (error, stdout) => {
            try { fs.unlinkSync(psScript); } catch (e) { }
            console.log('[COMPRESS]', (stdout || '').trim().replace(/\n/g, ' | '));
            resolve();
        });
    });

    try { fs.unlinkSync(rawFile); } catch (e) { }

    if (!fs.existsSync(compressedFile)) {
        throw new Error('Compression failed');
    }

    console.log('[SCAN] Final:', Math.round(fs.statSync(compressedFile).size / 1024), 'KB');

    const imageBuffer = fs.readFileSync(compressedFile);
    setTimeout(() => { try { fs.unlinkSync(compressedFile); } catch (e) { } }, 3000);

    return { base64Image: imageBuffer.toString('base64') };
}

app.listen(PORT, '127.0.0.1', () => {
    console.log('');
    console.log('==================================================');
    console.log('   Bill Scanner Service v1.7 (Higher Quality)');
    console.log('==================================================');
    console.log(`   URL: http://localhost:${PORT}`);
    console.log('   Target: 400-600 KB');
    console.log('   Quality: 85% JPEG');
    console.log('   Max dimension: 2000px');
    console.log('==================================================');
    console.log('');
});
