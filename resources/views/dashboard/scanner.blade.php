@extends('layouts.app')

@section('content')

<div class="space-y-8">

    <div>

        <h1 class="text-4xl font-black">
            WISE Waste Scanner
        </h1>

        <p class="text-slate-500 mt-2">
            Use your webcam live to scan waste for fast identification.
        </p>

    </div>

    <div class="grid lg:grid-cols-2 gap-8">

        <!-- Scanner -->
        <div class="bg-white rounded-3xl border p-8">

            <div class="text-center space-y-6">

                <div class="relative rounded-3xl overflow-hidden border border-slate-200 bg-slate-900">

                    <video
                        id="cameraVideo"
                        class="hidden w-full h-[320px] object-cover"
                        autoplay
                        muted
                        playsinline>
                    </video>

                    <img
                        id="preview"
                        class="hidden w-full h-[320px] object-cover"
                        alt="Captured preview">

                    <div id="cameraFallback" class="flex items-center justify-center h-[320px] text-slate-400">
                        Live camera preview will appear here.
                    </div>

                </div>

                <canvas id="canvas" class="hidden"></canvas>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">

                    <button
                        id="startCamera"
                        type="button"
                        class="px-6 py-3 rounded-2xl bg-lime-500 text-white font-bold shadow-sm hover:bg-lime-600 transition">
                        Start Camera
                    </button>

                    <button
                        id="captureBtn"
                        type="button"
                        class="px-6 py-3 rounded-2xl bg-blue-500 text-white font-bold shadow-sm hover:bg-blue-600 transition hidden">
                        Capture Frame
                    </button>

                    <button
                        id="stopCamera"
                        type="button"
                        class="px-6 py-3 rounded-2xl bg-slate-200 text-slate-700 font-bold hidden">
                        Stop Camera
                    </button>

                    <label class="cursor-pointer bg-slate-100 border border-slate-200 px-6 py-3 rounded-2xl font-bold text-slate-700">
                        Upload Image
                        <input
                            type="file"
                            accept="image/*"
                            id="imageInput"
                            hidden>
                    </label>

                </div>

                <p id="cameraHint" class="text-sm text-slate-500">
                    Allow camera access to scan waste in real time.
                </p>

            </div>

        </div>

        <!-- Result -->
        <div class="bg-white rounded-3xl border p-8">

            <h3 class="text-2xl font-black mb-6">
                Classification Result
            </h3>

            <div class="space-y-6">

                <div>

                    <label class="text-sm text-slate-500">
                        Category
                    </label>

                    <h2 id="resultCategory" class="text-4xl font-black text-lime-600">
                        Waiting for scan
                    </h2>

                </div>

                <div>

                    <label class="text-sm text-slate-500">
                        Confidence
                    </label>

                    <div class="mt-2 bg-slate-200 rounded-full h-4">

                        <div
                            id="confidenceBar"
                            class="bg-lime-500 h-4 rounded-full"
                            style="width: 0%">
                        </div>

                    </div>

                    <p id="confidenceValue" class="mt-2 font-bold">
                        0%
                    </p>

                </div>

                <div class="bg-lime-50 p-6 rounded-2xl">

                    <h4 class="font-black text-lg">
                        Recycling Recommendation
                    </h4>

                    <ul id="recommendations" class="mt-3 space-y-2 text-slate-700">
                        <li>• Start a scan or upload an image first.</li>
                    </ul>

                </div>

                <button
                    class="w-full bg-lime-500 text-white py-4 rounded-2xl font-bold hover:bg-lime-600 transition">
                    Ask AI Assistant
                </button>

            </div>

        </div>

    </div>

</div>

<script>
const cameraVideo = document.getElementById('cameraVideo');
const preview = document.getElementById('preview');
const cameraFallback = document.getElementById('cameraFallback');
const canvas = document.getElementById('canvas');
const startCameraBtn = document.getElementById('startCamera');
const captureBtn = document.getElementById('captureBtn');
const stopCameraBtn = document.getElementById('stopCamera');
const imageInput = document.getElementById('imageInput');
const cameraHint = document.getElementById('cameraHint');
const resultCategory = document.getElementById('resultCategory');
const confidenceBar = document.getElementById('confidenceBar');
const confidenceValue = document.getElementById('confidenceValue');
const recommendations = document.getElementById('recommendations');

let cameraStream = null;

async function startCamera() {
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment'
            },
            audio: false
        });

        cameraVideo.srcObject = cameraStream;
        cameraVideo.classList.remove('hidden');
        cameraFallback.classList.add('hidden');
        captureBtn.classList.remove('hidden');
        stopCameraBtn.classList.remove('hidden');
        preview.classList.add('hidden');
        cameraHint.textContent = 'Point your camera at waste and tap Capture Frame.';
    } catch (error) {
        cameraHint.textContent = 'Camera access blocked or unavailable. Please upload an image instead.';
        console.error(error);
    }
}

function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }

    cameraVideo.classList.add('hidden');
    cameraFallback.classList.remove('hidden');
    captureBtn.classList.add('hidden');
    stopCameraBtn.classList.add('hidden');
}

function showPreview(imageSrc) {
    preview.src = imageSrc;
    preview.classList.remove('hidden');
    cameraVideo.classList.add('hidden');
    cameraFallback.classList.add('hidden');
    cameraHint.textContent = 'Image ready for classification.';
}

function updateResult(category, confidence, recommendationsText) {
    resultCategory.textContent = category;
    confidenceBar.style.width = `${confidence}%`;
    confidenceValue.textContent = `${confidence}%`;
    recommendations.innerHTML = recommendationsText.map(item => `<li>• ${item}</li>`).join('');
}

startCameraBtn?.addEventListener('click', startCamera);
stopCameraBtn?.addEventListener('click', stopCamera);

captureBtn?.addEventListener('click', () => {
    if (!cameraStream) {
        return;
    }

    const width = cameraVideo.videoWidth || 640;
    const height = cameraVideo.videoHeight || 480;

    canvas.width = width;
    canvas.height = height;

    const ctx = canvas.getContext('2d');
    ctx.drawImage(cameraVideo, 0, 0, width, height);

    const imageData = canvas.toDataURL('image/jpeg');
    showPreview(imageData);
    stopCamera();

    updateResult('Plastic Waste', 96, [
        'Clean bottle before recycling.',
        'Remove caps and rinse lightly.',
        'Place in designated plastic bin.'
    ]);
});

imageInput?.addEventListener('change', function (event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        showPreview(e.target.result);
        stopCamera();
        updateResult('Plastic Waste', 96, [
            'Clean bottle before recycling.',
            'Remove caps and rinse lightly.',
            'Place in designated plastic bin.'
        ]);
    };
    reader.readAsDataURL(file);
});
</script>

@endsection