@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div style="margin: -24px -1.5rem -2rem; height: calc(100vh - 64px); display: flex; flex-direction: column; overflow: hidden;">
    <div class="fade-in-up" style="flex: 1; background: rgba(0,0,0,0.15); display: grid; grid-template-columns: 260px 1fr; overflow: hidden;">

        {{-- Conversation List --}}
        <div style="border-right:1px solid rgba(139,92,246,.15);display:flex;flex-direction:column;background:rgba(8,8,26,0.3);">
            <div style="padding:1rem;border-bottom:1px solid rgba(139,92,246,.12);">
                <div style="position:relative;">
                    <input type="text" id="convSearch" placeholder="Search conversations..." 
                           style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(139,92,246,0.2);border-radius:12px;padding:9px 12px 9px 34px;color:#fff;font-size:.8rem;outline:none;transition:all 0.2s;">
                    <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);opacity:0.4;">🔍</span>
                </div>
            </div>
            <div style="padding:0.8rem 1.2rem;border-bottom:1px solid rgba(139,92,246,.08);">
                <h2 style="font-size:.7rem;font-weight:800;color:rgba(196,181,253,0.5);letter-spacing:.08em;text-transform:uppercase;">Conversations</h2>
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
                        @php 
                            $isActive = isset($selectedTrainer) && $selectedTrainer && $selectedTrainer->id == $item['user']->id;
                            $lastMsgTime = $conversations->where(fn($m) => ($m->sender_id == $item['user']->id || $m->receiver_id == $item['user']->id))->first()->created_at;
                        @endphp
                        <a href="{{ url('/chat/' . $item['user']->id) }}"
                           class="conv-item"
                           data-name="{{ strtolower($item['user']->name) }}"
                           style="display:block;padding:12px 14px;border-bottom:1px solid rgba(139,92,246,.05);text-decoration:none;transition:all .2s;{{ $isActive ? 'background:rgba(139,92,246,.12);border-left:3px solid #8b5cf6;' : 'border-left:3px solid transparent;' }}">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="position:relative;flex-shrink:0;">
                                    <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#8b5cf6,#ec4899);display:flex;align-items:center;justify-content:center;font-size:.95rem;font-weight:800;color:#fff;">
                                        {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                                    </div>
                                    <span style="position:absolute;bottom:-2px;right:-2px;width:10px;height:10px;border-radius:50%;background:#10b981;border:2px solid #0a0a1a;"></span>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2px;">
                                        <p style="font-size:.82rem;font-weight:700;color:#e2d9f3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item['user']->name }}</p>
                                        <span style="font-size:.6rem;color:rgba(255,255,255,0.2);">{{ $lastMsgTime->format('H:i') }}</span>
                                    </div>
                                    <p style="font-size:.72rem;color:rgba(255,255,255,.35);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item['last_message'] }}</p>
                                </div>
                                @if($item['unread'] > 0)
                                    <span style="background:#8b5cf6;color:#fff;font-size:.6rem;padding:2px 6px;border-radius:50px;font-weight:800;flex-shrink:0;box-shadow:0 0 10px rgba(139,92,246,0.3);">{{ $item['unread'] }}</span>
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
                <div style="padding:10px 20px;border-bottom:1px solid rgba(139,92,246,.12);background:rgba(139,92,246,.04);display:flex;align-items:center;gap:12px;">
                    <div style="position:relative;">
                        <div style="width:42px;height:42px;border-radius:14px;background:linear-gradient(135deg,#8b5cf6,#ec4899);display:flex;align-items:center;justify-content:center;font-size:1.05rem;font-weight:800;color:#fff;">
                            {{ strtoupper(substr($selectedTrainer->name, 0, 1)) }}
                        </div>
                        <span style="position:absolute;bottom:-2px;right:-2px;width:12px;height:12px;border-radius:50%;background:#10b981;border:3px solid #0a0a1a;"></span>
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <p style="font-size:.95rem;font-weight:800;color:#fff;">{{ $selectedTrainer->name }}</p>
                            @if(isset($nextSession))
                                <span style="font-size:.65rem;background:rgba(139,92,246,0.15);color:#c4b5fd;padding:2px 8px;border-radius:6px;font-weight:700;border:1px solid rgba(139,92,246,0.2);">
                                    Next Session: {{ \Carbon\Carbon::parse($nextSession->session_date)->format('M d') }}
                                </span>
                            @endif
                        </div>
                        <p style="font-size:.7rem;color:rgba(255,255,255,.35);font-weight:600;">{{ $selectedTrainer->specialization ?? ucfirst($selectedTrainer->role ?? 'Trainer') }}</p>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <button title="Start Video Call" style="background:transparent;border:1px solid rgba(139,92,246,0.2);color:#c4b5fd;border-radius:10px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='rgba(139,92,246,0.1)'" onmouseout="this.style.background='transparent'">📹</button>
                        <a href="{{ url('/trainers/' . $selectedTrainer->id) }}" style="text-decoration:none;background:rgba(139,92,246,0.1);color:#c4b5fd;border:1px solid rgba(139,92,246,0.2);padding:7px 14px;border-radius:10px;font-size:.75rem;font-weight:700;transition:all 0.2s;" onmouseover="this.style.background='rgba(139,92,246,0.2)'" onmouseout="this.style.background='rgba(139,92,246,0.1)'">View Profile</a>
                    </div>
                </div>

                {{-- Messages --}}
                <div id="chatMessages" style="flex:1;overflow-y:auto;padding:1.2rem;display:flex;flex-direction:column;gap:.7rem;background:rgba(0,0,0,.15);">
                    <div style="text-align:center;color:rgba(255,255,255,.2);font-size:.8rem;">Loading messages…</div>
                </div>

                {{-- Input --}}
                <div style="padding:12px 16px;border-top:1px solid rgba(139,92,246,.12);background:rgba(8,8,26,.8);">
                    <div id="typingIndicator" style="display:none;font-size:.65rem;color:rgba(255,255,255,0.3);margin-bottom:8px;padding-left:4px;">
                        Trainer is typing...
                    </div>
                    <form id="chatForm" style="display:flex;gap:.8rem;align-items:center;" onsubmit="return false;">
                        @csrf
                        <div style="display:flex;gap:6px;">
                            <button type="button" title="Attach file" style="background:rgba(255,255,255,0.05);border:none;color:#c4b5fd;width:38px;height:38px;border-radius:10px;cursor:pointer;font-size:1.1rem;">📎</button>
                            <button type="button" title="Emojis" style="background:rgba(255,255,255,0.05);border:none;color:#c4b5fd;width:38px;height:38px;border-radius:10px;cursor:pointer;font-size:1.1rem;">😊</button>
                        </div>
                        <input type="hidden" name="receiver_id" value="{{ $selectedTrainer->id }}" id="receiverId">
                        <input type="text" name="message" id="messageInput"
                               style="flex:1;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.2);border-radius:12px;padding:11px 16px;color:#fff;font-size:.88rem;outline:none;transition:all .2s;"
                               placeholder="Type your message..." autocomplete="off"
                               onfocus="this.style.borderColor='rgba(139,92,246,0.5)';this.style.background='rgba(255,255,255,0.08)'"
                               onblur="this.style.borderColor='rgba(139,92,246,0.2)';this.style.background='rgba(255,255,255,0.05)'">
                        <button type="button" id="sendButton"
                                style="background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;border:none;border-radius:12px;width:40px;height:40px;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 6px 16px rgba(139,92,246,.3);transition:all .2s;">
                            <span style="transform:rotate(-45deg);margin-left:4px;">🚀</span>
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

    let lastRenderedDate = null;

    function formatMessageDate(date) {
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);

        if (date.toDateString() === today.toDateString()) return 'Today';
        if (date.toDateString() === yesterday.toDateString()) return 'Yesterday';
        
        return date.toLocaleDateString([], { month: 'short', day: 'numeric', year: date.getFullYear() !== today.getFullYear() ? 'numeric' : undefined });
    }

    function buildMessageEl(msg) {
        const isOwn = msg.sender_id == currentUserId;
        const date = new Date(msg.created_at);
        const timeStr = date.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
        const fullDateStr = date.toLocaleDateString([], {month:'short', day:'numeric'});
        
        const fragment = document.createDocumentFragment();

        // Date separator
        const dateStr = date.toDateString();
        if (dateStr !== lastRenderedDate) {
            const separator = document.createElement('div');
            separator.style.cssText = 'text-align:center;margin:1.5rem 0 1rem;position:relative;';
            separator.innerHTML = `
                <div style="position:absolute;top:50%;left:0;right:0;height:1px;background:rgba(139,92,246,0.1);z-index:1;"></div>
                <span style="position:relative;z-index:2;background:#0f0f23;padding:4px 14px;border-radius:20px;font-size:.65rem;color:rgba(255,255,255,0.3);font-weight:800;text-transform:uppercase;letter-spacing:0.05em;border:1px solid rgba(139,92,246,0.1);">${formatMessageDate(date)}</span>
            `;
            fragment.appendChild(separator);
            lastRenderedDate = dateStr;
        }

        const wrapper = document.createElement('div');
        wrapper.dataset.msgId = msg.id;
        wrapper.style.cssText = 'display:flex;margin-bottom:4px;' + (isOwn ? 'justify-content:flex-end;' : 'justify-content:flex-start;');

        if (isOwn) {
            wrapper.innerHTML = `
                <div style="max-width:75%;position:relative;">
                    <div style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);border-radius:18px 18px 4px 18px;padding:10px 14px;box-shadow:0 4px 12px rgba(139,92,246,.25);">
                        <p style="font-size:.88rem;color:#fff;word-break:break-word;line-height:1.4;">${escapeHtml(msg.message)}</p>
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:4px;margin-top:4px;">
                            <p style="font-size:.6rem;color:rgba(255,255,255,.6);">${timeStr}</p>
                            <span style="font-size:.7rem;color:rgba(255,255,255,0.6);line-height:1;">${msg.is_read ? '✓✓' : '✓'}</span>
                        </div>
                    </div>
                </div>`;
        } else {
            wrapper.innerHTML = `
                <div style="max-width:75%;">
                    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(139,92,246,.15);border-radius:18px 18px 18px 4px;padding:10px 14px;backdrop-filter:blur(10px);">
                        <p style="font-size:.88rem;color:#e2d9f3;word-break:break-word;line-height:1.4;">${escapeHtml(msg.message)}</p>
                        <p style="font-size:.6rem;color:rgba(255,255,255,.3);margin-top:4px;">${timeStr}</p>
                    </div>
                </div>`;
        }
        fragment.appendChild(wrapper);
        return fragment;
    }

    // Search functionality
    document.getElementById('convSearch')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.conv-item').forEach(item => {
            const name = item.dataset.name;
            item.style.display = name.includes(query) ? 'block' : 'none';
        });
    });

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
                    lastRenderedDate = null; // reset for full rebuild
                    messages.forEach(msg => chatMessagesDiv.appendChild(buildMessageEl(msg)));
                    lastMessageId = messages.length > 0 ? messages[messages.length - 1].id : null;
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