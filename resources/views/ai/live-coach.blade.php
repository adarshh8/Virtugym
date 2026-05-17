@extends('layouts.app')

@section('title', 'Live AI Form Coach')

@section('content')
<div style="max-width:1100px;margin:0 auto;" class="fade-in-up">
    <!-- Header -->
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem;">
        <div>
            <h1 style="font-size:2.2rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.5rem;">
                Live AI Form Coach
            </h1>
            <p style="color:var(--vg-text-muted);font-size:1rem;">Real-time posture analysis and rep counting using your webcam.</p>
        </div>
        <div style="display:flex;gap:10px;">
            <button id="startBtn" style="background:var(--vg-accent);color:white;border:none;padding:10px 20px;border-radius:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;transition:0.3s;">
                <i data-lucide="video"></i> Start Camera
            </button>
            <button id="stopBtn" style="background:rgba(239,68,68,0.2);color:#fca5a5;border:1px solid rgba(239,68,68,0.4);padding:10px 20px;border-radius:12px;font-weight:700;cursor:pointer;display:none;align-items:center;gap:8px;transition:0.3s;">
                <i data-lucide="video-off"></i> Stop
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;">
        
        <!-- Camera View -->
        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;overflow:hidden;position:relative;min-height:500px;display:flex;align-items:center;justify-content:center;box-shadow:0 10px 30px rgba(0,0,0,0.2);">
            
            <div id="loader" style="position:absolute;z-index:10;display:none;flex-direction:column;align-items:center;gap:15px;">
                <div style="width:40px;height:40px;border:4px solid var(--vg-border);border-top-color:var(--vg-accent);border-radius:50%;animation:spin 1s linear infinite;"></div>
                <p style="color:var(--vg-text-strong);font-weight:600;">Initializing AI Model...</p>
            </div>

            <div id="placeholder" style="text-align:center;color:var(--vg-text-muted);">
                <i data-lucide="camera" style="width:64px;height:64px;margin:0 auto 15px;opacity:0.5;"></i>
                <p>Click "Start Camera" to begin your session.</p>
                <p style="font-size:0.8rem;margin-top:5px;opacity:0.7;">Make sure your full body is visible.</p>
            </div>

            <!-- Hidden video element for processing -->
            <video id="input_video" style="display:none;" autoplay playsinline></video>
            
            <!-- Canvas where we draw the video and skeleton -->
            <canvas id="output_canvas" style="width:100%;height:100%;object-fit:cover;display:none;"></canvas>

            <!-- Form Feedback Overlay -->
            <div id="feedbackOverlay" style="position:absolute;bottom:20px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.7);backdrop-filter:blur(10px);border:1px solid var(--vg-border);padding:15px 30px;border-radius:50px;display:none;align-items:center;gap:15px;box-shadow:0 10px 25px rgba(0,0,0,0.5);">
                <div id="feedbackStatus" style="width:15px;height:15px;border-radius:50%;background:#22c55e;box-shadow:0 0 10px #22c55e;"></div>
                <p id="feedbackText" style="color:white;font-weight:700;font-size:1.2rem;letter-spacing:1px;">Perfect Form!</p>
            </div>
        </div>

        <!-- Dashboard Panel -->
        <div style="display:flex;flex-direction:column;gap:1.5rem;">
            
            <!-- Current Exercise -->
            <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:1.5rem;">
                <h3 style="color:var(--vg-text-muted);font-size:0.85rem;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;">Active Exercise</h3>
                <select id="exerciseSelect" style="width:100%;background:rgba(255,255,255,0.05);border:1px solid var(--vg-border);color:var(--vg-text-strong);padding:12px 15px;border-radius:12px;font-size:1rem;font-weight:600;outline:none;">
                    <option value="squat">Bodyweight Squat</option>
                    <option value="pushup">Push-up (Coming Soon)</option>
                </select>
            </div>

            <!-- Rep Counter -->
            <div style="background:linear-gradient(135deg, var(--vg-panel), var(--vg-panel-strong));border:1px solid var(--vg-border);border-radius:24px;padding:2rem 1.5rem;text-align:center;position:relative;overflow:hidden;">
                <div style="position:absolute;top:-20px;right:-20px;width:100px;height:100px;background:var(--vg-accent);filter:blur(60px);opacity:0.3;border-radius:50%;"></div>
                
                <h3 style="color:var(--vg-text-muted);font-size:0.85rem;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;">Rep Count</h3>
                <div id="repCount" style="font-size:5rem;font-weight:900;line-height:1;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:5px;">0</div>
                <p style="color:var(--vg-text-strong);font-weight:600;" id="phaseText">Ready</p>
                
                <button id="resetBtn" style="margin-top:20px;background:rgba(255,255,255,0.05);color:var(--vg-text-muted);border:1px solid var(--vg-border);padding:8px 15px;border-radius:8px;font-size:0.8rem;cursor:pointer;transition:0.3s;">
                    Reset Counter
                </button>
            </div>

            <!-- Instructions -->
            <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:1.5rem;flex-grow:1;">
                <h3 style="color:var(--vg-text-strong);font-size:1.1rem;font-weight:700;margin-bottom:15px;display:flex;align-items:center;gap:8px;">
                    <i data-lucide="info" style="width:18px;"></i> How it works
                </h3>
                <ul style="color:var(--vg-text-muted);font-size:0.9rem;line-height:1.6;padding-left:15px;display:flex;flex-direction:column;gap:10px;">
                    <li>Step back so your full body is visible.</li>
                    <li>Face the camera directly or at a slight angle.</li>
                    <li>The AI will automatically track your joints and count reps.</li>
                    <li>Watch the feedback bar at the bottom for posture corrections.</li>
                </ul>
            </div>

        </div>
    </div>
</div>

<style>
@keyframes spin { 100% { transform: rotate(360deg); } }
@media (max-width: 900px) {
    .fade-in-up > div:nth-child(2) { grid-template-columns: 1fr; }
}
</style>

<!-- Load MediaPipe Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/control_utils/control_utils.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/pose/pose.js" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const videoElement = document.getElementById('input_video');
    const canvasElement = document.getElementById('output_canvas');
    const canvasCtx = canvasElement.getContext('2d');
    
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const placeholder = document.getElementById('placeholder');
    const loader = document.getElementById('loader');
    
    const repCountEl = document.getElementById('repCount');
    const phaseText = document.getElementById('phaseText');
    const resetBtn = document.getElementById('resetBtn');
    
    const feedbackOverlay = document.getElementById('feedbackOverlay');
    const feedbackStatus = document.getElementById('feedbackStatus');
    const feedbackText = document.getElementById('feedbackText');

    let camera = null;
    let isRunning = false;
    
    // Squat Tracking Variables
    let reps = 0;
    let stage = "down"; // "up" or "down"
    
    // Angle calculation utility
    function calculateAngle(a, b, c) {
        const radians = Math.atan2(c.y - b.y, c.x - b.x) - Math.atan2(a.y - b.y, a.x - b.x);
        let angle = Math.abs((radians * 180.0) / Math.PI);
        if (angle > 180.0) {
            angle = 360 - angle;
        }
        return angle;
    }

    // Process Pose Results
    function onResults(results) {
        if (!isRunning) return;
        
        // Hide loader when first frame is processed
        if (loader.style.display !== 'none') {
            loader.style.display = 'none';
            canvasElement.style.display = 'block';
            feedbackOverlay.style.display = 'flex';
        }

        // Setup Canvas
        canvasElement.width = videoElement.videoWidth;
        canvasElement.height = videoElement.videoHeight;
        
        canvasCtx.save();
        canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
        
        // Draw Video Frame
        canvasCtx.drawImage(results.image, 0, 0, canvasElement.width, canvasElement.height);
        
        // Draw Skeleton
        if (results.poseLandmarks) {
            drawConnectors(canvasCtx, results.poseLandmarks, POSE_CONNECTIONS, {
                color: 'rgba(139, 92, 246, 0.8)', // Primary Accent
                lineWidth: 4
            });
            drawLandmarks(canvasCtx, results.poseLandmarks, {
                color: '#22c55e', // Green dots
                lineWidth: 2,
                radius: 4
            });

            // --- SQUAT LOGIC ---
            const landmarks = results.poseLandmarks;
            
            // Get coordinates (Using Left side for simplicity)
            const hip = landmarks[23]; // LEFT_HIP
            const knee = landmarks[25]; // LEFT_KNEE
            const ankle = landmarks[27]; // LEFT_ANKLE
            
            // Calculate Knee Angle
            const angle = calculateAngle(hip, knee, ankle);
            
            // Rep Counting Logic
            if (angle > 160) {
                if (stage === "up") {
                    reps += 1;
                    repCountEl.innerText = reps;
                    
                    // Simple animation effect
                    repCountEl.style.transform = 'scale(1.2)';
                    setTimeout(() => repCountEl.style.transform = 'scale(1)', 200);
                }
                stage = "down";
                phaseText.innerText = "Stand (Up)";
                
                feedbackStatus.style.background = "#22c55e"; // Green
                feedbackStatus.style.boxShadow = "0 0 10px #22c55e";
                feedbackText.innerText = "Good! Now go lower.";
                
            } else if (angle < 90) { // Below parallel
                stage = "up";
                phaseText.innerText = "Squat (Down)";
                
                feedbackStatus.style.background = "#3b82f6"; // Blue
                feedbackStatus.style.boxShadow = "0 0 10px #3b82f6";
                feedbackText.innerText = "Perfect Depth! Stand up.";
            } else {
                // In between
                if(stage === "down") {
                    feedbackStatus.style.background = "#f59e0b"; // Yellow
                    feedbackStatus.style.boxShadow = "0 0 10px #f59e0b";
                    feedbackText.innerText = "Lower... keep chest up";
                }
            }
        } else {
            feedbackStatus.style.background = "#ef4444"; // Red
            feedbackStatus.style.boxShadow = "0 0 10px #ef4444";
            feedbackText.innerText = "No body detected. Step back.";
        }
        
        canvasCtx.restore();
    }

    // Initialize MediaPipe Pose
    const pose = new Pose({locateFile: (file) => {
        return `https://cdn.jsdelivr.net/npm/@mediapipe/pose/${file}`;
    }});
    
    pose.setOptions({
        modelComplexity: 1,
        smoothLandmarks: true,
        enableSegmentation: false,
        minDetectionConfidence: 0.5,
        minTrackingConfidence: 0.5
    });
    
    pose.onResults(onResults);

    // Setup Camera
    camera = new Camera(videoElement, {
        onFrame: async () => {
            if (isRunning) {
                await pose.send({image: videoElement});
            }
        },
        width: 1280,
        height: 720
    });

    // Start Button
    startBtn.addEventListener('click', () => {
        isRunning = true;
        placeholder.style.display = 'none';
        startBtn.style.display = 'none';
        loader.style.display = 'flex';
        stopBtn.style.display = 'flex';
        camera.start();
    });

    // Stop Button
    stopBtn.addEventListener('click', () => {
        isRunning = false;
        camera.stop();
        
        canvasElement.style.display = 'none';
        feedbackOverlay.style.display = 'none';
        loader.style.display = 'none';
        stopBtn.style.display = 'none';
        
        placeholder.style.display = 'block';
        startBtn.style.display = 'flex';
        
        // Clear canvas
        canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
    });

    // Reset Counter
    resetBtn.addEventListener('click', () => {
        reps = 0;
        stage = "down";
        repCountEl.innerText = reps;
        phaseText.innerText = "Ready";
    });
});
</script>
@endsection
