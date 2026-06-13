@extends('layouts.app')

@section('content')

<div class="bg-white rounded-3xl border h-[80vh] flex flex-col">

    <div class="border-b p-6 flex items-center gap-4">

        <div class="w-14 h-14 rounded-2xl bg-lime-500 flex items-center justify-center text-white text-2xl">
            🤖
        </div>

        <div>

            <h2 class="font-black text-xl">
                WISE AI Assistant
            </h2>

            <p class="text-sm text-slate-500">
                Powered by Gemma + FastAPI
            </p>

        </div>

    </div>

    <div class="flex-1 overflow-hidden p-8 bg-slate-50 flex flex-col">

        <div id="chatMessages" class="flex-1 overflow-y-auto space-y-4 pr-2">
            <div class="bg-white border rounded-2xl p-4 max-w-xl">
                Halo 👋 Silakan ketik pertanyaanmu tentang daur ulang atau pengelolaan sampah.
            </div>
        </div>

        <div class="mt-4 border-t pt-4">
            <div class="flex gap-3">
                <input
                    id="chatInput"
                    type="text"
                    placeholder="Ask about recycling..."
                    class="flex-1 border rounded-2xl px-5 py-3"
                >

                <button
                    id="chatSendBtn"
                    class="bg-lime-500 text-white px-8 rounded-2xl">
                    Send
                </button>
            </div>

            <p id="chatStatus" class="mt-3 text-sm text-slate-500">
                Chatbot terhubung ke FastAPI.
            </p>
        </div>

    </div>

</div>

<script>
const FASTAPI_URL = "{{ env('FASTAPI_URL', 'http://127.0.0.1:8000') }}";
const CHAT_ENDPOINT = `${FASTAPI_URL}/chat`;
const chatMessages = document.getElementById('chatMessages');
const chatInput = document.getElementById('chatInput');
const chatSendBtn = document.getElementById('chatSendBtn');
const chatStatus = document.getElementById('chatStatus');

function addChatBubble(text, sender) {
    const bubble = document.createElement('div');
    bubble.className = sender === 'user'
        ? 'bg-lime-500 text-white rounded-2xl p-4 ml-auto max-w-xl'
        : 'bg-white border rounded-2xl p-4 max-w-xl';
    bubble.textContent = text;
    chatMessages.appendChild(bubble);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

async function sendChat() {
    const message = chatInput.value.trim();
    if (!message) return;

    addChatBubble(message, 'user');
    chatInput.value = '';
    chatStatus.textContent = 'Mengirim ke FastAPI...';

    try {
        const response = await fetch(CHAT_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ message })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.detail || 'FastAPI chat endpoint error');
        }

        addChatBubble(data.reply || 'Tidak ada balasan.', 'bot');
        chatStatus.textContent = 'Balasan diterima.';
    } catch (error) {
        addChatBubble('Gagal menghubungi chatbot. Periksa FastAPI.', 'bot');
        chatStatus.textContent = error.message;
        console.error(error);
    }
}

chatSendBtn?.addEventListener('click', sendChat);
chatInput?.addEventListener('keypress', event => {
    if (event.key === 'Enter') {
        event.preventDefault();
        sendChat();
    }
});
</script>

@endsection