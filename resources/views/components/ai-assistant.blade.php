<div
    class="fixed bottom-6 end-6 z-40"
    x-data="{
        open: false,
        loading: false,
        draft: '',
        messages: [
            { role: 'assistant', text: @js(__('Hi! Ask me things like pending requests, active shipments, low stock, or a tracking number like RF-XXXXXXXX.')) },
        ],
        async send() {
            const text = this.draft.trim();
            if (!text || this.loading) return;

            this.messages.push({ role: 'user', text });
            this.draft = '';
            this.loading = true;
            this.$nextTick(() => this.scrollToBottom());

            try {
                const response = await fetch('{{ route('assistant.ask') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ message: text }),
                });
                const data = await response.json();
                this.messages.push({ role: 'assistant', text: data.reply ?? @js(__('The assistant could not answer that right now. Please try again.')) });
            } catch (e) {
                this.messages.push({ role: 'assistant', text: @js(__('The assistant could not answer that right now. Please try again.')) });
            } finally {
                this.loading = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        },
        scrollToBottom() {
            const el = this.$refs.messagesEl;
            if (el) el.scrollTop = el.scrollHeight;
        },
    }"
>
    <button
        type="button"
        aria-label="{{ __('AI Assistant') }}"
        x-on:click="open = !open; $nextTick(() => scrollToBottom())"
        class="w-14 h-14 rounded-2xl bg-field-600 hover:bg-field-700 text-white shadow-xl shadow-field-950/30 flex items-center justify-center transition-colors"
    >
        <x-icon name="sparkles" class="w-6 h-6" x-show="!open" />
        <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        x-on:click.outside="open = false"
        class="absolute bottom-[calc(100%+0.75rem)] end-0 w-80 max-w-[90vw] h-[28rem] max-h-[70vh] bg-white border border-ink-100 rounded-3xl shadow-2xl overflow-hidden flex flex-col"
    >
        <div class="flex items-center gap-2 px-4 py-3 border-b border-ink-100 bg-field-50">
            <div class="w-8 h-8 rounded-xl bg-field-600 text-white flex items-center justify-center shrink-0"><x-icon name="sparkles" class="w-4 h-4" /></div>
            <div>
                <p class="text-xs font-bold text-ink-900">{{ __('ReliefFlow Assistant') }}</p>
                <p class="text-[10px] text-ink-400">{{ __('AI-powered, answers from live platform data') }}</p>
            </div>
        </div>

        <div x-ref="messagesEl" class="flex-grow overflow-y-auto p-4 space-y-3">
            <template x-for="(message, index) in messages" :key="index">
                <div class="flex" :class="message.role === 'user' ? 'justify-end' : 'justify-start'">
                    <p
                        class="max-w-[85%] text-[11px] leading-relaxed rounded-2xl px-3.5 py-2.5"
                        :class="message.role === 'user' ? 'bg-field-600 text-white' : 'bg-ink-50 text-ink-800'"
                        x-text="message.text"
                    ></p>
                </div>
            </template>
            <div x-show="loading" x-cloak class="flex justify-start">
                <p class="bg-ink-50 text-ink-400 text-[11px] rounded-2xl px-3.5 py-2.5">{{ __('Thinking…') }}</p>
            </div>
        </div>

        <form x-on:submit.prevent="send()" class="flex items-center gap-2 p-3 border-t border-ink-100">
            <input
                type="text"
                x-model="draft"
                :disabled="loading"
                placeholder="{{ __('Ask a question…') }}"
                class="flex-grow rounded-xl border-ink-200 text-xs focus:border-field-500 focus:ring-field-500 disabled:opacity-60"
            >
            <button type="submit" :disabled="loading || !draft.trim()" class="w-9 h-9 shrink-0 rounded-xl bg-field-600 hover:bg-field-700 disabled:opacity-40 text-white flex items-center justify-center transition-colors">
                <x-icon name="arrow-right" class="w-4 h-4 rtl:rotate-180" />
            </button>
        </form>
    </div>
</div>
