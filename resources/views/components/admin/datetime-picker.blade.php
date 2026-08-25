@props([
    'name' => '',
    'id' => '',
    'value' => '',
    'required' => false,
    'min' => '',
    'placeholder' => 'Pilih tanggal & jam',
])

@php
    $val = (string) $value;
    $minStr = (string) $min;
@endphp

<div
    x-data="{
        open: false,
        val: @js($val),
        display: '',
        viewYear: 0,
        viewMonth: 0,
        hour: '00',
        minute: '00',
        days: [],
        weekdays: ['Sen','Sel','Rab','Kam','Jum','Sab','Min'],
        init() {
            this.syncFromVal();
            this.display = this.formatDisplay(this.val);
            let d = this.val ? new Date(this.val) : new Date();
            if (isNaN(d)) d = new Date();
            this.viewYear = d.getFullYear();
            this.viewMonth = d.getMonth();
            this.build();
            this.$watch('val', v => { this.display = this.formatDisplay(v); this.syncFromVal(); this.$dispatch('date-change', { value: v, name: @js($name) }); });
            this.$watch('viewMonth', () => this.build());
            this.$watch('viewYear', () => this.build());
        },
        syncFromVal() {
            if (!this.val) return;
            const d = new Date(this.val);
            if (isNaN(d)) return;
            this.hour = String(d.getHours()).padStart(2,'0');
            this.minute = String(d.getMinutes()).padStart(2,'0');
        },
        combine(dateStr) {
            if (!dateStr) return;
            const h = this.hour.padStart(2,'0');
            const m = this.minute.padStart(2,'0');
            this.val = `${dateStr}T${h}:${m}`;
            this.display = this.formatDisplay(this.val);
        },
        formatDisplay(v) {
            if (!v) return '';
            const d = new Date(v);
            if (isNaN(d)) return v;
            return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' + String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
        },
        ymd(d) {
            const y = d.getFullYear();
            const m = String(d.getMonth()+1).padStart(2,'0');
            const day = String(d.getDate()).padStart(2,'0');
            return `${y}-${m}-${day}`;
        },
        ymdHi(dateStr) {
            const h = this.hour.padStart(2,'0');
            const m = this.minute.padStart(2,'0');
            return `${dateStr}T${h}:${m}`;
        },
        isDisabled(d) {
            const s = this.ymd(d);
            const min = @js($minStr);
            if (min && s < min.split('T')[0]) return true;
            return false;
        },
        build() {
            const first = new Date(this.viewYear, this.viewMonth, 1);
            let startDay = first.getDay(); startDay = startDay === 0 ? 6 : startDay - 1;
            const daysInMonth = new Date(this.viewYear, this.viewMonth+1, 0).getDate();
            const daysInPrev = new Date(this.viewYear, this.viewMonth, 0).getDate();
            this.days = [];
            for (let i = startDay-1; i >= 0; i--) {
                const d = new Date(this.viewYear, this.viewMonth-1, daysInPrev - i);
                this.days.push({ date: d, str: this.ymd(d), day: d.getDate(), current: false, disabled: this.isDisabled(d) });
            }
            for (let i = 1; i <= daysInMonth; i++) {
                const d = new Date(this.viewYear, this.viewMonth, i);
                this.days.push({ date: d, str: this.ymd(d), day: i, current: true, disabled: this.isDisabled(d) });
            }
            const need = 42 - this.days.length;
            for (let i = 1; i <= need; i++) {
                const d = new Date(this.viewYear, this.viewMonth+1, i);
                this.days.push({ date: d, str: this.ymd(d), day: i, current: false, disabled: this.isDisabled(d) });
            }
        },
        isToday(str) { const t = new Date(); return str === this.ymd(t); },
        datePart() { return this.val ? this.val.split('T')[0] : ''; },
        isSelected(str) { return this.datePart() === str; },
        select(str, disabled) {
            if (disabled) return;
            this.combine(str);
            // keep open for time adjustment
        },
        prev() { if (this.viewMonth === 0) { this.viewMonth = 11; this.viewYear--; } else this.viewMonth--; },
        next() { if (this.viewMonth === 11) { this.viewMonth = 0; this.viewYear++; } else this.viewMonth++; },
        monthLabel() { return new Date(this.viewYear, this.viewMonth, 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }); },
        clear() { this.val = ''; },
        updateTime() { if (this.datePart()) this.combine(this.datePart()); else { const t = new Date(); const s = this.ymd(t); this.viewYear = t.getFullYear(); this.viewMonth = t.getMonth(); this.combine(s); } },
        apply() { this.open = false; }
    }"
    class="relative"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
>
    <input type="hidden" name="{{ $name }}" x-model="val" @if($required) :required="true" @endif />
    @if($id)
        <input type="hidden" id="{{ $id }}-hidden" x-model="val" />
    @endif

    <button type="button" @click="open = !open"
        @if($id) id="{{ $id }}" @endif
        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
        :class="!val && 'text-gray-400 dark:text-white/30'">
        <span class="flex items-center gap-2.5 truncate">
            <svg class="h-4 w-4 shrink-0 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 01 2.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 12.75h.008v.008H12v-.008zm0 0V12h.008v.008H12V12zm0 2.25h.008v.008H12v-.008zm-3-6h.008v.008H9V8.25zm0 2.25h.008v.008H9v-.008zm0 2.25h.008v.008H9v-.008zM7.5 8.25h.008v.008H7.5V8.25zm0 2.25h.008v.008H7.5V10.5zm0 2.25h.008v.008H7.5V12.75zm6.75 0h.008v.008h-.008v-.008zm0 2.25h.008v.008H12v-.008zm2.25-2.25h.008v.008H14.25V10.5zm0 2.25h.008v.008H14.25V12.75zm0 0h.008v.008H14.25V12.75z"/></svg>
            <span x-text="display || @js($placeholder)" class="truncate"></span>
        </span>
        <span class="flex items-center gap-1.5">
            <span x-show="val" @click.stop="clear()" class="flex h-6 w-6 items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-white/10" title="Hapus">
                <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </span>
            <svg class="h-5 w-5 shrink-0 text-gray-500 transition-transform duration-200 dark:text-gray-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </span>
    </button>

    <div x-show="open" x-cloak
        class="absolute left-0 z-50 mt-1 w-[360px] max-w-[calc(100vw-2rem)] overflow-hidden rounded-lg border border-gray-200 bg-white shadow-theme-lg dark:border-gray-700 dark:bg-gray-dark"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1">
        <div class="flex items-center justify-between px-3 py-2.5">
            <button type="button" @click="prev()" class="flex h-8 w-8 items-center justify-center rounded-md hover:bg-gray-100 dark:hover:bg-white/10">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <span class="text-sm font-semibold text-gray-800 dark:text-white/90 capitalize" x-text="monthLabel()"></span>
            <button type="button" @click="next()" class="flex h-8 w-8 items-center justify-center rounded-md hover:bg-gray-100 dark:hover:bg-white/10">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
        <div class="grid grid-cols-7 gap-px px-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400">
            <template x-for="w in weekdays" :key="w"><span class="py-1" x-text="w"></span></template>
        </div>
        <div class="grid grid-cols-7 gap-1.5 p-3">
            <template x-for="d in days" :key="d.str">
                <button type="button"
                    @click="select(d.str, d.disabled)"
                    :disabled="d.disabled"
                    class="flex h-9 w-9 items-center justify-center rounded-md text-sm transition-colors"
                    :class="isSelected(d.str) ? 'bg-brand-500 text-white dark:bg-brand-500' : (d.current ? (isToday(d.str) ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400 font-semibold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5') : 'text-gray-400 dark:text-white/20 hover:bg-gray-50 dark:hover:bg-white/5') + (d.disabled ? ' opacity-30 cursor-not-allowed' : '')"
                    x-text="d.day"></button>
            </template>
        </div>
        <div class="flex items-center gap-2 border-t border-gray-100 bg-gray-50 px-3 py-2.5 dark:border-gray-700 dark:bg-white/[0.03]">
            <div class="flex items-center gap-1.5">
                <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <select x-model="hour" @change="updateTime()" class="h-8 rounded-md border border-gray-300 bg-white px-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <template x-for="h in Array.from({length:24},(_,i)=>String(i).padStart(2,'0'))" :key="h"><option :value="h" x-text="h"></option></template>
                </select>
                <span class="text-gray-500">:</span>
                <select x-model="minute" @change="updateTime()" class="h-8 rounded-md border border-gray-300 bg-white px-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <template x-for="m in Array.from({length:60},(_,i)=>String(i).padStart(2,'0'))" :key="m"><option :value="m" x-text="m"></option></template>
                </select>
            </div>
            <div class="ml-auto flex items-center gap-2">
                <button type="button" @click="clear()" class="text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400">Hapus</button>
                <button type="button" @click="apply()" class="rounded-md bg-brand-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-600">Terapkan</button>
            </div>
        </div>
    </div>
</div>
