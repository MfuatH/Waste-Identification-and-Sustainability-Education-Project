@extends('layouts.app')

@section('content')

<div class="space-y-8">

    @if(session('success'))
        <div class="rounded-3xl border border-lime-200 bg-lime-50 p-4 text-lime-800">
            {{ session('success') }}
        </div>
    @endif

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

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mt-4">
                    <p id="cameraHint" class="text-sm text-slate-500">
                        Allow camera access to scan waste in real time.
                    </p>

                    <button
                        id="saveScanBtn"
                        type="button"
                        class="px-6 py-3 rounded-2xl bg-emerald-500 text-white font-bold shadow-sm hover:bg-emerald-600 transition hidden">
                        Simpan Scan
                    </button>
                </div>

                <p id="saveStatus" class="text-sm text-slate-500 mt-2 hidden"></p>

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
                        Result
                    </label>

                    <h2 id="resultCategory" class="text-4xl font-black text-lime-600">
                        Waiting for scan
                    </h2>

                    <p id="resultCategoryType" class="text-sm text-slate-500 mt-1">
                        Pilih gambar untuk memulai.
                    </p>

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
                    <div
    id="youtubeSection"
    class="bg-red-50 p-6 rounded-2xl hidden">

    <h4 class="font-black text-lg">
        Tutorial Pengolahan Sampah
    </h4>

    <p
        id="youtubeTitle"
        class="mt-2 text-slate-700">
    </p>

    <a
        id="youtubeLink"
        href="#"
        target="_blank"
        class="inline-block mt-4 px-5 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition">

        🎥 Tonton Tutorial di YouTube

    </a>

</div>

                    <h4 class="font-black text-lg">
                        Recycling Recommendation
                    </h4>

                    <ul id="recommendations" class="mt-3 space-y-2 text-slate-700">
                        <li>• Start a scan or upload an image first.</li>
                    </ul>

                </div>

                <button
                    id="askAssistantBtn"
                    class="w-full bg-lime-500 text-white py-4 rounded-2xl font-bold hover:bg-lime-600 transition">
                    Ask AI Assistant
                </button>

            </div>

        </div>

    </div>

</div>

<script>
const FASTAPI_URL = "{{ env('FASTAPI_URL', 'http://127.0.0.1:8000') }}".replace(/\/+$/g, '');
const PREDICT_ENDPOINT = `${FASTAPI_URL}/predict`;
const CHAT_PAGE_URL = "{{ route('chatbot') }}";
const cameraVideo = document.getElementById('cameraVideo');
const preview = document.getElementById('preview');
const cameraFallback = document.getElementById('cameraFallback');
const canvas = document.getElementById('canvas');
const startCameraBtn = document.getElementById('startCamera');
const captureBtn = document.getElementById('captureBtn');
const stopCameraBtn = document.getElementById('stopCamera');
const imageInput = document.getElementById('imageInput');
const askAssistantBtn = document.getElementById('askAssistantBtn');
const cameraHint = document.getElementById('cameraHint');
const saveScanBtn = document.getElementById('saveScanBtn');
const saveStatus = document.getElementById('saveStatus');
const resultCategory = document.getElementById('resultCategory');
const resultCategoryType = document.getElementById('resultCategoryType');
const confidenceBar = document.getElementById('confidenceBar');
const confidenceValue = document.getElementById('confidenceValue');
const recommendations = document.getElementById('recommendations');

let cameraStream = null;
let currentImageFile = null;
let currentCategoryKey = null;
let currentPredictedClass = null;
let currentRecommendations = [
    'Start a scan or upload an image first.'
];

const classLabels = {
    accu: 'Accu',
    battery: 'Baterai',
    botol_kaca: 'Botol Kaca',
    botol_plastik: 'Botol Plastik',
    buah_sayur: 'Buah & Sayur',
    bungkus_plastik_makanan: 'Bungkus Plastik Makanan',
    cup_plastik: 'Cup Plastik',
    kaleng_minuman: 'Kaleng Minuman',
    kardus: 'Kardus',
    kertas: 'Kertas',
    pakaian: 'Pakaian',
    sepatu: 'Sepatu',
    sisa_makanan: 'Sisa Makanan'
};

const categoryNames = {
    organik: 'Organik',
    anorganik: 'Anorganik',
    'e-waste': 'Limbah Elektronik'
};

const categoryRecommendations = {
    organik: [
        'Pisahkan sampah organik dari sampah lainnya.',
        'Taruh pada tempat kompos atau tempat sampah organik.',
        'Gunakan sampah organik untuk kompos atau pupuk tanaman.'
    ],
    anorganik: [
        'Bersihkan sampah anorganik sebelum dibuang.',
        'Masukkan ke dalam tempat sampah daur ulang yang sesuai.',
        'Pisahkan plastik, kaca, kertas, dan logam.'
    ],
    'e-waste': [
        'Kumpulkan limbah elektronik di tempat khusus e-waste.',
        'Jangan buang bersama sampah biasa.',
        'Lepaskan baterai sebelum dibuang jika memungkinkan.'
    ]
};

const youtubeVideos = {
    organik: {
        title: "Tutorial Membuat Kompos dari Sampah Organik",
        url: "https://youtu.be/0qfGNQ499JA?si=t3Epxd-PAPQK6duC"
    },

    anorganik: {
        title: "Daur Ulang Sampah Anorganik: Plastik, Kertas, Kaca, dan Logam",
        url: "https://youtu.be/oVOgpI_V8OY?si=ptppcvAtgp1ir4lY"
    },

    'e-waste': {
        title: "Pengelolaan Limbah Elektronik yang Benar",
        url: "https://youtube.com/playlist?list=PLHWBteJlBKUqp-qxOU_eOvFg7qILUjGbm&si=5E9k5LsarX2L2Han"
    }
};


function showPreview(imageSrc) {
    preview.src = imageSrc;
    preview.classList.remove('hidden');
    cameraVideo.classList.add('hidden');
    cameraFallback.classList.add('hidden');
    cameraHint.textContent = 'Image ready for classification.';
    saveScanBtn.classList.remove('hidden');
}

function getReadableLabel(classKey) {
    return classLabels[classKey] || classKey.replace(/_/g, ' ');
}

function getRecommendations(category, predictedClass) {
    const categoryKey = category || 'anorganik';
    const baseRecommendations = categoryRecommendations[categoryKey] || categoryRecommendations.anorganik;
    const extra = predictedClass ? [`Pastikan ${getReadableLabel(predictedClass)} dibuang dengan benar sesuai kategori.`] : [];
    return [...extra, ...baseRecommendations];
}

function updateYoutubeTutorial(category) {

    const youtubeSection =
        document.getElementById('youtubeSection');

    const youtubeTitle =
        document.getElementById('youtubeTitle');

    const youtubeLink =
        document.getElementById('youtubeLink');

    if (youtubeVideos[category]) {

        youtubeTitle.textContent =
            youtubeVideos[category].title;

        youtubeLink.href =
            youtubeVideos[category].url;

        youtubeSection.classList.remove('hidden');

    } else {

        youtubeSection.classList.add('hidden');

    }
}

function updateResult(label, categoryKey, confidence, recommendationsText) {
    currentCategoryKey = categoryKey || 'anorganik';
    currentPredictedClass = label;

    resultCategory.textContent = label;
    resultCategoryType.textContent = categoryNames[categoryKey] || categoryNames.anorganik;

    confidenceBar.style.width = `${confidence}%`;
    confidenceValue.textContent = `${confidence}%`;

    currentRecommendations = recommendationsText;
    recommendations.innerHTML = recommendationsText
        .map(item => `<li>• ${item}</li>`)
        .join('');

    updateYoutubeTutorial(categoryKey);
    saveScanBtn.classList.remove('hidden');
}

function setStatus(message, isError = false) {
    saveStatus.textContent = message;
    saveStatus.classList.remove('text-slate-500', 'text-green-600', 'text-red-600');
    saveStatus.classList.add(isError ? 'text-red-600' : 'text-green-600');
    saveStatus.classList.remove('hidden');
}

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

function stopCamera(showFallback = true) {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }

    cameraVideo.classList.add('hidden');
    captureBtn.classList.add('hidden');
    stopCameraBtn.classList.add('hidden');

    if (showFallback && preview.classList.contains('hidden')) {
        cameraFallback.classList.remove('hidden');
    } else {
        cameraFallback.classList.add('hidden');
    }
}

async function saveScan() {
    if (!currentImageFile) {
        setStatus('Pilih gambar atau ambil foto dulu sebelum menyimpan.', true);
        return;
    }

    const category = currentCategoryKey || 'anorganik';
    const confidence = parseFloat(confidenceValue.textContent.replace('%', '')) || 0;
    const recommendationText = currentRecommendations.join('\n');

    const formData = new FormData();
    formData.append('image', currentImageFile);
    formData.append('category', category);
    formData.append('confidence', confidence);
    formData.append('recommendation', recommendationText);

    saveScanBtn.disabled = true;
    saveScanBtn.textContent = 'Menyimpan...';

    try {
        const response = await fetch("{{ route('scanner.upload') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData,
        });

        const contentType = response.headers.get('content-type') || '';
        const result = contentType.includes('application/json')
            ? await response.json()
            : { success: false, message: 'Server merespon dengan tipe yang tidak diharapkan.' };

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Terjadi kesalahan saat menyimpan scan.');
        }

        setStatus('Scan berhasil disimpan ke database.');
        currentImageFile = null;
        saveScanBtn.textContent = 'Simpan Scan';
        saveScanBtn.disabled = false;
    } catch (error) {
        setStatus(error.message, true);
        saveScanBtn.textContent = 'Simpan Scan';
        saveScanBtn.disabled = false;
        console.error(error);
    }
}

async function classifyImage(file) {
    if (!file) {
        setStatus('File tidak ditemukan.', true);
        return;
    }

    setStatus('Menganalisis gambar, tunggu sebentar...');

    const formData = new FormData();
    formData.append('file', file);

    try {
        const response = await fetch(PREDICT_ENDPOINT, {
            method: 'POST',
            body: formData,
        });

        const data = await response.json();

        if (!response.ok || !data.predicted_class) {
            throw new Error(data.detail || data.error || 'Prediksi gagal.');
        }

        const classLabel = getReadableLabel(data.predicted_class);
        const categoryKey = data.category || 'anorganik';
        const categoryLabel = categoryNames[categoryKey] || categoryNames.anorganik;
        const confidencePercent = Math.round((data.confidence || 0) * 10000) / 100;
        const recommendationList = getRecommendations(categoryKey, data.predicted_class);

        updateResult(classLabel, categoryKey, confidencePercent, recommendationList);
        setStatus(`Prediksi selesai: ${classLabel} (${categoryLabel}).`);
        currentImageFile = file;
    } catch (error) {
        setStatus(error.message || 'Terjadi kesalahan saat memprediksi gambar.', true);
        console.error(error);
    }
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

    canvas.toBlob(async blob => {
        if (!blob) {
            setStatus('Tidak dapat menangkap gambar.', true);
            return;
        }

        const file = new File([blob], 'scan.jpg', { type: 'image/jpeg' });
        currentImageFile = file;
        showPreview(URL.createObjectURL(blob));
        stopCamera(false);
        await classifyImage(file);
    }, 'image/jpeg');
});

imageInput?.addEventListener('change', function (event) {
    const file = event.target.files[0];
    if (!file) return;

    currentImageFile = file;

    const reader = new FileReader();
    reader.onload = async function (e) {
        showPreview(e.target.result);
        stopCamera(false);
        await classifyImage(file);
    };
    reader.readAsDataURL(file);
});

askAssistantBtn?.addEventListener('click', () => {
    window.location.href = CHAT_PAGE_URL;
});

saveScanBtn?.addEventListener('click', saveScan);
</script>

@endsection
