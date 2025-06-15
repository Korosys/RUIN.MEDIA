
<!DOCTYPE html>
<html lang="en">
<head>
<meta property="og:title" content="Ruin.Media">
    <meta property="og:description" content="Making the internet worse, one file at a time.">
    <meta property="og:image" content="https://ruin.media/favicon.ico">
    <meta name="twitter:card" content="summary_large_image">
    <meta charset="UTF-8">
<link rel="icon" href="favicon.ico" type="image/x-icon">
    <title>RUIN.MEDIA</title>
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
            <div class="error-message" id="error-message" style="display: none;">
                <strong>ERROR:</strong> <span id="error-text"></span>
            </div>
            
            <form id="bitcrusher-form">
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
                    <strong>NOTE:</strong> Files are not sent to the server. All processing happens in your browser.
                </div>
                
                <button type="submit" class="function-button">
                    RUIN ME
                </button>
            </form>
            
            <div class="preview-container" id="preview-container" style="margin-top: 30px; text-align: center; display: none;">
                <h3>Preview:</h3>
                <div id="image-preview" style="display: none;">
                    <canvas id="processed-image-canvas"></canvas>
                    <img id="processed-image" alt="Processed Image" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                </div>
                <div id="audio-preview" style="display: none;">
                    <audio id="processed-audio" controls style="width: 100%;">
                        Your browser does not support the audio element.
                    </audio>
                </div>
                <p>
                    <a id="download-link" class="function-button" style="display: inline-block; width: auto; padding: 10px 20px; margin-top: 15px;">
                        Download
                    </a>
                </p>
            </div>
        </div>
    </div>
<div style="text-align: center; padding: 20px; margin-top: 30px;">
        <a href="https://ko-fi.com/korosys" target="_blank" 
           style="display: inline-block; 
                  background: linear-gradient(135deg, #5e35b1, #d81b60);
                  color: white;
                  padding: 12px 24px;
                  border-radius: 8px;
                  font-weight: bold;
                  text-decoration: none;
                  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                  transition: all 0.3s;">
            DONATE
        </a>
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

        // Client-side processing functions
        const form = document.getElementById('bitcrusher-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const file = fileInput.files[0];
            if (!file) {
                showError('Please select a file');
                return;
            }
            
            const isImage = file.type.startsWith('image/');
            const isAudio = file.type.startsWith('audio/');
            
            if (!isImage && !isAudio) {
                showError('Invalid file type. Only audio and image files are allowed.');
                return;
            }
            
            const extremeQuality = qualityInput.value === "1";
            showProcessing(true);
            
            try {
                if (isImage) {
                    await processImage(file, extremeQuality);
                } else {
                    await processAudio(file, extremeQuality);
                }
            } catch (err) {
                showError(`Processing failed: ${err.message}`);
                showProcessing(false);
            }
        });

        function showError(message) {
            const errorElement = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');
            errorText.textContent = message;
            errorElement.style.display = 'block';
            errorElement.classList.add('show');
        }

        function showProcessing(show) {
            const button = document.querySelector('.function-button[type="submit"]');
            if (show) {
                button.textContent = 'Processing...';
                button.disabled = true;
            } else {
                button.textContent = 'RUIN ME';
                button.disabled = false;
            }
        }

        async function processImage(file, extreme) {
            const reader = new FileReader();
            
            return new Promise((resolve, reject) => {
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        try {
                            const canvas = document.getElementById('processed-image-canvas');
                            const ctx = canvas.getContext('2d');
                            
                            // Set canvas size to image size
                            canvas.width = img.width;
                            canvas.height = img.height;
                            
                            // Determine scale factors
                            const downscaleFactor = extreme ? 32 : 16;
                            const upscaleFactor = 8;
                            
                            // Step 1: Downscale
                            const smallWidth = Math.max(1, Math.floor(img.width / downscaleFactor));
                            const smallHeight = Math.max(1, Math.floor(img.height / downscaleFactor));
                            
                            // Create temporary canvas for downscaling
                            const tempCanvas = document.createElement('canvas');
                            const tempCtx = tempCanvas.getContext('2d');
                            tempCanvas.width = smallWidth;
                            tempCanvas.height = smallHeight;
                            tempCtx.drawImage(img, 0, 0, smallWidth, smallHeight);
                            
                            // Clear main canvas
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            
                            // Step 2: Upscale with nearest neighbor
                            ctx.imageSmoothingEnabled = false;
                            ctx.drawImage(tempCanvas, 0, 0, smallWidth, smallHeight, 0, 0, img.width, img.height);
                            
                            // Step 3: Add noise
                            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                            const data = imageData.data;
                            const strength = extreme ? 50 : 30;
                            
                            for (let i = 0; i < data.length; i += 4) {
                                const noise = (Math.random() - 0.5) * strength;
                                data[i] = Math.max(0, Math.min(255, data[i] + noise));     // R
                                data[i+1] = Math.max(0, Math.min(255, data[i+1] + noise)); // G
                                data[i+2] = Math.max(0, Math.min(255, data[i+2] + noise)); // B
                            }
                            
                            ctx.putImageData(imageData, 0, 0);
                            
                            // Convert to JPEG with low quality
                            const processedImage = document.getElementById('processed-image');
                            processedImage.src = canvas.toDataURL('image/jpeg', extreme ? 0.1 : 0.3);
                            canvas.style.display = 'none';
                            
                            // Show preview and download link
                            document.getElementById('image-preview').style.display = 'block';
                            document.getElementById('audio-preview').style.display = 'none';
                            document.getElementById('preview-container').style.display = 'block';
                            
                            const downloadLink = document.getElementById('download-link');
                            downloadLink.href = processedImage.src;
                            downloadLink.download = `bitcrushed_${extreme ? 'extreme' : 'standard'}.jpg`;
                            
                            showProcessing(false);
                            resolve();
                        } catch (err) {
                            reject(err);
                        }
                    };
                    img.src = e.target.result;
                };
                reader.onerror = (e) => reject(new Error('Failed to read image file'));
                reader.readAsDataURL(file);
            });
        }

        async function processAudio(file, extreme) {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const arrayBuffer = await file.arrayBuffer();
            const audioBuffer = await audioContext.decodeAudioData(arrayBuffer);
            
            // Create offline context for processing
            const newSampleRate = extreme ? 8000 : 12000;
            const numberOfFrames = Math.ceil(audioBuffer.duration * newSampleRate);
            
            const offlineContext = new OfflineAudioContext(
                audioBuffer.numberOfChannels,
                numberOfFrames,
                newSampleRate
            );
            
            // Create audio source
            const source = offlineContext.createBufferSource();
            source.buffer = audioBuffer;
            
            // Apply processing effects
            if (extreme) {
                // Extreme processing chain (phaser and delay removed)
                const splitter = offlineContext.createChannelSplitter(2);
                const merger = offlineContext.createChannelMerger(2);
                
                // Create effects for each channel
                const createExtremeChain = (channel) => {
                    const filter = offlineContext.createBiquadFilter();
                    filter.type = 'bandpass';
                    filter.frequency.value = 1500;
                    
                    const inputGain = offlineContext.createGain();
                    inputGain.gain.value = 1.0;
                    
                    const outputGain = offlineContext.createGain();
                    outputGain.gain.value = 1.2;
                    
                    // Connect input directly to output (bypass removed effects)
                    inputGain.connect(outputGain);
                    
                    return { inputGain, outputGain, filter };
                };
                
                // Left channel processing
                const leftChain = createExtremeChain('left');
                source.connect(leftChain.inputGain);
                leftChain.outputGain.connect(merger, 0, 0);
                
                // Right channel processing
                const rightChain = createExtremeChain('right');
                source.connect(rightChain.inputGain);
                rightChain.outputGain.connect(merger, 0, 1);
                
                // Connect to output
                merger.connect(offlineContext.destination);
            } else {
                // Standard processing - simple downsampling
                source.connect(offlineContext.destination);
            }
            
            // Start processing
            source.start();
            const processedBuffer = await offlineContext.startRendering();
            
            // Create WAV blob
            const wavBlob = bufferToWav(processedBuffer);
            const url = URL.createObjectURL(wavBlob);
            
            // Show preview and download link
            const audioPlayer = document.getElementById('processed-audio');
            audioPlayer.src = url;
            
            document.getElementById('image-preview').style.display = 'none';
            document.getElementById('audio-preview').style.display = 'block';
            document.getElementById('preview-container').style.display = 'block';
            
            const downloadLink = document.getElementById('download-link');
            downloadLink.href = url;
            downloadLink.download = `bitcrushed_${extreme ? 'extreme' : 'standard'}.wav`;
            
            showProcessing(false);
        }

        function bufferToWav(buffer) {
            const numChannels = buffer.numberOfChannels;
            const sampleRate = buffer.sampleRate;
            const format = 1; // PCM
            const bitDepth = 16;
            
            const bytesPerSample = bitDepth / 8;
            const blockAlign = numChannels * bytesPerSample;
            
            const dataChunkSize = buffer.length * blockAlign;
            const bufferSize = 44 + dataChunkSize;
            
            const arrayBuffer = new ArrayBuffer(bufferSize);
            const view = new DataView(arrayBuffer);
            
            // Write WAV header
            writeString(view, 0, 'RIFF');
            view.setUint32(4, 36 + dataChunkSize, true);
            writeString(view, 8, 'WAVE');
            writeString(view, 12, 'fmt ');
            view.setUint32(16, 16, true); // fmt chunk size
            view.setUint16(20, format, true);
            view.setUint16(22, numChannels, true);
            view.setUint32(24, sampleRate, true);
            view.setUint32(28, sampleRate * blockAlign, true); // byte rate
            view.setUint16(32, blockAlign, true);
            view.setUint16(34, bitDepth, true);
            writeString(view, 36, 'data');
            view.setUint32(40, dataChunkSize, true);
            
            // Write audio data
            let offset = 44;
            for (let i = 0; i < buffer.length; i++) {
                for (let channel = 0; channel < numChannels; channel++) {
                    const sample = Math.max(-1, Math.min(1, buffer.getChannelData(channel)[i]));
                    const int16 = sample < 0 ? sample * 0x8000 : sample * 0x7FFF;
                    view.setInt16(offset, int16, true);
                    offset += 2;
                }
            }
            
            return new Blob([view], { type: 'audio/wav' });
        }

        function writeString(view, offset, string) {
            for (let i = 0; i < string.length; i++) {
                view.setUint8(offset + i, string.charCodeAt(i));
            }
        }
    </script>
</body>
</html>