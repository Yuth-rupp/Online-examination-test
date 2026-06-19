<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics Exam - Enter Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen flex flex-col justify-between text-slate-800 antialiased">

    <header class="bg-white border-b border-slate-200 px-8 py-3 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-indigo-600 rounded flex items-center justify-center text-white transform rotate-45">
                <span class="transform -rotate-45 font-bold text-sm">S</span>
            </div>
            <div>
                <h1 class="font-bold text-slate-900 text-sm md:text-base">Statistics Exam</h1>
                <p class="text-xs text-slate-500 font-medium tracking-wide uppercase">45 MIN • LIVE SESSION</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="font-bold text-slate-900 text-sm">{{ Auth::user()->full_name ?? 'Jane Doe' }}</p>
                <p class="text-xs text-slate-400 font-medium">Student ID: {{ Auth::user()->institutional_id ?? '88291' }}</p>
            </div>
            <div class="w-10 h-10 bg-amber-400 rounded-full border border-slate-200 overflow-hidden flex items-center justify-center font-bold text-amber-900 shadow-sm">
                {{ strtoupper(substr(Auth::user()->full_name ?? 'JD', 0, 2)) }}
            </div>
        </div>
    </header>

    <main class="flex-grow flex flex-col items-center justify-center p-6 my-4">
        
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-bold uppercase tracking-wider border border-emerald-200 mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live Now
            </span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">Enter Exam Room</h2>
            <p class="text-slate-500 mt-2 text-sm md:text-base">Identity and environment verification required to proceed.</p>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl max-w-5xl w-full p-8 md:p-10 grid grid-cols-1 md:grid-cols-2 gap-10">
            
            <div class="flex flex-col justify-between space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 mb-6">Exam Guidelines</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-4 p-4 bg-slate-50/70 rounded-xl border border-slate-100">
                            <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-900">No tab switching</h4>
                                <p class="text-xs text-slate-500 mt-0.5">System detects activity automatically.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-4 bg-slate-50/70 rounded-xl border border-slate-100">
                            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-camera text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-900">Keep camera on</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Feed must be visible at all times.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-4 bg-slate-50/70 rounded-xl border border-slate-100">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-shield-halved text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-900">Secure browser active</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Locked to this secure session.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <label for="agreement" class="flex items-start gap-4 p-5 bg-indigo-50/40 border border-indigo-100 rounded-2xl cursor-pointer hover:bg-indigo-50/80 transition-all select-none">
                    <input type="checkbox" id="agreement" class="w-5 h-5 mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 cursor-pointer transition">
                    <span class="text-xs md:text-sm font-medium text-slate-700 leading-relaxed">
                        I agree to the <a href="#" class="text-indigo-600 underline font-semibold">exam rules</a> and understand that non-compliance leads to immediate disqualification.
                    </span>
                </label>
            </div>

            <div class="space-y-6">
                <h3 class="text-xl font-bold text-slate-900">System Verification</h3>
                
                <div class="relative w-full h-48 md:h-52 bg-slate-900 rounded-2xl overflow-hidden shadow-inner flex items-center justify-center group border border-slate-800">
                    <video id="webcamPreview" autoplay playsinline muted class="w-full h-full object-cover hidden"></video>
                    
                    <div id="videoOverlay" class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center text-center p-4 transition-all duration-300">
                        <button id="enableCamBtn" class="w-14 h-14 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center mb-3 transition shadow-lg group-hover:scale-105 active:scale-95">
                            <i id="overlayCamIcon" class="fa-solid fa-video-slash text-xl text-rose-400"></i>
                        </button>
                        <span id="videoStatusText" class="text-xs font-bold text-slate-300 tracking-wider uppercase">Camera Required</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between p-4 bg-white border border-slate-100 shadow-sm rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-camera text-slate-400"></i>
                            <span class="font-bold text-xs uppercase tracking-wider text-slate-600">Camera</span>
                        </div>
                        <div id="cameraBadge" class="flex items-center gap-3">
                            <span class="text-xs font-bold text-rose-500 uppercase tracking-wider">Turned Off</span>
                            <button id="toggleCamBtn" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition-all">Turn On</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-white border border-slate-100 shadow-sm rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-microphone text-slate-400"></i>
                            <span class="font-bold text-xs uppercase tracking-wider text-slate-600">Microphone</span>
                        </div>
                        <div id="micBadge" class="flex items-center gap-3">
                            <span class="text-xs font-bold text-rose-500 uppercase tracking-wider">Turned Off</span>
                            <button id="toggleMicBtn" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition-all">Turn On</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-white border border-slate-100 shadow-sm rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-wifi text-slate-400"></i>
                            <span class="font-bold text-xs uppercase tracking-wider text-slate-600">Internet</span>
                        </div>
                        <span id="internetBadge" class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                            Checking...
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full max-w-5xl mt-8 text-center">
            <button id="startExamBtn" disabled class="w-full bg-slate-300 text-slate-500 font-bold py-4 px-6 rounded-2xl flex items-center justify-center gap-2 cursor-not-allowed transition-all duration-200 text-base shadow-sm">
                START EXAM <i class="fa-solid fa-arrow-right"></i>
            </button>
            <p class="text-[10px] tracking-widest text-slate-400 uppercase font-mono mt-4">Session ID: STATS-2026-88A2-LDN</p>
        </div>

    </main>

    <footer class="border-t border-slate-200 bg-white/50 px-8 py-4 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-4">
        <a href="{{ route('student.dashboard') }}" class="hover:text-slate-800 font-medium transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Return to Dashboard
        </a>
        <div class="flex flex-wrap justify-center gap-6 font-medium text-[11px] tracking-wide text-slate-400 uppercase">
            <span><i class="fa-solid fa-lock text-indigo-500 mr-1"></i> End-To-End Encrypted</span>
            <span><i class="fa-solid fa-headset text-indigo-500 mr-1"></i> 24/7 Support</span>
            <span><i class="fa-solid fa-cloud-arrow-up text-indigo-500 mr-1"></i> Progress Auto-Save</span>
        </div>
    </footer>

    <script>
        // State management monitoring flags
        let localStream = null;
        let isCamReady = false;
        let isMicReady = false;

        // Elements tracking configurations mapping
        const webcamPreview = document.getElementById('webcamPreview');
        const videoOverlay = document.getElementById('videoOverlay');
        const overlayCamIcon = document.getElementById('overlayCamIcon');
        const videoStatusText = document.getElementById('videoStatusText');
        
        const cameraBadge = document.getElementById('cameraBadge');
        const toggleCamBtn = document.getElementById('toggleCamBtn');
        const enableCamBtn = document.getElementById('enableCamBtn');

        const micBadge = document.getElementById('micBadge');
        const toggleMicBtn = document.getElementById('toggleMicBtn');
        
        const internetBadge = document.getElementById('internetBadge');
        const agreement = document.getElementById('agreement');
        const startExamBtn = document.getElementById('startExamBtn');

        // Dynamic Native Hardware Access Stream Initializer Subsystem
        async function requestHardware(requestCam = true, requestMic = true) {
            try {
                const constraints = {
                    video: requestCam,
                    audio: requestMic
                };
                
                const stream = await navigator.mediaDevices.getUserMedia(constraints);
                
                if (requestCam) {
                    webcamPreview.srcObject = stream;
                    webcamPreview.classList.remove('hidden');
                    videoOverlay.classList.add('bg-slate-900/10'); // Alpha transparency backdrop when feed goes live
                    overlayCamIcon.className = 'fa-solid fa-video text-white';
                    videoStatusText.innerText = 'CAMERA READY';
                    videoStatusText.className = "text-xs font-bold text-white uppercase drop-shadow-md tracking-wider";
                    
                    cameraBadge.innerHTML = `<span class="text-xs font-bold text-emerald-500 uppercase tracking-wider flex items-center gap-1"><i class="fa-solid fa-circle-check"></i> Ready</span>`;
                    isCamReady = true;
                }

                if (requestMic) {
                    micBadge.innerHTML = `<span class="text-xs font-bold text-emerald-500 uppercase tracking-wider flex items-center gap-1"><i class="fa-solid fa-circle-check"></i> Ready</span>`;
                    isMicReady = true;
                }

                localStream = stream;
                validateFormState();
            } catch (err) {
                console.warn('System Access Device Exception Encountered:', err);
                alert('Hardware permission request was dismissed or blocked. Please reset your browser site settings locks and allow hardware device triggers.');
            }
        }

        // Live Active Network Latency Diagnostics Tracker Function
        function updateNetworkStatus() {
            if (!navigator.onLine) {
                setNetworkBadge('Disconnected', 'text-rose-500');
                return;
            }

            const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            if (connection) {
                const rtt = connection.rtt; // Evaluation Round Trip Delay Latency
                const downlink = connection.downlink; // Network stream connection width bandwidth speed profile context parameter
                
                if (rtt > 350 || downlink < 1.5) {
                    setNetworkBadge('Poor', 'text-amber-500');
                } else if (rtt > 150 || downlink < 4) {
                    setNetworkBadge('Good', 'text-blue-500');
                } else {
                    setNetworkBadge('Strong', 'text-emerald-500');
                }
            } else {
                // Connection API structural fallback configuration criteria
                setNetworkBadge('Connected', 'text-emerald-500');
            }
            validateFormState();
        }

        function setNetworkBadge(statusText, colorClass) {
            internetBadge.className = `text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 ${colorClass}`;
            let icon = 'fa-wifi';
            if (statusText === 'Disconnected') icon = 'fa-circle-xmark';
            internetBadge.innerHTML = `<i class="fa-solid ${icon}"></i> ${statusText}`;
        }

        // Form Validation Rules Check State Monitor
        function validateFormState() {
            const isInternetOk = !internetBadge.classList.contains('text-rose-500') && internetBadge.innerText !== 'CHECKING...';
            
            if (isCamReady && isMicReady && isInternetOk && agreement.checked) {
                startExamBtn.removeAttribute('disabled');
                startExamBtn.className = "w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-2xl flex items-center justify-center gap-2 cursor-pointer transition transform duration-150 active:scale-[0.99] text-base shadow-md shadow-indigo-200";
            } else {
                startExamBtn.setAttribute('disabled', 'true');
                startExamBtn.className = "w-full bg-slate-300 text-slate-500 font-bold py-4 px-6 rounded-2xl flex items-center justify-center gap-2 cursor-not-allowed transition duration-200 text-base shadow-sm";
            }
        }

        // Click Bindings Actions Registers Mapping
        toggleCamBtn.addEventListener('click', () => requestHardware(true, false));
        enableCamBtn.addEventListener('click', () => requestHardware(true, false));
        toggleMicBtn.addEventListener('click', () => requestHardware(false, true));
        agreement.addEventListener('change', validateFormState);

        startExamBtn.addEventListener('click', () => {
            if (!startExamBtn.hasAttribute('disabled')) {
                alert('Verification complete! Launching standard secure examination platform framework instances...');
            }
        });

        // Event listener hooks tracking active network changes cycles
        window.addEventListener('online', updateNetworkStatus);
        window.addEventListener('offline', updateNetworkStatus);
        
        // Root system init loop run tracking execution step
        updateNetworkStatus();
    </script>
</body>
</html>