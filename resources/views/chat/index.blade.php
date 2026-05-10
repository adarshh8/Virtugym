@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div style="max-width:1280px;margin:0 auto;">

    <h1 style="font-size:1.6rem;font-weight:900;background:linear-gradient(135deg,#fff 20%,#c4b5fd);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:1.5rem;" class="fade-in-up">
        💬 Messages
    </h1>

    <div class="fade-in-up" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:24px;overflow:hidden;height:620px;display:grid;grid-template-columns:280px 1fr;">

        {{-- Conversation List --}}
        <div style="border-right:1px solid rgba(139,92,246,.15);display:flex;flex-direction:column;">
            <div style="padding:1.1rem 1.2rem;border-bottom:1px solid rgba(139,92,246,.12);background:rgba(139,92,246,.06);">
                <h2 style="font-size:.88rem;font-weight:700;color:#c4b5fd;letter-spacing:.04em;">CONVERSATIONS</h2>
            </div>
            <div style="overflow-y:auto;flex:1;">
                @php
                    use App\Models\Message;
                    use App\Models\User;
                    $uniqueUsers = [];
                    $convList = [];
                    if(isset($conversations)) {
                        foreach($conversations as $conv) {
                            $otherId = ($conv->sender_id == Auth::id()) ? $conv->receiver_id : $conv->sender_id;
                            if(!in_array($otherId, $uniqueUsers)) {
                                $uniqueUsers[] = $otherId;
                                $otherUser = User::find($otherId);
                                if($otherUser) {
                                    $unread = Message::where('receiver_id', Auth::id())
                                        ->where('sender_id', $otherId)
                                        ->where('is_read', false)
                                        ->count();
                                    $convList[] = ['user' => $otherUser, 'last_message' => $conv->message, 'unread' => $unread];
                                }
                            }
                        }
                    }
                @endphp

                @if(count($convList) > 0)
                    @foreach($convList as $item)
                        @php $isActive = isset($selectedTrainer) && $selectedTrainer && $selectedTrainer->id == $item['user']->id; @endphp
                        <a href="{{ url('/chat/' . $item['user']->id) }}"
                           style="display:block;padding:14px 16px;border-bottom:1px solid rgba(139,92,246,.08);text-decoration:none;transition:background .2s;{{ $isActive ? 'background:rgba(139,92,246,.15);' : '' }}"
                           onmouseover="this.style.background='rgba(139,92,246,.1)'"
                           onmouseout="this.style.background='{{ $isActive ? 'rgba(139,92,246,.15)' : 'transparent' }}'">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#8b5cf6,#ec4899);display:flex;align-items:center;justify-content:center;font-size:.9rem;font-weight:800;color:#fff;flex-shrink:0;">
                                    {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <p style="font-size:.85rem;font-weight:600;color:#e2d9f3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item['user']->name }}</p>
                                    <p style="font-size:.73rem;color:rgba(255,255,255,.3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:1px;">{{ Str::limit($item['last_message'], 35) }}</p>
                                </div>
                                @if($item['unread'] > 0)
                                    <span style="background:#ef4444;color:#fff;font-size:.65rem;padding:2px 7px;border-radius:50px;font-weight:700;flex-shrink:0;">{{ $item['unread'] }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                @else
                    <div style="text-align:center;padding:3rem 1rem;">
                        <div style="font-size:2rem;opacity:.3;margin-bottom:.5rem;">💬</div>
                        <p style="color:rgba(255,255,255,.25);font-size:.82rem;">No conversations yet</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Chat Area --}}
        <div style="display:flex;flex-direction:column;height:100%;overflow:hidden;">
            @if(isset($selectedTrainer) && $selectedTrainer)
                {{-- Chat Header --}}
                <div style="padding:14px 20px;border-bottom:1px solid rgba(139,92,246,.12);background:rgba(139,92,246,.06);display:flex;align-items:center;gap:12px;">
                    <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#8b5cf6,#ec4899);display:flex;align-items:center;justify-content:center;font-size:.9rem;font-weight:800;color:#fff;flex-shrink:0;">
                        {{ strtoupper(substr($selectedTrainer->name, 0, 1)) }}
                    </div>
                    <div>
                        <p style="font-size:.9rem;font-weight:700;color:#e2d9f3;">{{ $selectedTrainer->name }}</p>
                        <p style="font-size:.72rem;color:rgba(255,255,255,.3);">{{ $selectedTrainer->specialization ?? ucfirst($selectedTrainer->role ?? 'Trainer') }}</p>
                    </div>
                    <div style="margin-left:auto;display:flex;align-items:center;gap:6px;">
                        <span style="width:8px;height:8px;border-radius:50%;background:#10b981;display:inline-block;"></span>
                        <span style="font-size:.73rem;color:rgba(255,255,255,.3);">Online</span>
                    </div>
                </div>

                {{-- Messages --}}
                <div id="chatMessages" style="flex:1;overflow-y:auto;padding:1.2rem;display:flex;flex-direction:column;gap:.7rem;background:rgba(0,0,0,.15);">
                    <div style="text-align:center;color:rgba(255,255,255,.2);font-size:.8rem;">Loading messages…</div>
                </div>

                {{-- Input --}}
                <div style="padding:14px 16px;border-top:1px solid rgba(139,92,246,.12);background:rgba(8,8,26,.6);">
                    <form id="chatForm" style="display:flex;gap:.6rem;align-items:center;" onsubmit="return false;">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $selectedTrainer->id }}" id="receiverId">
                        <input type="text" name="message" id="messageInput"
                               style="flex:1;background:rgba(255,255,255,.06);border:1px solid rgba(139,92,246,.25);border-radius:12px;padding:11px 16px;color:#fff;font-size:.88rem;outline:none;transition:border-color .2s;"
                               placeholder="Type your message…" autocomplete="off"
                               onfocus="this.style.borderColor='rgba(139,92,246,.6)'"
                               onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                        <button type="button" id="sendButton"
                                style="background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;border:none;border-radius:12px;padding:11px 22px;font-size:.88rem;font-weight:700;cursor:pointer;box-shadow:0 6px 16px rgba(139,92,246,.35);transition:all .2s;"
                                onmouseover="this.style.boxShadow='0 10px 24px rgba(139,92,246,.55)';this.style.transform='translateY(-1px)'"
                                onmouseout="this.style.boxShadow='0 6px 16px rgba(139,92,246,.35)';this.style.transform=''">
                            Send ➤
                        </button>
                    </form>
                </div>
            @else
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1rem;">
                    <div style="font-size:3rem;opacity:.25;">💬</div>
                    <p style="color:rgba(255,255,255,.25);font-size:.9rem;">Select a conversation to start chatting</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Custom scrollbar inside chat */
    #chatMessages::-webkit-scrollbar { width: 4px; }
    #chatMessages::-webkit-scrollbar-thumb { background: rgba(139,92,246,.4); border-radius: 4px; }
</style>

@if(isset($selectedTrainer) && $selectedTrainer)
<script>
    const trainerId = '{{ $selectedTrainer->id }}';
    const currentUserId = '{{ Auth::id() }}';
    const chatMessagesDiv = document.getElementById('chatMessages');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');

    let lastMessageId = null;
    let firstLoad = true;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function buildMessageEl(msg) {
        const isOwn = msg.sender_id == currentUserId;
        const date = new Date(msg.created_at);
        const timeStr = date.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
        const wrapper = document.createElement('div');
        wrapper.dataset.msgId = msg.id;
        wrapper.style.cssText = 'display:flex;' + (isOwn ? 'justify-content:flex-end;' : 'justify-content:flex-start;');

        if (isOwn) {
            wrapper.innerHTML = `
                <div style="max-width:65%;background:linear-gradient(135deg,#8b5cf6,#7c3aed);border-radius:18px 18px 4px 18px;padding:10px 14px;box-shadow:0 4px 12px rgba(139,92,246,.3);">
                    <p style="font-size:.85rem;color:#fff;word-break:break-word;">${escapeHtml(msg.message)}</p>
                    <p style="font-size:.65rem;color:rgba(255,255,255,.5);margin-top:4px;text-align:right;">${timeStr}</p>
                </div>`;
        } else {
            wrapper.innerHTML = `
                <div style="max-width:65%;background:rgba(255,255,255,.07);border:1px solid rgba(139,92,246,.2);border-radius:18px 18px 18px 4px;padding:10px 14px;">
                    <p style="font-size:.85rem;color:#e2d9f3;word-break:break-word;">${escapeHtml(msg.message)}</p>
                    <p style="font-size:.65rem;color:rgba(255,255,255,.3);margin-top:4px;">${timeStr}</p>
                </div>`;
        }
        return wrapper;
    }

    function loadMessages() {
        fetch('/chat/messages/' + trainerId)
            .then(r => r.json())
            .then(messages => {
                if (!chatMessagesDiv) return;

                if (messages.length === 0) {
                    if (firstLoad) {
                        chatMessagesDiv.innerHTML = '<div style="text-align:center;color:rgba(255,255,255,.2);font-size:.82rem;padding:2rem;">No messages yet. Start the conversation! 👋</div>';
                        firstLoad = false;
                    }
                    return;
                }

                // On first load, render all messages
                if (firstLoad) {
                    chatMessagesDiv.innerHTML = '';
                    messages.forEach(msg => chatMessagesDiv.appendChild(buildMessageEl(msg)));
                    lastMessageId = messages[messages.length - 1].id;
                    chatMessagesDiv.scrollTop = chatMessagesDiv.scrollHeight;
                    firstLoad = false;
                    return;
                }

                // On subsequent polls, only append messages newer than lastMessageId
                const newMessages = messages.filter(msg => msg.id > lastMessageId);
                if (newMessages.length > 0) {
                    // Remove "no messages" placeholder if present
                    const placeholder = chatMessagesDiv.querySelector('[data-placeholder]');
                    if (placeholder) placeholder.remove();

                    newMessages.forEach(msg => chatMessagesDiv.appendChild(buildMessageEl(msg)));
                    lastMessageId = newMessages[newMessages.length - 1].id;
                    chatMessagesDiv.scrollTop = chatMessagesDiv.scrollHeight;
                }
            })
            .catch(e => console.error('Error loading messages:', e));
    }

    function sendMessage() {
        const message = messageInput.value.trim();
        if (!message) return;
        const receiverId = document.getElementById('receiverId').value;

        fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ receiver_id: receiverId, message })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) { messageInput.value = ''; loadMessages(); }
        })
        .catch(e => console.error('Error sending message:', e));
    }

    sendButton?.addEventListener('click', sendMessage);
    messageInput?.addEventListener('keypress', e => { if(e.key === 'Enter') { e.preventDefault(); sendMessage(); } });

    loadMessages();
    const interval = setInterval(loadMessages, 3000);
    window.addEventListener('beforeunload', () => clearInterval(interval));
</script>
@endif
@endsection