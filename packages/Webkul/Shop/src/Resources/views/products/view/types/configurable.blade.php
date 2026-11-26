@if (Webkul\Product\Helpers\ProductType::hasVariants($product->type))
    {!! view_render_event('bagisto.shop.products.view.configurable-options.before', ['product' => $product]) !!}

    <v-product-configurable-options :errors="errors"></v-product-configurable-options>

    {!! view_render_event('bagisto.shop.products.view.configurable-options.after', ['product' => $product]) !!}

    @push('scripts')
        <script
            type="text/x-template"
            id="v-product-configurable-options-template"
        >
            <div class="w-full max-w-[520px]">
                <input
                    type="hidden"
                    name="selected_configurable_option"
                    id="selected_configurable_option"
                    :value="selectedOptionVariant"
                    ref="selected_configurable_option"
                >

                <div
                    class="mt-6"
                    v-for='(attribute, index) in childAttributes'
                    :key="attribute.id"
                >
                    <!-- Header with Label and Selected Value -->
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-sm font-bold uppercase tracking-wider text-slate-900">
                            @{{ attribute.label }}:
                            <span class="font-semibold text-slate-600 normal-case ml-1">
                                @{{ getSelectedOptionLabel(attribute) }}
                            </span>
                        </span>

                        <span
                            class="text-xs font-medium text-slate-400"
                            v-if="! attribute.selectedValue"
                        >
                            Select an option
                        </span>
                    </div>

                    <!-- 1. Color Swatch Options -->
                    <div
                        class="flex flex-wrap items-center gap-3"
                        v-if="isColorAttribute(attribute)"
                    >
                        <template v-for="(option, optIdx) in attribute.options">
                            <label
                                class="relative flex h-10 w-10 cursor-pointer items-center justify-center rounded-full p-0.5 transition-all duration-200 focus:outline-none"
                                :class="[
                                    option.id == attribute.selectedValue
                                        ? 'ring-2 ring-slate-900 ring-offset-2 scale-110 shadow-sm'
                                        : 'hover:scale-105 opacity-85 hover:opacity-100'
                                ]"
                                :title="option.label"
                                v-if="option.id"
                            >
                                <v-field
                                    type="radio"
                                    :name="'super_attribute[' + attribute.id + ']'"
                                    :value="option.id"
                                    v-model="attribute.selectedValue"
                                    v-slot="{ field }"
                                    rules="required"
                                    :label="attribute.label"
                                    :aria-label="attribute.label"
                                >
                                    <input
                                        type="radio"
                                        :name="'super_attribute[' + attribute.id + ']'"
                                        :value="option.id"
                                        v-bind="field"
                                        :id="'attribute_' + attribute.id + '_' + option.id"
                                        class="peer sr-only"
                                        @click="configure(attribute, option.id)"
                                    />
                                </v-field>

                                <span
                                    class="h-full w-full rounded-full border border-black/15 shadow-inner"
                                    :style="{ 'background-color': getColorValue(option) }"
                                ></span>
                            </label>
                        </template>

                        <span
                            class="text-xs text-slate-500"
                            v-if="! attribute.options.length"
                        >
                            @lang('shop::app.products.view.type.configurable.select-above-options')
                        </span>
                    </div>

                    <!-- 2. Image Swatch Options -->
                    <div
                        class="flex flex-wrap items-center gap-2.5"
                        v-else-if="attribute.swatch_type == 'image'"
                    >
                        <template v-for="(option, optIdx) in attribute.options">
                            <label 
                                class="group relative flex h-14 w-14 cursor-pointer items-center justify-center overflow-hidden rounded-xl border-2 bg-white transition hover:border-slate-400"
                                :class="option.id == attribute.selectedValue ? 'border-slate-900 ring-2 ring-slate-900/20 shadow-sm' : 'border-slate-200'"
                                :title="option.label"
                                v-if="option.id"
                            >
                                <v-field
                                    type="radio"
                                    :name="'super_attribute[' + attribute.id + ']'"
                                    v-model="attribute.selectedValue"
                                    :value="option.id"
                                    v-slot="{ field }"
                                    rules="required"
                                    :label="attribute.label"
                                    :aria-label="attribute.label"
                                >
                                    <input
                                        type="radio"
                                        :name="'super_attribute[' + attribute.id + ']'"
                                        :value="option.id"
                                        v-bind="field"
                                        :id="'attribute_' + attribute.id + '_' + option.id"
                                        class="peer sr-only"
                                        @click="configure(attribute, option.id)"
                                    />
                                </v-field>

                                <img
                                    class="h-full w-full object-cover"
                                    :src="option.swatch_value"
                                    :title="option.label"
                                    :alt="option.swatch_alt || option.label"
                                />
                            </label>
                        </template>
                    </div>

                    <!-- 3. Size / Text / Button Options (Default for all other attributes, NO DROPDOWNS!) -->
                    <div
                        class="flex flex-wrap items-center gap-2.5"
                        v-else
                    >
                        <template v-for="(option, optIdx) in attribute.options">
                            <label 
                                class="group relative flex min-w-[52px] h-[46px] px-4 cursor-pointer items-center justify-center rounded-xl border text-sm font-bold uppercase transition-all duration-200 select-none"
                                :class="[
                                    option.id == attribute.selectedValue
                                        ? '!bg-slate-900 !border-slate-900 !text-white shadow-md ring-2 ring-slate-900/25'
                                        : 'bg-white border-slate-300 text-slate-800 hover:border-slate-900 hover:bg-slate-50'
                                ]"
                                :title="option.label"
                                v-if="option.id"
                            >
                                <v-field
                                    type="radio"
                                    :name="'super_attribute[' + attribute.id + ']'"
                                    :value="option.id"
                                    v-model="attribute.selectedValue"
                                    v-slot="{ field }"
                                    rules="required"
                                    :label="attribute.label"
                                    :aria-label="attribute.label"
                                >
                                    <input
                                        type="radio"
                                        :name="'super_attribute[' + attribute.id + ']'"
                                        :value="option.id"
                                        v-bind="field"
                                        :id="'attribute_' + attribute.id + '_' + option.id"
                                        class="peer sr-only"
                                        @click="configure(attribute, option.id)"
                                    />
                                </v-field>

                                <span class="tracking-wide">
                                    @{{ option.label }}
                                </span>
                            </label>
                        </template>

                        <span
                            class="text-xs text-slate-500"
                            v-if="! attribute.options.length"
                        >
                            @lang('shop::app.products.view.type.configurable.select-above-options')
                        </span>
                    </div>

                    <v-error-message
                        :name="'super_attribute[' + attribute.id + ']'"
                        v-slot="{ message }"
                    >
                        <p class="mt-1.5 text-xs font-medium text-red-500">
                            @{{ message }}
                        </p>
                    </v-error-message>
                </div>
            </div>
        </script>
        </script>

        <script type="module">
            let galleryImages = @json(product_image()->getGalleryImages($product));

            app.component('v-product-configurable-options', {
                template: '#v-product-configurable-options-template',

                props: ['errors'],

                data() {
                    return {
                        config: @json(app('Webkul\Product\Helpers\ConfigurableOption')->getConfigurationConfig($product)),

                        childAttributes: [],

                        possibleOptionVariant: null,

                        selectedOptionVariant: '',

                        galleryImages: [],
                    }
                },

                mounted() {
                    let attributes = JSON.parse(JSON.stringify(this.config)).attributes.slice();

                    let index = attributes.length;

                    while (index--) {
                        let attribute = attributes[index];

                        attribute.options = [];

                        if (index) {
                            attribute.disabled = true;
                        } else {
                            this.fillAttributeOptions(attribute);
                        }

                        attribute = Object.assign(attribute, {
                            childAttributes: this.childAttributes.slice(),
                            prevAttribute: attributes[index - 1],
                            nextAttribute: attributes[index + 1]
                        });

                        this.childAttributes.unshift(attribute);
                    }
                },

                methods: {
                    isColorAttribute(attribute) {
                        if (attribute.swatch_type === 'color') return true;
                        let code = (attribute.code || '').toLowerCase();
                        let label = (attribute.label || '').toLowerCase();
                        return code.includes('color') || label.includes('color') || code.includes('colour') || label.includes('colour');
                    },

                    getColorValue(option) {
                        if (option.swatch_value) return option.swatch_value;
                        return option.label ? option.label.toLowerCase() : '#e2e8f0';
                    },

                    getSelectedOptionLabel(attribute) {
                        if (! attribute.selectedValue) return '';
                        let opt = attribute.options.find(o => o.id == attribute.selectedValue);
                        return opt ? opt.label : '';
                    },

                    configure(attribute, optionId) {
                        this.possibleOptionVariant = this.getPossibleOptionVariant(attribute, optionId);

                        if (optionId) {
                            attribute.selectedValue = optionId;
                            
                            if (attribute.nextAttribute) {
                                attribute.nextAttribute.disabled = false;

                                this.clearAttributeSelection(attribute.nextAttribute);

                                this.fillAttributeOptions(attribute.nextAttribute);

                                this.resetChildAttributes(attribute.nextAttribute);
                            } else {
                                this.selectedOptionVariant = this.possibleOptionVariant;
                            }
                        } else {
                            this.clearAttributeSelection(attribute);

                            this.clearAttributeSelection(attribute.nextAttribute);

                            this.resetChildAttributes(attribute);
                        }

                        this.reloadPrice();
                        
                        this.reloadImages();
                    },

                    getPossibleOptionVariant(attribute, optionId) {
                        let matchedOptions = attribute.options.filter(option => option.id == optionId);

                        if (matchedOptions[0]?.allowedProducts) {
                            return matchedOptions[0].allowedProducts[0];
                        }

                        return undefined;
                    },

                    fillAttributeOptions(attribute) {
                        let options = this.config.attributes.find(tempAttribute => tempAttribute.id === attribute.id)?.options;

                        attribute.options = [{
                            'id': '',
                            'label': "@lang('shop::app.products.view.type.configurable.select-options')",
                            'products': []
                        }];

                        if (! options) {
                            return;
                        }

                        let prevAttributeSelectedOption = attribute.prevAttribute?.options.find(option => option.id == attribute.prevAttribute.selectedValue);

                        let index = 1;

                        for (let i = 0; i < options.length; i++) {
                            let allowedProducts = [];

                            if (prevAttributeSelectedOption) {
                                for (let j = 0; j < options[i].products.length; j++) {
                                    if (prevAttributeSelectedOption.allowedProducts && prevAttributeSelectedOption.allowedProducts.includes(options[i].products[j])) {
                                        allowedProducts.push(options[i].products[j]);
                                    }
                                }
                            } else {
                                allowedProducts = options[i].products.slice(0);
                            }

                            if (allowedProducts.length > 0) {
                                options[i].allowedProducts = allowedProducts;

                                attribute.options[index++] = options[i];
                            }
                        }
                    },

                    resetChildAttributes(attribute) {
                        if (! attribute.childAttributes) {
                            return;
                        }

                        attribute.childAttributes.forEach(function (set) {
                            set.selectedValue = null;

                            set.disabled = true;
                        });
                    },

                    clearAttributeSelection (attribute) {
                        if (! attribute) {
                            return;
                        }

                        attribute.selectedValue = null;

                        this.selectedOptionVariant = null;
                    },

                    reloadPrice () {
                        let selectedOptionCount = this.childAttributes.filter(attribute => attribute.selectedValue).length;

                        let finalPrice = document.querySelector('.final-price');

                        let regularPrice = document.querySelector('.regular-price');

                        let configVariant = this.config.variant_prices[this.possibleOptionVariant];

                        if (this.childAttributes.length == selectedOptionCount) {
                            document.querySelector('.price-label').style.display = 'none';

                            if (parseFloat(configVariant.regular.price) > parseFloat(configVariant.final.price)) {
                                regularPrice.style.display = 'block';

                                finalPrice.innerHTML = configVariant.final.formatted_price;

                                regularPrice.innerHTML = configVariant.regular.formatted_price;
                            } else {
                                finalPrice.innerHTML = configVariant.regular.formatted_price;

                                regularPrice.style.display = 'none';

                                regularPrice.innerHTML = '';
                            }

                            this.$emitter.emit('configurable-variant-selected-event',this.possibleOptionVariant);
                        } else {
                            document.querySelector('.price-label').style.display = 'inline-block';

                            const baseRegular = parseFloat(this.config.regular?.price ?? 0);

                            const baseFinal = parseFloat(this.config.final?.price ?? baseRegular);

                            if (baseFinal < baseRegular) {
                                regularPrice.style.display = 'block';

                                regularPrice.innerHTML = this.config.regular.formatted_price;

                                finalPrice.innerHTML = this.config.final.formatted_price;
                            } else {
                                regularPrice.style.display = 'none';

                                regularPrice.innerHTML = '';

                                finalPrice.innerHTML = this.config.regular.formatted_price;
                            }

                            this.$emitter.emit('configurable-variant-selected-event', 0);
                        }
                    },

                    reloadImages () {
                        galleryImages.splice(0, galleryImages.length)

                        if (this.possibleOptionVariant) {
                            this.config.variant_images[this.possibleOptionVariant].forEach(function(image) {
                                galleryImages.push(image);
                            });

                            this.config.variant_videos[this.possibleOptionVariant].forEach(function(video) {
                                galleryImages.push(video);
                            });
                        }

                        this.galleryImages.forEach(function(image) {
                            galleryImages.push(image);
                        });

                        if (galleryImages.length) {
                            this.$parent.$parent.$refs.gallery.media.images =  [...galleryImages];
                        }

                        this.$emitter.emit('configurable-variant-update-images-event', galleryImages);
                    },
                }
            });

        </script>
    @endpush

@endif