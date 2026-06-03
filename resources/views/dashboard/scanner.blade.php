@extends('layouts.app')

@section('content')

<div class="space-y-8">

    <div>

        <h1 class="text-4xl font-black">
            AI Waste Scanner
        </h1>

        <p class="text-slate-500 mt-2">
            Upload waste image and let AI classify it instantly.
        </p>

    </div>

    <div class="grid lg:grid-cols-2 gap-8">

        <!-- Upload -->

        <div class="bg-white rounded-3xl border p-8">

        <div class="text-center">

            <img
                id="preview"
                class="hidden w-full max-h-96 object-contain rounded-3xl mb-6 border">

            <div class="flex flex-col md:flex-row gap-4 justify-center">

                <!-- Ambil dari galeri -->

                <label class="cursor-pointer bg-lime-500 text-white px-6 py-4 rounded-2xl font-bold">

                    📁 Upload Image

                    <input
                        type="file"
                        accept="image/*"
                        id="galleryInput"
                        hidden>

                </label>

                <!-- Kamera -->

                <label class="cursor-pointer bg-blue-500 text-white px-6 py-4 rounded-2xl font-bold">

                    📸 Open Camera

                    <input
                        type="file"
                        accept="image/*"
                        capture="environment"
                        id="cameraInput"
                        hidden>

                </label>

            </div>

            <p class="mt-5 text-slate-500">
                JPG, PNG, JPEG Supported
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

                    <h2 class="text-4xl font-black text-lime-600">
                        Plastic Waste
                    </h2>

                </div>

                <div>

                    <label class="text-sm text-slate-500">
                        Confidence
                    </label>

                    <div class="mt-2 bg-slate-200 rounded-full h-4">

                        <div
                            class="bg-lime-500 h-4 rounded-full"
                            style="width:96%">
                        </div>

                    </div>

                    <p class="mt-2 font-bold">
                        96.4%
                    </p>

                </div>

                <div class="bg-lime-50 p-6 rounded-2xl">

                    <h4 class="font-black text-lg">
                        ♻ Recycling Recommendation
                    </h4>

                    <ul class="mt-3 space-y-2">

                        <li>• Clean bottle before recycling.</li>
                        <li>• Remove cap if necessary.</li>
                        <li>• Place in plastic recycling bin.</li>

                    </ul>

                </div>

                <button
                    class="w-full bg-lime-500 text-white py-4 rounded-2xl font-bold">

                    Ask AI Assistant

                </button>

            </div>

        </div>

    </div>

</div>

<script>

const video = document.getElementById('video');
const preview = document.getElementById('preview');
const canvas = document.getElementById('canvas');

const startCamera =
    document.getElementById('startCamera');

const captureBtn =
    document.getElementById('captureBtn');

const fileInput =
    document.getElementById('fileInput');

let stream = null;

/*
|--------------------------------------------------------------------------
| START CAMERA
|--------------------------------------------------------------------------
*/

startCamera.addEventListener('click', async () => {

    try {

        stream =
        await navigator.mediaDevices.getUserMedia({

            video: {
                facingMode: "environment"
            },

            audio:false

        });

        video.srcObject = stream;

        video.classList.remove('hidden');

        captureBtn.classList.remove('hidden');

    }

    catch(error)
    {
        alert(
            'Camera tidak tersedia atau izin ditolak. Gunakan Upload Image.'
        );

        console.error(error);
    }

});

/*
|--------------------------------------------------------------------------
| CAPTURE IMAGE
|--------------------------------------------------------------------------
*/

captureBtn.addEventListener('click', () => {

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const ctx = canvas.getContext('2d');

    ctx.drawImage(
        video,
        0,
        0,
        canvas.width,
        canvas.height
    );

    const imageData =
        canvas.toDataURL('image/jpeg');

    preview.src = imageData;

    preview.classList.remove('hidden');

    stopCamera();

});

/*
|--------------------------------------------------------------------------
| UPLOAD IMAGE
|--------------------------------------------------------------------------
*/

fileInput.addEventListener('change', function(e){

    const file = e.target.files[0];

    if(!file) return;

    const reader = new FileReader();

    reader.onload = function(event){

        preview.src = event.target.result;

        preview.classList.remove('hidden');

    }

    reader.readAsDataURL(file);

});

/*
|--------------------------------------------------------------------------
| STOP CAMERA
|--------------------------------------------------------------------------
*/

function stopCamera()
{
    if(stream)
    {
        stream.getTracks().forEach(track => {

            track.stop();

        });

        video.classList.add('hidden');
    }
}

</script>

@endsection