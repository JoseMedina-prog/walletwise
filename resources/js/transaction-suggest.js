/**
 * WalletWise · Sugerencia automática de categoría
 *
 * Mientras el usuario escribe en el campo "description", busca en sus
 * transacciones previas y sugiere categorías ordenadas por frecuencia.
 *
 * - Debounce 300ms para no martillar el servidor
 * - Mínimo 3 caracteres
 * - Click o Enter acepta la sugerencia
 * - Esc cierra el dropdown
 */
(function () {
    'use strict';

    const input = document.getElementById('description');
    const typeSelect = document.getElementById('type');
    const categorySelect = document.getElementById('category_id');
    if (!input || !categorySelect) return;

    // Crear contenedor de sugerencias justo después del input (después de su wrapper)
    const wrap = input.closest('div') || input.parentElement;
    if (!wrap) return;

    const dropdown = document.createElement('div');
    dropdown.id = 'ww-suggest-dropdown';
    dropdown.className = 'hidden mt-1.5 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg overflow-hidden z-20';
    dropdown.setAttribute('role', 'listbox');
    wrap.appendChild(dropdown);

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const url = '/transactions/suggest-category';

    let timer = null;
    let lastTerm = '';
    let current = []; // [{id, name, type, match_count}]
    let activeIdx = -1;

    const close = () => {
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
        activeIdx = -1;
    };

    const render = (suggestions) => {
        current = suggestions;
        activeIdx = -1;

        if (suggestions.length === 0) {
            dropdown.innerHTML = '<div class="px-3.5 py-2.5 text-xs text-slate-500 dark:text-slate-400">Sin sugerencias para ese texto.</div>';
            dropdown.classList.remove('hidden');
            return;
        }

        dropdown.innerHTML = suggestions.map((s, i) => {
            const typeColor = s.type === 'income' ? 'text-income-600 dark:text-income-400' : 'text-expense-600 dark:text-expense-400';
            const dot = s.type === 'income' ? 'bg-income-500' : 'bg-expense-500';
            return `
                <button type="button" role="option" data-idx="${i}" data-id="${s.id}"
                        class="ww-suggest-item w-full flex items-center gap-2.5 px-3.5 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                    <span class="w-1.5 h-1.5 rounded-full ${dot} flex-shrink-0"></span>
                    <span class="font-semibold text-slate-800 dark:text-slate-100">${escapeHtml(s.name)}</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider ${typeColor}">${s.type === 'income' ? 'ingreso' : 'gasto'}</span>
                    <span class="ms-auto text-[11px] text-slate-400 dark:text-slate-500 num">${s.match_count} ${s.match_count === 1 ? 'vez' : 'veces'}</span>
                </button>
            `;
        }).join('');

        dropdown.classList.remove('hidden');
    };

    const escapeHtml = (str) => str.replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    const fetchSuggestions = async (term) => {
        try {
            const res = await fetch(url + '?q=' + encodeURIComponent(term) + (typeSelect && typeSelect.value ? '&type=' + typeSelect.value : ''), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf || '',
                },
                credentials: 'same-origin',
            });
            if (!res.ok) {
                close();
                return;
            }
            const data = await res.json();
            render(data.suggestions || []);
        } catch (e) {
            close();
        }
    };

    const accept = (idx) => {
        const s = current[idx];
        if (!s) return;
        // Seleccionar la categoría en el <select>
        for (const opt of categorySelect.options) {
            if (opt.value === String(s.id)) {
                opt.selected = true;
                categorySelect.dispatchEvent(new Event('change', { bubbles: true }));
                break;
            }
        }
        close();
        // Pequeño feedback visual
        categorySelect.classList.add('ring-2', 'ring-brand-500/40');
        setTimeout(() => categorySelect.classList.remove('ring-2', 'ring-brand-500/40'), 600);
    };

    // Debounce del input
    input.addEventListener('input', () => {
        const term = input.value.trim();
        if (timer) clearTimeout(timer);
        if (term.length < 3) {
            close();
            return;
        }
        if (term === lastTerm) return;
        lastTerm = term;
        timer = setTimeout(() => fetchSuggestions(term), 300);
    });

    // Cuando cambia el tipo, re-buscar con el término actual
    if (typeSelect) {
        typeSelect.addEventListener('change', () => {
            const term = input.value.trim();
            if (term.length >= 3) {
                fetchSuggestions(term);
            }
        });
    }

    // Click en una sugerencia
    dropdown.addEventListener('click', (e) => {
        const btn = e.target.closest('.ww-suggest-item');
        if (!btn) return;
        accept(parseInt(btn.dataset.idx, 10));
    });

    // Teclado: ↓ ↑ Enter Esc
    input.addEventListener('keydown', (e) => {
        if (dropdown.classList.contains('hidden')) return;
        const items = dropdown.querySelectorAll('.ww-suggest-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIdx = (activeIdx + 1) % items.length;
            updateActive(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIdx = (activeIdx - 1 + items.length) % items.length;
            updateActive(items);
        } else if (e.key === 'Enter') {
            if (activeIdx >= 0) {
                e.preventDefault();
                accept(activeIdx);
            }
        } else if (e.key === 'Escape') {
            close();
        }
    });

    const updateActive = (items) => {
        items.forEach((el, i) => {
            el.classList.toggle('bg-slate-50', i === activeIdx);
            el.classList.toggle('dark:bg-slate-700/50', i === activeIdx);
        });
    };

    // Cerrar al hacer click fuera
    document.addEventListener('click', (e) => {
        if (!wrap.contains(e.target)) close();
    });
})();
