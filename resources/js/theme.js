const Theme = {
    STORAGE_KEY: 'ww-theme',

    init() {
        try {
            const stored = localStorage.getItem(this.STORAGE_KEY);
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const dark = stored ? stored === 'dark' : prefersDark;
            this.apply(dark, false);

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!localStorage.getItem(this.STORAGE_KEY)) {
                    this.apply(e.matches, false);
                }
            });
        } catch (e) {
            this.apply(false, false);
        }
    },

    apply(dark, persist = true) {
        document.documentElement.classList.toggle('dark', dark);
        document.dispatchEvent(new CustomEvent('ww:theme-changed', {
            detail: { dark }
        }));
        if (persist) {
            try {
                localStorage.setItem(this.STORAGE_KEY, dark ? 'dark' : 'light');
            } catch (e) {}
        }
    },

    toggle() {
        const next = !document.documentElement.classList.contains('dark');
        this.apply(next);
    },

    current() {
        return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    },
};

window.WWTheme = Theme;

Theme.init();