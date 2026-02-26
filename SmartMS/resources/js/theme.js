/**
 * Theme: dark by default. Persisted in localStorage.
 * A11y (visually impaired mode): off by default. Persisted in localStorage.
 */
(function () {
    const THEME_KEY = 'smartlms_theme';
    const A11Y_KEY = 'smartlms_a11y';

    function getTheme() {
        try {
            return localStorage.getItem(THEME_KEY) || 'dark';
        } catch (_) {
            return 'dark';
        }
    }

    function getA11y() {
        try {
            return localStorage.getItem(A11Y_KEY) === '1';
        } catch (_) {
            return false;
        }
    }

    function apply() {
        const root = document.documentElement;
        const theme = getTheme();
        const a11y = getA11y();
        root.classList.remove('light', 'dark', 'a11y');
        root.classList.add(theme);
        if (a11y) root.classList.add('a11y');
    }

    window.smartLmsTheme = {
        getTheme,
        getA11y,
        setTheme(value) {
            try {
                localStorage.setItem(THEME_KEY, value === 'light' ? 'light' : 'dark');
            } catch (_) {}
            apply();
        },
        setA11y(value) {
            try {
                localStorage.setItem(A11Y_KEY, value ? '1' : '0');
            } catch (_) {}
            apply();
        },
        toggleTheme() {
            this.setTheme(getTheme() === 'dark' ? 'light' : 'dark');
        },
        toggleA11y() {
            this.setA11y(!getA11y());
        },
    };

    apply();
})();
