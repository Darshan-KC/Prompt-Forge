document.addEventListener('alpine:init', () => {
    Alpine.data('pfChat', (initial = {}) => ({
        // --- Configuration ---
        provider: initial.provider || 'anthropic',
        model: initial.model || 'claude-3-7-sonnet',
        temperature: initial.temperature ?? 0.7,
        maxTokens: initial.maxTokens ?? 4096,
        topP: initial.topP ?? 1,
        stream: true,
        providers: initial.providers || [],
        system: initial.system || 'You are a helpful, articulate assistant. Provide clear, structured answers. When appropriate, use markdown formatting including headings, lists, and code blocks.',

        // --- Conversation state ---
        messages: initial.messages || [],
        input: '',
        isStreaming: false,
        _streamTimer: null,
        _chunkTimer: null,
        _chunks: [],
        _chunkIndex: 0,
        _activeMessageId: null,

        // --- Panel ---
        showConfig: true,
        showSystemPrompt: false,

        // --- Derived ---
        get currentProvider() {
            return this.providers.find(p => p.slug === this.provider) || {};
        },

        get models() {
            return (this.currentProvider.models || []).map(m => ({ ...m, provider: this.currentProvider.slug }));
        },

        get currentModel() {
            return this.models.find(m => m.slug === this.model) || {};
        },

        get totalTokensIn() {
            return this.messages.reduce((sum, m) => sum + (m.tokensIn || 0), 0);
        },

        get totalTokensOut() {
            return this.messages.reduce((sum, m) => sum + (m.tokensOut || 0), 0);
        },

        get totalCost() {
            return this.messages.reduce((sum, m) => sum + (m.cost || 0), 0);
        },

        get messageCount() {
            return this.messages.length;
        },

        // --- Actions ---
        selectProvider(slug) {
            this.provider = slug;
            const list = this.models;
            this.model = list.length ? list[0].slug : this.model;
        },

        formatPrice(perMillion) {
            return perMillion.toFixed(2) + ' / 1M';
        },

        formatTokens(n) {
            if (!n) return '0';
            return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },

        estimateTokens(text) {
            return Math.max(1, Math.ceil(text.length / 4));
        },

        // --- Input ---
        handleKeydown(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.send();
            }
        },

        autoResize(e) {
            const el = e.target;
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 200) + 'px';
        },

        // --- Send ---
        send() {
            const text = this.input.trim();
            if (!text || this.isStreaming) return;

            const userMessage = {
                id: Date.now(),
                role: 'user',
                content: text,
                timestamp: new Date().toISOString(),
            };

            this.messages.push(userMessage);
            this.input = '';

            // Reset textarea height
            this.$nextTick(() => {
                const textarea = this.$refs.chatInput;
                if (textarea) textarea.style.height = 'auto';
                this.scrollToBottom();
            });

            this.startAssistantResponse(text);
        },

        // --- Streaming simulation ---
        startAssistantResponse(userText) {
            this.isStreaming = true;

            const assistantMessage = {
                id: Date.now() + 1,
                role: 'assistant',
                content: '',
                timestamp: new Date().toISOString(),
                tokensIn: this.estimateTokens(this.system + userText),
                tokensOut: 0,
                cost: 0,
                status: 'streaming',
                model: this.model,
                provider: this.provider,
            };

            this.messages.push(assistantMessage);
            this._activeMessageId = assistantMessage.id;

            this.$nextTick(() => this.scrollToBottom());

            // Simulate delay before streaming starts
            this._streamTimer = setTimeout(() => {
                this._chunks = this.buildResponse(userText);
                this._chunkIndex = 0;
                this.streamChunk();
            }, 500 + Math.random() * 500);
        },

        streamChunk() {
            if (!this.isStreaming || this._chunkIndex >= this._chunks.length) {
                this.finishStream();
                return;
            }

            const msg = this.messages.find(m => m.id === this._activeMessageId);
            if (!msg) { this.finishStream(); return; }

            const slice = this._chunks[this._chunkIndex];
            msg.content += slice;
            msg.tokensOut += Math.max(1, Math.round(slice.split(/\s+/).filter(Boolean).length * 0.75));
            this._chunkIndex++;

            this.$nextTick(() => this.scrollToBottom());

            this._chunkTimer = setTimeout(() => this.streamChunk(), 40 + Math.random() * 120);
        },

        finishStream() {
            this._teardown();
            this.isStreaming = false;

            const msg = this.messages.find(m => m.id === this._activeMessageId);
            if (msg) {
                msg.status = 'completed';
                const pricing = this.currentModel.pricing || { input: 0, output: 0 };
                msg.cost = ((pricing.input * msg.tokensIn + pricing.output * msg.tokensOut) / 1e6);
            }

            this._activeMessageId = null;
        },

        stop() {
            this._teardown();
            this.isStreaming = false;

            const msg = this.messages.find(m => m.id === this._activeMessageId);
            if (msg) {
                msg.status = 'cancelled';
            }

            this._activeMessageId = null;
        },

        _teardown() {
            if (this._streamTimer) clearTimeout(this._streamTimer);
            if (this._chunkTimer) clearTimeout(this._chunkTimer);
            this._streamTimer = null;
            this._chunkTimer = null;
        },

        clearChat() {
            if (this.isStreaming) return;
            this.messages = [];
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.thread;
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        // --- Mock response builder ---
        buildResponse(userText) {
            const lower = userText.toLowerCase();

            if (lower.includes('hello') || lower.includes('hi') || lower.includes('hey')) {
                return [
                    'Hello! I\'m your AI assistant. I can help with ',
                    'a wide range of tasks including:\n\n',
                    '- **Code review** and debugging\n',
                    '- **Writing** and editing\n',
                    '- **Research** and analysis\n',
                    '- **Math** and logic problems\n\n',
                    'What would you like to work on today?',
                ];
            }

            if (lower.includes('code') || lower.includes('function') || lower.includes('implement')) {
                return [
                    '# Implementation Plan\n\n',
                    'Here\'s a structured approach to the coding task:\n\n',
                    '## 1. Break it down\n',
                    'Split the problem into smaller, testable units.\n\n',
                    '## 2. Define the interface\n',
                    '```typescript\n',
                    'interface Result {\n',
                    '  success: boolean;\n',
                    '  data: unknown;\n',
                    '  error?: string;\n',
                    '}\n',
                    '```\n\n',
                    '## 3. Implement core logic\n',
                    'Write the main function with proper error handling,\n',
                    'then add edge case coverage.\n\n',
                    '## 4. Test\n',
                    'Create unit tests for each branch, then integration\n',
                    'tests for the full flow.\n\n',
                    'Want me to write the full implementation for a\n',
                    'specific language or framework?',
                ];
            }

            if (lower.includes('explain') || lower.includes('what is') || lower.includes('how does')) {
                return [
                    '## Explanation\n\n',
                    'Let me break this down clearly:\n\n',
                    '**Core concept:** The idea works by establishing a\n',
                    'chain of transformations that convert raw input into\n',
                    'structured output.\n\n',
                    '**Key components:**\n',
                    '1. **Input layer** — receives and validates the data\n',
                    '2. **Processing** — applies the transformation rules\n',
                    '3. **Output** — formats the result for consumption\n\n',
                    '**Why it matters:**\n',
                    'This pattern is widely used because it provides:\n',
                    '- Clear separation of concerns\n',
                    '- Easy testing at each stage\n',
                    '- Flexibility to swap components\n\n',
                    'Would you like a deeper dive into any of these\n',
                    'components?',
                ];
            }

            return [
                '## Response\n\n',
                'Great question. Here\'s my analysis:\n\n',
                '### Key Points\n\n',
                '1. **Context matters** — The answer depends on your\n',
                '   specific use case and constraints.\n\n',
                '2. **Trade-offs** — Every approach has pros and cons.\n',
                '   The best choice balances complexity against\n',
                '   maintainability.\n\n',
                '3. **Iteration** — Start with the simplest version that\n',
                '   works, then refine based on real feedback.\n\n',
                '### Recommendation\n\n',
                'I\'d suggest starting with a minimal prototype to\n',
                'validate the approach, then expanding based on what\n',
                'you learn.\n\n',
                '```markdown\n',
                'Tip: You can save this conversation as a prompt\n',
                'template from the sidebar menu.\n',
                '```\n\n',
                'Want me to elaborate on any of these points?',
            ];
        },

        // --- Helpers ---
        timeAgo(iso) {
            if (!iso) return '';
            const diff = (Date.now() - new Date(iso).getTime()) / 1000;
            if (diff < 5) return 'just now';
            if (diff < 60) return Math.floor(diff) + 's ago';
            if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
            return Math.floor(diff / 3600) + 'h ago';
        },

        // --- Init ---
        init() {
            this._onKeydown = (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'enter') {
                    e.preventDefault();
                    this.isStreaming ? this.stop() : this.send();
                }
            };
            window.addEventListener('keydown', this._onKeydown);
            this.scrollToBottom();
        },

        destroy() {
            this._teardown();
            window.removeEventListener('keydown', this._onKeydown);
        },
    }));
});
