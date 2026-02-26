import './bootstrap';
import './theme';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('lessonComplete', (initialCompleted = false, completeUrl = '', completedLabel = 'Completed', completeBtnLabel = 'Complete') => ({
    completed: initialCompleted,
    completeUrl,
    completedLabel,
    completeBtnLabel,
    loading: false,
    async submitComplete() {
        if (this.loading || this.completed) return;
        this.loading = true;
        const formData = new FormData();
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) formData.append('_token', token);
        try {
            const r = await fetch(this.completeUrl, {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await r.json().catch(() => ({}));
            if (data.completed) {
                this.completed = true;
            }
        } finally {
            this.loading = false;
        }
    },
}));

Alpine.start();
