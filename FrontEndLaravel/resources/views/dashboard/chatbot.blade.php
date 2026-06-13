@extends('layouts.app')

@section('content')

<div class="bg-white/80 backdrop-blur-sm rounded-3xl border h-[80vh] flex flex-col shadow-lg overflow-hidden">

    <div class="p-6 flex items-center gap-4 bg-gradient-to-r from-lime-100 to-white">

        <div class="w-14 h-14 rounded-2xl bg-lime-500 flex items-center justify-center text-white text-2xl shadow">🤖</div>

        <div>
            <h2 class="font-black text-xl">WISE AI Assistant</h2>
            <p class="text-sm text-slate-600">Fast, friendly tips on recycling and waste handling.</p>
        </div>

        <div class="ml-auto text-sm text-slate-500">Powered by Gemma + FastAPI</div>

    </div>

    <div class="flex-1 overflow-hidden p-6 bg-transparent flex flex-col">

        <div id="chatMessages" class="flex-1 overflow-y-auto space-y-4 pr-2">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-lime-200 flex items-center justify-center text-lime-700">AI</div>
                <div class="bg-white border rounded-2xl p-4 max-w-xl shadow-sm">Halo 👋 Silakan ketik pertanyaanmu tentang daur ulang atau pengelolaan sampah.</div>
            </div>
        </div>

        <div class="mt-4 border-t pt-4">
            <div class="flex gap-3 items-center">
                <input
                    id="chatInput"
                    type="text"
                    placeholder="Tanyakan tentang pemilahan, daur ulang, atau penyimpanan sampah..."
                    class="flex-1 border border-slate-200 rounded-full px-5 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-lime-300"
                >

                <button id="chatSendBtn" class="bg-lime-600 hover:bg-lime-700 text-white px-6 py-2 rounded-full shadow">Kirim</button>
            </div>

            <p id="chatStatus" class="mt-3 text-sm text-slate-500">Chatbot terhubung ke FastAPI.</p>
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
    const wrapper = document.createElement('div');
    wrapper.className = sender === 'user' ? 'flex items-start gap-3 justify-end' : 'flex items-start gap-3';

    const avatar = document.createElement('div');
    avatar.className = sender === 'user' ? 'w-10 h-10 rounded-full bg-lime-600 flex items-center justify-center text-white ml-2' : 'w-10 h-10 rounded-full bg-lime-200 flex items-center justify-center text-lime-700';
    avatar.textContent = sender === 'user' ? 'You' : 'AI';

    const bubble = document.createElement('div');
    bubble.className = sender === 'user'
        ? 'bg-lime-600 text-white rounded-2xl p-4 max-w-xl shadow ml-2'
        : 'bg-white border rounded-2xl p-4 max-w-xl shadow-sm';
    bubble.textContent = text;

    if (sender === 'user') {
        // user: bubble then avatar
        wrapper.appendChild(bubble);
        wrapper.appendChild(avatar);
    } else {
        wrapper.appendChild(avatar);
        wrapper.appendChild(bubble);
    }

    chatMessages.appendChild(wrapper);
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