document.addEventListener('alpine:init', () => {
    Alpine.data('pfChat', () => ({
        // --- Compose box / UI state ---
        input: '',
        showConfig: true,
        showSystemPrompt: false,
        stream: true,

        // --- Request / streaming animation ---
        isSending: false,
        isStreaming: false,
        _timer: null,
        _streamId: null,
        _fullText: '',
        _tokens: [],
        _position: 0,

        // True from the moment a message is submitted until the stream finishes.
        isBusy() {
            return this.isSending || this.isStreaming;
        },

        // --- Rendering ---
        renderMarkdown(text) {
            return window.renderMarkdown(text);
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
            if (!text || this.isBusy()) return;

            // Flip the pending flag before the request leaves the browser: generation
            // is a blocking roundtrip, so this is what keeps the loading state on screen
            // for the whole wait instead of only during the token animation.
            this.isSending = true;

            this.input = '';
            this.$nextTick(() => {
                const textarea = this.$refs.chatInput;
                if (textarea) textarea.style.height = 'auto';
                this.scrollToBottom();
            });

            this.$wire
                .send(text)
                .then((result) => {
                    this.isSending = false;
                    if (result) this.startStream(result);
                })
                .catch(() => {
                    this.isSending = false;
                });
        },

        // --- Streaming animation over the server-persisted placeholder ---
        startStream({ id, content, tokensIn }) {
            this.isStreaming = true;
            this._streamId = id;
            this._fullText = content;
            this._tokens = Array.from(content.match(/\s+|\S+/g) ?? []);
            this._position = 0;

            this.scrollToBottom();

            this._timer = setInterval(() => this.step(), 35);
        },

        step() {
            const el = document.getElementById('pf-stream-' + this._streamId);
            if (!el) {
                this.finishStream();
                return;
            }

            this._position = Math.min(this._position + 2, this._tokens.length);
            el.textContent = this._tokens.slice(0, this._position).join('');

            this.scrollToBottom();

            if (this._position >= this._tokens.length) {
                this.finishStream();
            }
        },

        finishStream() {
            if (!this.isStreaming) return;

            const id = this._streamId;
            const content = document.getElementById('pf-stream-' + id)?.textContent || this._fullText;
            const tokensOut = Math.max(1, Math.round(content.split(/\s+/).filter(Boolean).length * 0.75));

            this.resetStream();

            this.$wire.finishStream(id, content, tokensOut);
            this.scrollToBottom();
        },

        stop() {
            if (!this.isStreaming) return;

            const id = this._streamId;
            const content = document.getElementById('pf-stream-' + id)?.textContent || '';

            this.resetStream();

            this.$wire.cancelStream(id, content);
        },

        resetStream() {
            clearInterval(this._timer);
            this._timer = null;
            this.isStreaming = false;
            this._streamId = null;
            this._fullText = '';
            this._tokens = [];
            this._position = 0;
        },

        clearChat() {
            if (this.isBusy()) return;
            this.$wire.clearChat();
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.thread;
                if (el) el.scrollTop = el.scrollHeight;
            });
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
            clearInterval(this._timer);
            window.removeEventListener('keydown', this._onKeydown);
        },
    }));
});

window.renderMarkdown = function (text) {
    if (!text) return '';

    let html = String(text)
        // Escape HTML first
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')

        // Code blocks
        .replace(
            /```(\w*)\n([\s\S]*?)```/g,
            '<pre class="rounded-lg bg-zinc-100 px-3.5 py-3 font-mono text-xs leading-relaxed text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"><code>$2</code></pre>'
        )

        // Inline code
        .replace(
            /`([^`]+)`/g,
            '<code class="rounded-md bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-brand-700 dark:bg-zinc-800 dark:text-brand-300">$1</code>'
        )

        // Bold
        .replace(
            /\*\*(.+?)\*\*/g,
            '<strong>$1</strong>'
        )

        // Italic
        .replace(
            /\*(.+?)\*/g,
            '<em>$1</em>'
        )

        // Headings
        .replace(
            /^### (.+)$/gm,
            '<h3 class="mt-4 mb-1 text-sm font-semibold text-zinc-900 dark:text-white">$1</h3>'
        )
        .replace(
            /^## (.+)$/gm,
            '<h2 class="mt-5 mb-2 text-base font-semibold text-zinc-900 dark:text-white">$1</h2>'
        )
        .replace(
            /^# (.+)$/gm,
            '<h1 class="mt-6 mb-2 text-lg font-bold text-zinc-900 dark:text-white">$1</h1>'
        )

        // Horizontal rule
        .replace(
            /^---$/gm,
            '<hr class="my-4 border-zinc-200 dark:border-white/10">'
        )

        // Unordered lists
        .replace(
            /^- (.+)$/gm,
            '<li class="ml-4 list-disc">$1</li>'
        )

        // Ordered lists
        .replace(
            /^\d+\. (.+)$/gm,
            '<li class="ml-4 list-decimal">$1</li>'
        )

        // Paragraph breaks
        .replace(/\n\n/g, '</p><p class="mt-2">')

        // Single line breaks
        .replace(/\n/g, '<br>');

    return '<p class="mt-2">' + html + '</p>';
};