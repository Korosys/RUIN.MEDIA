<?php
// Create uploads directory if missing
$uploadDir = 'uploads/';
$previewFile = ''; // Initialize preview file variable
if (!is_dir($uploadDir)) {
    if (!@mkdir($uploadDir, 0755, true)) {
        die("Failed to create upload directory. Please check permissions.");
    }
}

// Check directory permissions
if (!is_writable($uploadDir)) {
    die("Upload directory is not writable");
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
        // Get original filename and extension
        $originalName = $_FILES['audio_file']['name'];
        $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'bmp']);
        
        // Generate files with sanitized names
        $inputFile = $uploadDir . uniqid('input_') . '_' . preg_replace('/[^A-Za-z0-9\._\-]/', '_', $originalName);
        $outputFile = $isImage
            ? $uploadDir . uniqid('output_') . '.jpg'
            : $uploadDir . uniqid('output_') . '.mp3';
        
        // Save uploaded file
        if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $inputFile)) {
            if ($isImage) {
                // Image processing
                if ($_POST['extreme_quality'] == '1') {
                    // Extreme quality reduction for images
                    $cmd = "ffmpeg -i ".escapeshellarg($inputFile)." -vf 'scale=iw/32:-1,scale=iw*8:-1:flags=neighbor,noise=alls=20:allf=t' -q:v 2 ".escapeshellarg($outputFile)." -y 2>&1";
                } else {
                    // Standard quality reduction for images
                    $cmd = "ffmpeg -i ".escapeshellarg($inputFile)." -vf 'scale=iw/16:-1,scale=iw*8:-1:flags=neighbor,noise=alls=20:allf=t' -q:v 2 ".escapeshellarg($outputFile)." -y 2>&1";
                }
            } else {
                // Audio processing
                // Detect audio channels
                $channelCmd = "ffprobe -i ".escapeshellarg($inputFile)." -show_entries stream=channels -of compact=p=0:nk=1 -v 0 2>&1";
                $channels = trim(shell_exec($channelCmd));
            
            if (!is_numeric($channels) || $channels < 1) {
                $channels = 1; // Default to mono if detection fails
            }
            
            // Build command based on quality toggle
                if (!is_numeric($channels) || $channels < 1) {
                    $channels = 1; // Default to mono if detection fails
                }
                
                // Build command based on quality toggle
                if ($_POST['extreme_quality'] == '1') {
                    $cmd = "ffmpeg -i ".escapeshellarg($inputFile)." -vn ";
                    $cmd .= "-filter_complex \"";

                    if ($channels > 1) {
                        // Stereo processing
                        $cmd .= "channelsplit [left][right]; ";
                        $cmd .= "[left] aresample=8000, aphaser=in_gain=0.9:out_gain=1:speed=2, acompressor=level_in=24:threshold=0.1:ratio=9, bandpass=f=1500, volume=6dB [left_processed]; ";
                        $cmd .= "[right] aresample=8000, aphaser=in_gain=0.9:out_gain=1:speed=2, acompressor=level_in=24:threshold=0.1:ratio=9, bandpass=f=1500, volume=6dB [right_processed]; ";
                        $cmd .= "[left_processed][right_processed] amix=inputs=2";
                    } else {
                        // Mono processing
                        $cmd .= "aresample=8000, aphaser=in_gain=0.9:out_gain=1:speed=2, acompressor=level_in=24:threshold=0.1:ratio=9, bandpass=f=1500, volume=6dB";
                    }

                    $cmd .= "\" ";
                    $cmd .= "-ar 8000 -b:a 8k ".escapeshellarg($outputFile)." -y 2>&1";
                } else {
                    // Standard quality reduction
                    $cmd = "ffmpeg -i ".escapeshellarg($inputFile)." -vn -ac 1 -ar 8000 -b:a 16k ".escapeshellarg($outputFile)." -y 2>&1";
                }
            }
            
            // Execute FFmpeg command
            $commandOutput = [];
            $returnCode = 0;
            exec($cmd, $commandOutput, $returnCode);
            
            if ($returnCode === 0 && file_exists($outputFile)) {
                // Prepare download filename with appropriate suffix and extension
                $suffix = ($_POST['extreme_quality'] == '1') ? '_bitcrushed_extreme' : '_bitcrushed';
                $downloadExt = $isImage ? '.jpg' : '.mp3';
                $downloadFilename = $baseName . $suffix . $downloadExt;
                
                // Debug: Log the actual extreme_quality value
                error_log("extreme_quality value: " . $_POST['extreme_quality']);
                error_log("Generated suffix: " . $suffix);
                
                // Clean and sanitize filename
                $safeFilename = preg_replace("/[^A-Za-z0-9\._-]/", '_', $downloadFilename);
                
                // Store processed file info for preview
                $previewFile = basename($outputFile);
                $downloadLink = '/' . $uploadDir . $previewFile; // Make path absolute
                
                // Delete input file only (keep output for preview)
                unlink($inputFile);
            } else {
                $ffmpegError = $commandOutput ? implode("<br>", array_slice($commandOutput, -10)) : 'No output';
                $error = "Processing failed. FFmpeg error: " . htmlspecialchars($ffmpegError);
            }
        } else {
            $error = "Failed to move uploaded file";
        }
    } else {
        $error = isset($_FILES['audio_file']) ? 
                 getUploadErrorMessage($_FILES['audio_file']['error']) : 
                 "Invalid file upload";
    }
}

function getUploadErrorMessage($errorCode) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => "File too large (server limit)",
        UPLOAD_ERR_FORM_SIZE => "File too large (form limit)",
        UPLOAD_ERR_PARTIAL => "Partial upload",
        UPLOAD_ERR_NO_FILE => "No file uploaded",
        UPLOAD_ERR_NO_TMP_DIR => "Missing temp folder",
        UPLOAD_ERR_CANT_WRITE => "Cannot write file",
        UPLOAD_ERR_EXTENSION => "File upload blocked"
    ];
    return $errors[$errorCode] ?? "Unknown upload error (Code: $errorCode)";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ruin.Media</title>
    <style>
        :root {
            --primary: #5e35b1;
            --secondary: #d81b60;
            --background: #121212;
            --surface: #1e1e1e;
            --error: #cf6679;
            --success: #4caf50;
        }
        
        body { 
            font-family: 'Roboto', sans-serif; 
            margin: 0;
            padding: 0;
            background: var(--background);
            color: rgba(255, 255, 255, 0.87);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            max-width: 600px;
            width: 100%;
            background: var(--surface);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
            overflow: hidden;
            margin: 20px;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 30px 20px;
            text-align: center;
        }
        
        header h1 {
            margin: 0;
            font-size: 2.2rem;
            letter-spacing: 1.5px;
            position: relative;
        }
        
        header h1:after {
            content: "";
            height: 4px;
            width: 150px;
            background: white;
            position: absolute;
            bottom: -15px;
            left: calc(50% - 75px);
            border-radius: 10px;
        }
        
        .content {
            padding: 30px;
        }
        
        .file-upload-container {
            border: 2px dashed rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 35px 0;
            text-align: center;
            margin: 20px 0 30px;
            transition: all 0.3s;
        }
        
        .file-upload-container:hover {
            border-color: var(--primary);
            background: rgba(94, 53, 177, 0.05);
        }
        
        .file-upload-container.active {
            border-color: var(--success);
            background: rgba(76, 175, 80, 0.05);
        }
        
        .file-upload-label {
            display: block;
            font-size: 1.2rem;
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        input[type="file"] {
            width: 80%;
            padding: 12px;
            background: transparent;
            color: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 6px;
            cursor: pointer;
        }
        
        .settings-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .settings-title {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .settings-title h2 {
            margin: 0 0 0 10px;
            font-size: 1.4rem;
            color: var(--secondary);
        }
        
        .mode-selector {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .mode {
            flex: 1;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 18px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .mode:hover {
            background: rgba(94, 53, 177, 0.15);
        }
        
        .mode.selected {
            background: rgba(94, 53, 177, 0.25);
            border-color: var(--primary);
        }
        
        .mode input {
            display: none;
        }
        
        .mode h3 {
            margin: 10px 0;
            color: white;
            font-size: 1.2rem;
        }
        
        .mode ul {
            text-align: left;
            margin: 10px 0 5px;
            padding-left: 20px;
            font-size: 0.9rem;
            opacity: 0.7;
        }
        
        .function-button {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            color: white;
            padding: 18px;
            font-size: 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 5px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .function-button:after {
            content: "";
            position: absolute;
            top: -100%;
            left: -100%;
            width: 300%;
            height: 300%;
            background: linear-gradient(135deg, rgba(255,255,255,0), hsla(0,0%,100%,0.2), rgba(255,255,255,0));
            transform: rotate(45deg);
            animation: shine 4s infinite;
        }
        
        @keyframes shine {
            0% {
                left: -200%;
            }
            100% {
                left: 150%;
            }
        }
        
        .function-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 25px rgba(216, 27, 96, 0.4);
        }
        
        .function-button:active {
            transform: translateY(1px);
        }
        
        .info-message {
            background: rgba(255, 255, 255, 0.07);
            border-left: 4px solid var(--primary);
            padding: 12px 15px;
            border-radius: 0 8px 8px 0;
            margin: 20px 0;
            font-size: 0.9rem;
        }
        
        .error-message {
            background: rgba(207, 102, 121, 0.15);
            border-left: 4px solid var(--error);
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
            opacity: 0;
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s;
        }
        
        .error-message.show {
            opacity: 1;
            max-height: 200px;
        }
        
        .gore-icon {
            font-size: 3.5rem;
            margin-bottom: 15px;
            display: inline-block;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>RUIN.MEDIA</h1>
        </header>
        
        <div class="content">
            <?php if (!empty($error)): ?>
                <div class="error-message show">
                    <strong>ERROR:</strong> <?= $error ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" id="bitcrusher-form">
                <div class="file-upload-container" id="drop-area">
                    <div class="gore-icon">🔊🖻</div>
                    <span class="file-upload-label">SELECT OR DRAG A FILE TO RUIN</span>
                    <input type="file" name="audio_file" id="audio-file" accept="audio/*,image/*" required>
                </div>
                
                <div class="settings-card">
                    <div class="settings-title">
                        <div class="gore-icon">⚙️</div>
                        <h2>SETTINGS</h2>
                    </div>
                    
                    <div class="mode-selector">
                        <label class="mode" id="mode-standard">
                            <input type="radio" name="mode" value="standard" checked>
                            <h3>STANDARD</h3>
                        </label>
                        
                        <label class="mode" id="mode-extreme">
                            <input type="radio" name="mode" value="extreme">
                            <div class="overlay-flash"></div>
                            <h3>RUINATION</h3>
                        </label>
                    </div>
                    <input type="hidden" id="extreme_quality" name="extreme_quality" value="0">
                </div>
                
                <div class="info-message">
                    <strong>NOTE:</strong> Files are deleted after 5 minutes.
                </div>
                
                <button type="submit" class="function-button">
                    RUIN ME
                </button>
            </form>
            
            <?php if (!empty($previewFile)): ?>
                <div class="preview-container" style="margin-top: 30px; text-align: center;">
                    <h3>Preview:</h3>
                    <?php if ($isImage): ?>
                        <img src="<?= $downloadLink ?>" alt="Processed Image" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                    <?php else: ?>
                        <audio controls style="width: 100%;">
                            <source src="<?= $downloadLink ?>" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    <?php endif; ?>
                    <p>
                        <a href="<?= $downloadLink ?>" download="<?= $safeFilename ?>" class="function-button" style="display: inline-block; width: auto; padding: 10px 20px; margin-top: 15px;">
                            Download
                        </a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Toggle quality modes
        const modeStandard = document.getElementById('mode-standard');
        const modeExtreme = document.getElementById('mode-extreme');
        const qualityInput = document.getElementById('extreme_quality');
        
        modeStandard.addEventListener('click', () => {
            modeStandard.classList.add('selected');
            modeExtreme.classList.remove('selected');
            qualityInput.value = "0";
        });
        
        modeExtreme.addEventListener('click', () => {
            modeExtreme.classList.add('selected');
            modeStandard.classList.remove('selected');
            qualityInput.value = "1";
        });
        
        // Set initial selection and value
        modeStandard.classList.add('selected');
        qualityInput.value = "0";
        
        // File upload drag and drop
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('audio-file');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => {
                dropArea.classList.add('active');
            }, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => {
                dropArea.classList.remove('active');
            }, false);
        });
        
        dropArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
        }
    </script>
</body>
</html>