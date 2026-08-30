document.addEventListener('alpine:init', () => {
    Alpine.data('pfLibrary', (prompts = []) => ({
        prompts,

        q: '',
        cat: 'all',
        favs: false,
        sort: 'updated',
        showFilters: false,

        filtered(i) {
            const p = this.prompts[i];
            if (!p) return false;
            if (this.favs && !p.favorite) return false;
            if (this.cat !== 'all' && p.category !== this.cat) return false;
            if (this.q.trim()) {
                const q = this.q.trim().toLowerCase();
                const haystack = (p.name + ' ' + p.description + ' ' + p.category + ' ' + p.tags.join(' ')).toLowerCase();
                if (!haystack.includes(q)) return false;
            }
            return true;
        },

        visibleCount() {
            if (!this.prompts.length) return 0;
            let n = 0;
            for (let i = 0; i < this.prompts.length; i += 1) {
                if (this.filtered(i)) n += 1;
            }
            return n;
        },

        sortedOrders() {
            const order = [];
            const list = this.prompts.map((p, i) => ({ i }));
            if (this.sort === 'usage') list.sort((a, b) => this.prompts[b.i].usageCount - this.prompts[a.i].usageCount);
            else if (this.sort === 'name') list.sort((a, b) => this.prompts[a.i].name.localeCompare(this.prompts[b.i].name));
            else list.sort((a, b) => new Date(this.prompts[b.i].updatedAt) - new Date(this.prompts[a.i].updatedAt));
            list.forEach(item => order.push(item.i));
            return order;
        },

        orderOf(i) {
            return this.sortedOrders().indexOf(i);
        },
    }));

    Alpine.data('pfRuns', (runs = []) => ({
        runs,

        q: '',
        status: 'all',
        provider: 'all',

        filtered(i) {
            const r = this.runs[i];
            if (!r) return false;
            if (this.status !== 'all' && r.status !== this.status) return false;
            if (this.provider !== 'all' && r.provider !== this.provider) return false;
            if (this.q.trim()) {
                const q = this.q.trim().toLowerCase();
                const haystack = (r.promptName + ' ' + r.category + ' ' + r.model + ' ' + r.outputPreview).toLowerCase();
                if (!haystack.includes(q)) return false;
            }
            return true;
        },

        visibleCount() {
            if (!this.runs.length) return 0;
            let n = 0;
            for (let i = 0; i < this.runs.length; i += 1) {
                if (this.filtered(i)) n += 1;
            }
            return n;
        },
    }));

    Alpine.data('pfDiff', (initial = {}) => ({
        name: initial.name || '',
        currentVersion: initial.currentVersion || 0,
        versions: initial.versions || [],
        system: initial.system || '',
        message: initial.message || '',
        tab: 'message',
        base: initial.versions && initial.versions.length > 1 ? initial.versions[1].number : (initial.currentVersion || 1),
        compare: initial.currentVersion || initial.versions[0]?.number || 1,

        get baseInfo() { return this.versions.find(v => v.number === this.base) || this.versions[1] || {}; },
        get compareInfo() { return this.versions.find(v => v.number === this.compare) || {}; },

        currentText(tab) {
            return tab === 'system' ? this.system : this.message;
        },

        textFor(version, tab) {
            const current = this.currentVersion || this.compare;
            let lines = this.currentText(tab).split('\n').filter(l => l.trim() !== '');
            let target = version;
            const steps = Math.min(Math.max(0, current - target), lines.length - 1);
            for (let s = 0; s < steps; s += 1) {
                if (lines.length <= 2) break;
                const idx = ((7 * (target + s) + 3) % lines.length + lines.length) % lines.length;
                lines.splice(idx, 1);
                if (lines.length > 1) {
                    const j = ((idx + 2) % lines.length + lines.length) % lines.length;
                    if (lines[j] && lines[j].length > 14) {
                        lines[j] = lines[j].slice(0, Math.max(10, lines[j].length - 48)).trim() + '…';
                    }
                }
            }
            return lines.length ? lines.join('\n').trim() : '  (empty)';
        },

        diffRows() {
            const a = this.textFor(this.base, this.tab).split('\n');
            const b = this.textFor(this.compare, this.tab).split('\n');
            const rows = [];
            let i = 0; let j = 0;

            const n = a.length; const m = b.length;
            const dp = Array.from({ length: n + 1 }, () => new Array(m + 1).fill(0));
            for (i = n - 1; i >= 0; i -= 1) {
                for (j = m - 1; j >= 0; j -= 1) {
                    dp[i][j] = a[i] === b[j]
                        ? dp[i + 1][j + 1] + 1
                        : Math.max(dp[i + 1][j], dp[i][j + 1]);
                }
            }
            i = 0; j = 0;
            while (i < n && j < m) {
                if (a[i] === b[j]) { rows.push({ type: 'ctx', line: a[i] }); i += 1; j += 1; }
                else if (dp[i + 1][j] >= dp[i][j + 1]) { rows.push({ type: 'rem', line: a[i] }); i += 1; }
                else { rows.push({ type: 'add', line: b[j] }); j += 1; }
            }
            while (i < n) { rows.push({ type: 'rem', line: a[i] }); i += 1; }
            while (j < m) { rows.push({ type: 'add', line: b[j] }); j += 1; }
            return rows;
        },

        changedCount() {
            return this.diffRows().filter(r => r.type !== 'ctx').length;
        },
    }));
});