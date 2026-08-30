document.addEventListener('alpine:init', () => {
    Alpine.data('pfPlayground', () => (initial = {}) => ({
        // --- Configuration ---
        provider: initial.provider || 'anthropic',
        model: initial.model || 'claude-3-7-sonnet',
        temperature: initial.temperature ?? 0.2,
        maxTokens: initial.maxTokens ?? 2048,
        topP: initial.topP ?? 1,
        stream: true,
        providers: initial.providers || [],

        // --- Prompt content ---
        system: initial.system || '',
        promptText: initial.promptText || '',
        variables: (initial.variables || []).map(v => ({ ...v, value: v.value ?? '' })),
        showAddVariable: false,
        newVariableKey: '',
        newVariableLabel: '',

        // --- Editor tabs ---
        tab: 'message',

        // --- Run state ---
        status: 'idle', // idle | running | streaming | completed | cancelled
        output: '',
        tokensIn: 0,
        tokensOut: 0,
        cost: 0,
        latencyMs: 0,
        elapsed: 0,

        _timer: null,
        _streamTimer: null,
        _chunk: [],
        _chunkIndex: 0,
        _runSerial: 5822,

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

        get interpolated() {
            const map = {};
            this.variables.forEach(v => {
                map[v.key] = v.value;
            });
            return this.promptText.replace(/\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}/g, (match, key) => {
                const token = '{{' + key + '}}';
                return (map[key] !== undefined && map[key] !== '') ? map[key] : token;
            });
        },

        get estimatedInputTokens() {
            const chars = this.system.length + this.interpolated.length;
            return Math.max(1, Math.ceil(chars / 4));
        },

        get formattedElapsed() {
            return (this.elapsed / 1000).toFixed(1) + 's';
        },

        get formattedTokensOut() {
            return String(this.tokensOut).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },

        // --- Configuration actions ---
        selectProvider(slug) {
            if (this.isBusy()) return;
            this.provider = slug;
            const list = this.models;
            this.model = list.length ? list[0].slug : this.model;
        },

        formatPrice(perMillion) {
            return perMillion.toFixed(2) + ' / 1M';
        },

        tokenFor(key) {
            return '{{' + key + '}}';
        },

        // --- Variables ---
        insertVariable(key) {
            if (this.isBusy()) return;
            this.tab = 'message';
            this.$nextTick(() => {
                const el = this.$refs.promptField;
                if (!el) return;
                const token = '{{' + key + '}}';
                const start = el.selectionStart ?? this.promptText.length;
                const end = el.selectionEnd ?? this.promptText.length;
                const next = this.promptText.slice(0, start) + token + this.promptText.slice(end);
                this.promptText = next;
                el.focus();
                const caret = start + token.length;
                el.setSelectionRange(caret, caret);
            });
        },

        addVariable() {
            const key = this.newVariableKey.trim().replace(/[^a-zA-Z0-9_.]/g, '_').toLowerCase();
            if (!key) return;
            if (!this.variables.some(v => v.key === key)) {
                this.variables.push({ key, label: this.newVariableLabel.trim() || key, value: '' });
            }
            this.newVariableKey = '';
            this.newVariableLabel = '';
            this.showAddVariable = false;
        },

        removeVariable(key) {
            if (this.isBusy()) return;
            this.variables = this.variables.filter(v => v.key !== key);
        },

        // --- Run simulation ---
        isBusy() {
            return this.status === 'running' || this.status === 'streaming';
        },

        run() {
            if (this.isBusy()) return;

            this._teardownRun();
            this.output = '';
            this.tokensIn = this.estimatedInputTokens;
            this.tokensOut = 0;
            this.latencyMs = 0;
            this.cost = 0;
            this.elapsed = 0;
            this.status = 'running';

            const tick = () => {
                this.elapsed += 100;
                if (this.isBusy()) {
                    this._timer = setTimeout(tick, 100);
                }
            };
            this._timer = setTimeout(tick, 100);

            this._streamTimer = setTimeout(() => this.startStreaming(), 400 + Math.random() * 300);
        },

        startStreaming() {
            if (this.status !== 'running') return;
            this.status = 'streaming';
            this._chunk = this.buildResponse();
            this._chunkIndex = 0;
            this._streamChunk();
        },

        _streamChunk() {
            if (this.status !== 'streaming') return;
            if (this._chunkIndex >= this._chunk.length) {
                this.finish();
                return;
            }
            const slice = this._chunk[this._chunkIndex];
            this.output += slice;
            this.tokensOut += Math.max(1, Math.round(slice.split(/\s+/).filter(Boolean).length / 4));
            this._chunkIndex += 1;
            this._streamTimer = setTimeout(() => this._streamChunk(), 60 + Math.random() * 140);
        },

        stop() {
            if (!this.isBusy()) return;
            this._teardownRun();
            this.status = 'cancelled';
            const total = this.tokensIn + this.tokensOut;
            this._notify('stop-circle', `Run stopped after ${total.toLocaleString()} tokens.`);
        },

        finish() {
            if (this.status !== 'streaming') return;
            this._teardownRun();
            this.status = 'completed';
            this.latencyMs = this.elapsed;
            this.cost = this._estimateCost();
            this._notify('check-circle', `Run #${this._runSerial} saved to history (mock).`);
            this._runSerial += 1;
        },

        reset() {
            if (this.isBusy()) return;
            this.output = '';
            this.status = 'idle';
            this.tokensIn = 0;
            this.tokensOut = 0;
            this.latencyMs = 0;
            this.cost = 0;
            this.elapsed = 0;
        },

        _teardownRun() {
            if (this._timer) clearTimeout(this._timer);
            if (this._streamTimer) clearTimeout(this._streamTimer);
            this._timer = null;
            this._streamTimer = null;
        },

        _estimateCost() {
            const pricing = this.currentModel.pricing || { input: 0, output: 0 };
            return (pricing.input * this.tokensIn + pricing.output * this.tokensOut) / 1e6;
        },

        buildResponse() {
            const inputTokens = this.tokensIn || this.estimatedInputTokens;
            return [
                '# Analysis complete',
                '',
                'Based on the instructions and context you provided, here is the structured result.',
                '',
                '## Key findings',
                '',
                '1. **Structure is solid** — the prompt defines a clear role, explicit constraints, and a concrete output format.',
                '2. **Variables resolve cleanly** — all interpolated values were injected without truncation.',
                '3. **One refinement** — tightening the output format constrains the response and improves parseability.',
                '',
                '## Recommended adjustments',
                '',
                '- Add an explicit "do not" list to reduce off-spec output.',
                '- Provide one worked example when the expected output shape is complex.',
                '',
                '## Summary',
                '',
                '| Metric | Value |',
                '| --- | --- |',
                '| Input tokens | ' + inputTokens.toLocaleString() + ' |',
                '| Output tokens | (streaming) |',
                '| Models compared | 1 |',
                '',
                'You can now iterate on this prompt, bump the temperature for more variety, or run it side by side with another model from the payload menu.',
                '',
                '```',
                '# Tip',
                'Save this as a version on the Prompt page to keep a traceable history.',
                '```',
            ];
        },

        _notify(icon, message) {
            window.dispatchEvent(new CustomEvent('pf-toast', { detail: { icon, message } }));
        },

        init() {
            this._onKeydown = (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'enter') {
                    e.preventDefault();
                    this.isBusy() ? this.stop() : this.run();
                }
            };
            window.addEventListener('keydown', this._onKeydown);
        },

        destroy() {
            this._teardownRun();
            window.removeEventListener('keydown', this._onKeydown);
        },
    }));
});