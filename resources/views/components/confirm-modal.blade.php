<div
    x-data="{
        show: false,
        title: 'Confirmar Acción',
        message: '¿Estás seguro de realizar esta acción?',
        confirmText: 'Confirmar',
        cancelText: 'Cancelar',
        confirmButtonClass: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
        onConfirm: null,
        onCancel: null,
        
        open(detail) {
            this.title = detail.title || 'Confirmar Acción';
            this.message = detail.message || '¿Estás seguro de realizar esta acción?';
            this.confirmText = detail.confirmText || 'Confirmar';
            this.cancelText = detail.cancelText || 'Cancelar';
            this.confirmButtonClass = detail.confirmButtonClass || 'bg-red-600 hover:bg-red-700 focus:ring-red-500';
            this.onConfirm = detail.onConfirm || null;
            this.onCancel = detail.onCancel || null;
            this.show = true;
        },
        close(isConfirmed = false) {
            if (!isConfirmed && this.onCancel && typeof this.onCancel === 'function') {
                this.onCancel();
            }
            this.show = false;
            this.onConfirm = null;
            this.onCancel = null;
        },
        triggerConfirm() {
            if (this.onConfirm && typeof this.onConfirm === 'function') {
                this.onConfirm();
            }
            this.close(true);
        }
    }"
    x-on:open-confirm-modal.window="open($event.detail)"
    x-on:close-confirm-modal.window="close()"
    x-show="show"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:keydown.escape.window="close()"
    class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center p-4"
    style="display: none;"
    :style="show ? 'display: flex !important;' : 'display: none !important;'"
>
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
        @click="close()"
    ></div>

    <div 
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
        class="relative bg-white rounded-2xl border border-slate-100 shadow-2xl p-6 max-w-md w-full z-10 overflow-hidden"
    >
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-amber-600 flex-shrink-0">
                    <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 tracking-tight" x-text="title">Confirmar Acción</h3>
                </div>
            </div>
            <button type="button" @click="close()" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="mb-6">
            <p class="text-sm text-slate-500 font-medium leading-relaxed" x-text="message"></p>
        </div>
        
        <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
            <button 
                type="button" 
                @click="close()" 
                class="px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-slate-300"
                x-text="cancelText"
            >
                Cancelar
            </button>
            <button 
                type="button" 
                @click="triggerConfirm()" 
                class="px-4 py-2 text-sm font-semibold text-white rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-1"
                :class="confirmButtonClass"
                x-text="confirmText"
            >
                Confirmar
            </button>
        </div>
    </div>
</div>
