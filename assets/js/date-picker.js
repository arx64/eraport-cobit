/**
 * Date Picker dengan Indikator Data
 * Sistem Analisis Risiko TI e-Raport - COBIT 2019
 *
 * Fungsi initDatePickerWithData() membungkus Flatpickr dan menambahkan:
 *   - Highlight biru + dot di tanggal yang punya data penilaian/evaluasi
 *   - Bootstrap tooltip on hover (jumlah penilaian, evaluasi, responden)
 *   - Auto submit form saat tanggal dipilih
 *   - Optional: AJAX refresh dataset sebelum render
 *
 * Cara pakai di view:
 *   const datesData = <?= json_encode($datesWithData) ?>;
 *   initDatePickerWithData('#tanggal', datesData, {
 *       formId: 'dateFilterForm',
 *       defaultDate: '<?= $tanggal ?>',
 *       onChange: (dateStr) => { /* custom *\/ }
 *   });
 */
(function (global) {
    'use strict';

    function pad2(n) {
        return n < 10 ? '0' + n : '' + n;
    }

    function toIsoDate(d) {
        return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate());
    }

    /**
     * Inisialisasi Flatpickr dengan indikator tanggal berisi data.
     *
     * @param {string|HTMLElement} selector  Selector atau element input.
     * @param {Array}             datesData Array of { tanggal, total_penilaian, total_evaluasi, total_responden }.
     * @param {Object}            options   {
     *     formId:       'dateFilterForm',  // optional: id form untuk auto submit
     *     defaultDate:  'YYYY-MM-DD',      // optional: tanggal default dipilih
     *     ajaxUrl:      '/api/dates-with-data', // optional: refresh data via AJAX
     *     onChange:     fn(dateStr)        // optional: callback kustom
     * }
     * @returns {Object} Instance Flatpickr
     */
    function initDatePickerWithData(selector, datesData, options) {
        options = options || {};

        const input = typeof selector === 'string'
            ? document.querySelector(selector)
            : selector;

        if (!input) {
            console.warn('[date-picker] input tidak ditemukan:', selector);
            return null;
        }

        // Bangun map tanggal -> info untuk lookup cepat
        const dateMap = buildDateMap(datesData || []);

        const enableDates = Object.keys(dateMap);

        const fp = flatpickr(input, {
            dateFormat: 'Y-m-d',
            defaultDate: options.defaultDate || input.value || null,
            enable: enableDates,
            locale: 'id',
            allowInput: false,
            disableMobile: false,
            onReady: function (selectedDates, dateStr, instance) {
                attachDayMarkers(instance, dateMap);
            },
            onMonthChange: function () {
                // Re-attach markers saat ganti bulan
                setTimeout(function () {
                    if (window.__dp_currentDateMap) {
                        attachDayMarkers(fp, window.__dp_currentDateMap);
                    }
                }, 0);
            },
            onYearChange: function () {
                setTimeout(function () {
                    if (window.__dp_currentDateMap) {
                        attachDayMarkers(fp, window.__dp_currentDateMap);
                    }
                }, 0);
            },
            onChange: function (selectedDates, dateStr, instance) {
                if (typeof options.onChange === 'function') {
                    options.onChange(dateStr, instance);
                } else if (options.formId) {
                    const form = document.getElementById(options.formId);
                    if (form) form.submit();
                }
            }
        });

        // Cache dateMap untuk re-attach saat ganti bulan
        window.__dp_currentDateMap = dateMap;

        // Optional: refresh data via AJAX
        if (options.ajaxUrl) {
            fetch(options.ajaxUrl, { credentials: 'same-origin' })
                .then(function (r) { return r.json(); })
                .then(function (fresh) {
                    const newMap = buildDateMap(fresh || []);
                    window.__dp_currentDateMap = newMap;
                    fp.set('enable', Object.keys(newMap));
                    fp.redraw();
                })
                .catch(function (err) {
                    console.warn('[date-picker] gagal refresh dataset:', err);
                });
        }

        return fp;
    }

    function buildDateMap(arr) {
        const map = {};
        arr.forEach(function (d) {
            if (!d || !d.tanggal) return;
            map[d.tanggal] = {
                tanggal: d.tanggal,
                total_penilaian: parseInt(d.total_penilaian || 0, 10),
                total_evaluasi: parseInt(d.total_evaluasi || 0, 10),
                total_responden: parseInt(d.total_responden || 0, 10)
            };
        });
        return map;
    }

    function attachDayMarkers(fp, dateMap) {
        if (!fp || !fp.daysContainer) return;
        const days = fp.daysContainer.querySelectorAll('.flatpickr-day');
        days.forEach(function (dayElem) {
            const dateObj = dayElem.dateObj;
            if (!dateObj) return;
            const iso = toIsoDate(dateObj);
            const info = dateMap[iso];
            // Bersihkan marker lama
            dayElem.classList.remove('has-data');
            dayElem.removeAttribute('data-bs-toggle');
            dayElem.removeAttribute('data-bs-placement');
            dayElem.removeAttribute('title');
            dayElem.removeAttribute('data-bs-original-title');
            if (info) {
                dayElem.classList.add('has-data');
                dayElem.setAttribute('data-bs-toggle', 'tooltip');
                dayElem.setAttribute('data-bs-placement', 'top');
                const tipText = formatTooltip(info);
                dayElem.setAttribute('title', tipText);
            }
        });
        // Init tooltip untuk marker yang baru di-attach
        if (window.bootstrap && bootstrap.Tooltip) {
            const tipElems = fp.daysContainer.querySelectorAll('[data-bs-toggle="tooltip"]');
            tipElems.forEach(function (el) {
                const inst = bootstrap.Tooltip.getInstance(el);
                if (!inst) new bootstrap.Tooltip(el, { html: true, trigger: 'hover' });
            });
        }
    }

    function formatTooltip(info) {
        const parts = [];
        parts.push('<strong>' + info.total_penilaian + '</strong> Penilaian');
        parts.push('<strong>' + info.total_evaluasi + '</strong> Hasil Evaluasi');
        parts.push('<strong>' + info.total_responden + '</strong> Responden');
        return parts.join(' &middot; ');
    }

    global.initDatePickerWithData = initDatePickerWithData;
})(window);
