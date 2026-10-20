@include('alerts')
<link rel="stylesheet" href="{{ asset('css/thera-brand.css') }}">
<script src="https://cdn.tailwindcss.com{{ ($useFormPlugin ?? true) ? '?plugins=forms,container-queries' : '' }}"></script>
<style>
    html:not(.material-symbols-ready) .material-symbols-outlined {
        display: inline-block;
        width: 1em;
        max-width: 1em;
        overflow: hidden;
        color: transparent !important;
        visibility: hidden;
        white-space: nowrap;
    }
</style>
<script>
    (() => {
        const revealMaterialSymbols = () => {
            document.documentElement.classList.add('material-symbols-ready');
        };

        if (!document.fonts?.load) {
            window.addEventListener('load', revealMaterialSymbols, { once: true });
            return;
        }

        document.fonts
            .load('24px "Material Symbols Outlined"')
            .then(revealMaterialSymbols)
            .catch(() => {});
    })();
</script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    primary: {
                        DEFAULT: '#0495F6',
                        hover: '#0466F1',
                        active: '#1035DB',
                    },
                    brand: {
                        DEFAULT: '#0495F6',
                        hover: '#0466F1',
                        active: '#1035DB',
                        light: '#E6F4FE',
                        dark: '#0376C5',
                        50: '#F0F9FF',
                        100: '#E0F2FE',
                    },
                    navy: {
                        DEFAULT: '#16243E',
                        dark: '#132038',
                        surface: '#0E1726',
                    },
                    sidebar: {
                        DEFAULT: '#16243E',
                        darker: '#132038',
                        hover: '#203254',
                        border: '#223456',
                    },
                    surface: {
                        sidebar: '#16243E',
                        sidebarDark: '#132038',
                        page: '#F6F9FC',
                        card: '#FFFFFF',
                        border: '#E2E8F0',
                        input: '#F8FAFC',
                    },
                    bodyBg: '#F6F9FC',
                    softSurface: '#F8FAFC',
                    textMain: '#0B1020',
                    textSecondary: '#64748B',
                    textMuted: '#94A3B8',
                    borderLight: '#E2E8F0',
                    'sidebar-bg': '#16243E',
                    'sidebar-surface': '#132038',
                },
                fontFamily: {
                    sans: ['Tajawal', 'sans-serif'],
                    inter: ['Inter', 'sans-serif'],
                    mono: ['Inter', 'monospace'],
                    arabic: ['Tajawal', 'sans-serif'],
                },
                boxShadow: {
                    soft: '0 2px 10px rgba(0, 0, 0, 0.04)',
                    dropdown: '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05)',
                    drawer: '-10px 0 30px rgba(0, 0, 0, 0.12)',
                },
            },
        },
    };
</script>
