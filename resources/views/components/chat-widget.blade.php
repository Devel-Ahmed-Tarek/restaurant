{{-- AI Chat Widget --}}
<div x-data="chatWidget()" x-cloak class="fixed bottom-20 lg:bottom-6 right-4 z-50">
    {{-- Chat Button --}}
    <button 
        @click="toggleChat()"
        x-show="!isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-75"
        x-transition:enter-end="opacity-100 scale-100"
        class="w-14 h-14 bg-primary-500 hover:bg-primary-600 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110"
    >
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
    </button>

    {{-- Chat Window --}}
    <div 
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="w-[350px] sm:w-[400px] h-[500px] sm:h-[550px] max-h-[80vh] bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-gray-200"
    >
        {{-- Header --}}
        <div class="bg-primary-500 text-white px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-sm">{{ __('AI Assistant') }}</h3>
                    <p class="text-xs text-white/80">{{ __('Ask me anything about our menu!') }}</p>
                </div>
            </div>
            <button @click="toggleChat()" class="p-1 hover:bg-white/20 rounded-full transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Messages Area --}}
        <div 
            x-ref="messagesContainer"
            class="chat-messages-scroll flex-1 min-h-0 overflow-y-auto overscroll-contain p-4 space-y-4 bg-gray-50"
            style="-webkit-overflow-scrolling: touch;"
        >
            {{-- Welcome Message --}}
            <template x-if="messages.length === 0">
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-800 mb-2">{{ __('Welcome!') }} 👋</h4>
                    <p class="text-sm text-gray-600 mb-4">{{ __('I can help you find the perfect meal, show you our offers, and place your order.') }}</p>
                    <div class="space-y-2">
                        <button @click="sendQuickMessage('{{ __('Show me your offers') }}')" class="w-full text-left px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm hover:border-primary-300 hover:bg-primary-50 transition-colors">
                            🎁 {{ __('Show me your offers') }}
                        </button>
                        <button @click="sendQuickMessage('{{ __('Recommend something not spicy') }}')" class="w-full text-left px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm hover:border-primary-300 hover:bg-primary-50 transition-colors">
                            🌶️ {{ __('Recommend something not spicy') }}
                        </button>
                        <button @click="sendQuickMessage('{{ __('I want chicken dishes') }}')" class="w-full text-left px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm hover:border-primary-300 hover:bg-primary-50 transition-colors">
                            🍗 {{ __('I want chicken dishes') }}
                        </button>
                        <button @click="sendQuickMessage('عاوز أكلة حلوة ورخيصة')" class="w-full text-left px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm hover:border-primary-300 hover:bg-primary-50 transition-colors">
                            🇪🇬 عاوز أكلة حلوة ورخيصة
                        </button>
                    </div>
                </div>
            </template>

            {{-- Chat Messages --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div 
                        :class="msg.role === 'user' 
                            ? 'bg-primary-500 text-white rounded-2xl rounded-br-md' 
                            : 'bg-white text-gray-800 rounded-2xl rounded-bl-md shadow-sm border border-gray-100'"
                        class="max-w-[85%] px-4 py-3 text-sm chat-message-content"
                    >
                        <div x-html="formatMessage(msg.content)"></div>
                    </div>
                </div>
            </template>

            {{-- Typing Indicator --}}
            <div x-show="isLoading" class="flex justify-start">
                <div class="bg-white text-gray-800 rounded-2xl rounded-bl-md shadow-sm border border-gray-100 px-4 py-3">
                    <div class="flex gap-1">
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cart Preview (when items in chat cart) --}}
        <div x-show="chatCart.length > 0" class="px-4 py-2 bg-primary-50 border-t border-primary-100 shrink-0">
            <div class="flex items-center justify-between text-sm">
                <span class="text-primary-700 font-medium">
                    🛒 <span x-text="chatCart.length"></span> {{ __('items') }} - <span x-text="formatMoney(cartTotal)"></span>
                </span>
                <button @click="viewCart()" class="text-primary-600 hover:text-primary-700 font-medium text-xs">
                    {{ __('View Cart') }}
                </button>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="p-4 bg-white border-t border-gray-100 shrink-0">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input 
                    type="text"
                    x-model="inputMessage"
                    :disabled="isLoading"
                    placeholder="{{ __('Type your message...') }}"
                    class="flex-1 px-4 py-2.5 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all disabled:opacity-50"
                    autocomplete="off"
                >
                <button 
                    type="submit"
                    :disabled="isLoading || !inputMessage.trim()"
                    class="px-4 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function chatWidget() {
    return {
        isOpen: false,
        isLoading: false,
        inputMessage: '',
        messages: [],
        conversationHistory: [],
        chatCart: [],
        customerData: {},

        init() {
            // Load saved state from localStorage
            const saved = localStorage.getItem('chatState');
            if (saved) {
                try {
                    const state = JSON.parse(saved);
                    this.messages = state.messages || [];
                    this.conversationHistory = state.history || [];
                    this.chatCart = state.cart || [];
                    this.customerData = state.customer || {};
                } catch (e) {
                    console.error('Failed to load chat state:', e);
                }
            }
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        async sendMessage() {
            const message = this.inputMessage.trim();
            if (!message || this.isLoading) return;

            this.inputMessage = '';
            this.messages.push({ role: 'user', content: message });
            this.isLoading = true;
            this.scrollToBottom();

            try {
                const response = await fetch('{{ route("chat.send", ["locale" => app()->getLocale()]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        message: message,
                        history: this.conversationHistory,
                        cart: this.chatCart,
                        customer: this.customerData
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.messages.push({ role: 'assistant', content: data.message });
                    this.conversationHistory = data.history || [];
                    this.chatCart = data.cart || [];
                    this.customerData = data.customer || {};

                    // Handle actions
                    if (data.action) {
                        this.handleAction(data.action);
                    }
                } else {
                    this.messages.push({ 
                        role: 'assistant', 
                        content: data.message || '{{ __("Sorry, something went wrong. Please try again.") }}'
                    });
                }
            } catch (error) {
                console.error('Chat error:', error);
                this.messages.push({ 
                    role: 'assistant', 
                    content: '{{ __("Sorry, I couldn\'t connect. Please check your internet connection.") }}'
                });
            } finally {
                this.isLoading = false;
                this.scrollToBottom();
                this.saveState();
            }
        },

        sendQuickMessage(message) {
            this.inputMessage = message;
            this.sendMessage();
        },

        handleAction(action) {
            if (action.type === 'order_placed' && action.order) {
                // Order was placed successfully
                this.chatCart = [];
                this.customerData = {};
                
                // Show success notification
                if (window.Alpine && window.Alpine.store('notifications')) {
                    window.Alpine.store('notifications').add({
                        type: 'success',
                        message: `{{ __('Order placed!') }} #${action.order.order_number}`
                    });
                }
            } else if (action.type === 'cart_updated') {
                // Cart was updated - could sync with main cart store if needed
            }
        },

        viewCart() {
            this.sendQuickMessage('{{ __("Show me my cart") }}');
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        saveState() {
            localStorage.setItem('chatState', JSON.stringify({
                messages: this.messages.slice(-20), // Keep last 20 messages
                history: this.conversationHistory.slice(-10), // Keep last 10 history items
                cart: this.chatCart,
                customer: this.customerData
            }));
        },

        formatMessage(content) {
            if (!content) return '';
            
            // Convert URLs to clickable links (before other formatting)
            let formatted = content.replace(
                /(https?:\/\/[^\s\)]+)/g, 
                '<a href="$1" target="_blank" rel="noopener">$1</a>'
            );
            
            // Convert markdown-like formatting
            formatted = formatted
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/\n/g, '<br>')
                .replace(/- (.*?)(?=<br>|$)/g, '• $1');
            
            return formatted;
        },

        formatMoney(amount) {
            return window.formatMoney ? window.formatMoney(amount) : `${amount}`;
        },

        get cartTotal() {
            return this.chatCart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }

@keyframes bounce {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-4px); }
}

/* Chat scroll styling */
.chat-messages-scroll {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}

.chat-messages-scroll::-webkit-scrollbar {
    width: 6px;
}

.chat-messages-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.chat-messages-scroll::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 3px;
}

.chat-messages-scroll::-webkit-scrollbar-thumb:hover {
    background-color: #94a3b8;
}

/* Message content styling */
.chat-message-content {
    overflow-wrap: break-word;
    word-wrap: break-word;
    word-break: break-word;
    hyphens: auto;
}

.chat-message-content a {
    color: inherit;
    text-decoration: underline;
    word-break: break-all;
}

.chat-message-content a:hover {
    opacity: 0.8;
}
</style>
