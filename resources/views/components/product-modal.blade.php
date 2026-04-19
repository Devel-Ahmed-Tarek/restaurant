<!-- Product Modal — mobile: bottom sheet with scroll; desktop: side-by-side -->
<div x-data="productModal()"
     x-show="isOpen"
     x-cloak
     @open-product.window="openProduct($event.detail)"
     class="fixed inset-0 z-50 flex items-end justify-center lg:items-center p-0 lg:p-4"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-[2px]" @click="closeModal()" aria-hidden="true"></div>

    <!-- Panel: max height + flex column — min-h-0 enables inner scroll on iOS/Android -->
    <div class="relative z-10 w-full max-w-lg lg:max-w-2xl mx-auto flex flex-col
                max-h-[min(92dvh,100%)] lg:max-h-[min(88dvh,900px)]
                min-h-0 overflow-hidden
                bg-white rounded-t-[1.75rem] lg:rounded-2xl shadow-2xl ring-1 ring-black/5"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full lg:translate-y-4 lg:scale-95 opacity-90"
         x-transition:enter-end="translate-y-0 lg:scale-100 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full lg:translate-y-0 opacity-90">

        <!-- Mobile sheet handle -->
        <div class="lg:hidden flex justify-center pt-3 pb-1 flex-shrink-0" aria-hidden="true">
            <span class="h-1.5 w-10 rounded-full bg-gray-200/90"></span>
        </div>

        <!-- Loading -->
        <div x-show="loading" class="p-10 text-center flex-shrink-0">
            <div class="animate-spin w-9 h-9 border-[3px] border-primary-500 border-t-transparent rounded-full mx-auto"></div>
            <p class="text-gray-500 mt-4 text-sm">{{ __('Loading...') }}</p>
        </div>

        <!-- Product body -->
        <div x-show="!loading && product" class="flex flex-col lg:flex-row flex-1 min-h-0 w-full">

            <!-- Image -->
            <div class="relative h-[38vw] max-h-44 min-h-[140px] sm:h-44 lg:min-h-0 lg:h-auto lg:w-72 xl:w-80
                        bg-gradient-to-b from-gray-50 to-gray-100 flex-shrink-0 lg:max-h-[min(88dvh,900px)]">
                <img :src="product?.image || '{{ asset("images/placeholder.svg") }}'"
                     :alt="product?.name"
                     class="w-full h-full object-cover">
                <button type="button"
                        @click="closeModal()"
                        class="absolute top-3 right-3 w-10 h-10 bg-white/95 backdrop-blur rounded-full flex items-center justify-center shadow-md hover:bg-white transition-colors ring-1 ring-black/5">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div class="absolute bottom-3 left-3 right-3 flex flex-wrap gap-1.5">
                    <template x-for="(tag, tagIdx) in (product?.tags || [])" :key="'tag-' + tagIdx + '-' + tag">
                        <span class="bg-white/95 backdrop-blur text-gray-800 text-[11px] font-medium px-2.5 py-1 rounded-full capitalize shadow-sm" x-text="tag.replace('_', ' ')"></span>
                    </template>
                </div>
            </div>

            <!-- Options column: scroll + sticky footer -->
            <div class="flex flex-col flex-1 min-h-0 min-w-0 lg:min-h-[min(400px,85vh)]">

                <!-- Scrollable options (critical: min-h-0 + overflow-y-auto + touch) -->
                <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain touch-pan-y px-4 pt-2 pb-3 lg:pt-4 lg:pb-4
                            [-webkit-overflow-scrolling:touch] space-y-5">

                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 leading-snug" x-text="product?.name"></h2>
                            <p class="text-gray-500 text-sm mt-1 line-clamp-3" x-text="product?.description"></p>
                        </div>
                        <div class="text-right flex-shrink-0 pl-2">
                            <template x-if="product?.old_price">
                                <p class="text-gray-400 text-xs line-through" x-text="formatMoney(parseFloat(product?.old_price))"></p>
                            </template>
                            <p class="text-primary-600 text-lg font-bold tabular-nums" x-text="formatMoney(parseFloat(product?.base_price))"></p>
                        </div>
                    </div>

                    <!-- Sizes -->
                    <template x-if="product?.sizes?.length > 0">
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Size') }}</h3>
                                <span class="text-[10px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">{{ __('Select any of 1') }}</span>
                            </div>
                            <div class="space-y-2">
                                <template x-for="size in product.sizes" :key="size.id">
                                    <label class="flex items-center justify-between gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all duration-150 active:scale-[0.99]"
                                           :class="selectedSize?.id === size.id
                                               ? 'border-primary-500 bg-primary-50/80 shadow-sm ring-1 ring-primary-500/20'
                                               : 'border-gray-100 bg-gray-50/50 hover:border-gray-200 hover:bg-white'">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <input type="radio" name="size" :value="size.id" x-model="selectedSizeId" @change="selectSize(size)"
                                                   class="w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500 shrink-0">
                                            <span class="text-gray-900 font-medium text-sm truncate" x-text="size.name"></span>
                                        </div>
                                        <span class="text-primary-600 text-sm font-semibold tabular-nums shrink-0" x-text="formatMoneyModifier(size.price_modifier)"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Toppings -->
                    <template x-if="product?.toppings?.length > 0">
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Toppings') }}</h3>
                                <span class="text-[10px] font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ __('Optional') }}</span>
                            </div>
                            <div class="space-y-2">
                                <template x-for="topping in product.toppings" :key="topping.id">
                                    <label class="flex items-center justify-between gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all duration-150 active:scale-[0.99]"
                                           :class="selectedToppingIds.includes(topping.id)
                                               ? 'border-primary-500 bg-primary-50/80 shadow-sm ring-1 ring-primary-500/20'
                                               : 'border-gray-100 bg-gray-50/50 hover:border-gray-200 hover:bg-white'">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <input type="checkbox" :value="topping.id" x-model="selectedToppingIds" @change="toggleTopping(topping)"
                                                   class="w-4 h-4 rounded-md text-primary-600 border-gray-300 focus:ring-primary-500 shrink-0">
                                            <span class="text-gray-900 font-medium text-sm truncate" x-text="topping.name"></span>
                                            <template x-if="topping.is_required">
                                                <span class="text-[10px] text-red-500 font-medium shrink-0">*{{ __('Required') }}</span>
                                            </template>
                                        </div>
                                        <span class="text-primary-600 text-sm font-semibold tabular-nums shrink-0" x-text="formatMoneyPlus(parseFloat(topping.price))"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer: always visible, safe-area for notched phones -->
                <div class="flex-shrink-0 border-t border-gray-100 bg-white/95 backdrop-blur-md px-4 pt-3
                            pb-[max(0.875rem,env(safe-area-inset-bottom))]
                            shadow-[0_-8px_30px_rgba(0,0,0,0.06)]">
                    <div class="flex items-stretch gap-3">
                        <div class="flex items-center gap-2 bg-gray-100/90 rounded-2xl px-2 py-1.5 shrink-0">
                            <button type="button" @click="decreaseQuantity()"
                                    class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-gray-200/80 transition-colors">
                                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <span class="w-8 text-center font-bold text-gray-900 tabular-nums" x-text="quantity"></span>
                            <button type="button" @click="increaseQuantity()"
                                    class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-gray-200/80 transition-colors">
                                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                        <button type="button" @click="addToCart()"
                                class="flex-1 min-w-0 bg-primary-500 hover:bg-primary-600 active:bg-primary-700 text-white font-semibold py-3.5 px-4 rounded-2xl transition-colors text-sm shadow-lg shadow-primary-500/25">
                            <span class="block truncate">{{ __('Add to Cart') }}</span>
                            <span class="block text-xs font-bold opacity-95 mt-0.5 tabular-nums" x-text="formatMoney(totalPrice)"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function customerApiProductUrl(productId) {
    const pathParts = window.location.pathname.split('/').filter(Boolean);
    const localeIndex = pathParts.findIndex((p) => p === 'en' || p === 'de');
    const prefix = localeIndex >= 0 ? '/' + pathParts.slice(0, localeIndex + 1).join('/') : '/{{ app()->getLocale() }}';
    return window.location.origin + prefix + '/api/products/' + productId;
}

function productModal() {
    return {
        isOpen: false,
        loading: false,
        product: null,
        quantity: 1,
        selectedSize: null,
        selectedSizeId: null,
        selectedToppings: [],
        selectedToppingIds: [],

        get totalPrice() {
            if (!this.product) return 0;

            let price = parseFloat(this.product.base_price);

            if (this.selectedSize) {
                price += parseFloat(this.selectedSize.price_modifier || 0);
            }

            this.selectedToppings.forEach(topping => {
                price += parseFloat(topping.price || 0);
            });

            return price * this.quantity;
        },

        formatMoney(n) {
            if (typeof window.formatMoney === 'function') {
                return window.formatMoney(n);
            }
            const sym = window.APP_CURRENCY || 'EGP';
            return sym + ' ' + Number(n).toFixed(2);
        },

        formatMoneyModifier(n) {
            if (typeof window.formatMoneyModifier === 'function') {
                return window.formatMoneyModifier(n);
            }
            const v = Number(n);
            const sym = window.APP_CURRENCY || 'EGP';
            return (v >= 0 ? '+ ' : '- ') + sym + ' ' + Math.abs(v).toFixed(2);
        },

        formatMoneyPlus(n) {
            if (typeof window.formatMoneyPlus === 'function') {
                return window.formatMoneyPlus(n);
            }
            const sym = window.APP_CURRENCY || 'EGP';
            return '+ ' + sym + ' ' + Number(n).toFixed(2);
        },

        async openProduct(productId) {
            this.isOpen = true;
            this.loading = true;
            this.resetSelections();

            try {
                const apiUrl = customerApiProductUrl(productId);
                const response = await fetch(apiUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!response.ok) {
                    throw new Error('Product request failed: ' + response.status);
                }
                this.product = await response.json();

                if (this.product.sizes?.length > 0) {
                    this.selectSize(this.product.sizes[0]);
                }
            } catch (error) {
                console.error('Error loading product:', error);
                this.product = null;
            } finally {
                this.loading = false;
            }
        },

        closeModal() {
            this.isOpen = false;
            this.product = null;
            this.resetSelections();
        },

        resetSelections() {
            this.quantity = 1;
            this.selectedSize = null;
            this.selectedSizeId = null;
            this.selectedToppings = [];
            this.selectedToppingIds = [];
        },

        selectSize(size) {
            this.selectedSize = size;
            this.selectedSizeId = size.id;
        },

        toggleTopping(topping) {
            const index = this.selectedToppings.findIndex(t => t.id === topping.id);
            if (index > -1) {
                this.selectedToppings.splice(index, 1);
            } else {
                this.selectedToppings.push(topping);
            }
        },

        increaseQuantity() {
            this.quantity++;
        },

        decreaseQuantity() {
            if (this.quantity > 1) {
                this.quantity--;
            }
        },

        addToCart() {
            if (!this.product || !this.product.id) {
                return;
            }
            Alpine.store('cart').add(
                this.product,
                this.quantity,
                this.selectedSize,
                this.selectedToppings
            );

            this.closeModal();
        }
    }
}
</script>
@endpush
