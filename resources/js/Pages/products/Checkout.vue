<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({ cartItems: Array, total: Number });

const form = useForm({
    first_name: '', last_name: '', email: '',
    phone: '', payment_method: 'stripe', stripe_token: '',
});

const paymentStatus = ref('idle');
const paymentError  = ref('');
let stripe = null;
let card   = null;
const stripeReady = ref(false);

onMounted(() => {
    const script  = document.createElement('script');
    script.src    = 'https://js.stripe.com/v3/';
    script.onload = () => {
        stripe = window.Stripe(import.meta.env.VITE_STRIPE_KEY);
        const elements = stripe.elements();
        card = elements.create('card', {
            style: { base: { fontSize: '16px', color: '#374151', '::placeholder': { color: '#9CA3AF' } } },
        });
        card.mount('#stripe-card-element');
        card.on('ready',  () => { stripeReady.value = true; });
        card.on('change', (e) => { paymentError.value = e.error ? e.error.message : ''; });
    };
    document.head.appendChild(script);
});

async function submit() {
    paymentError.value  = '';
    paymentStatus.value = 'processing';

    if (form.payment_method === 'stripe') {
        if (!stripe || !card) {
            paymentError.value  = 'Stripe not loaded.';
            paymentStatus.value = 'idle';
            return;
        }
        const { token, error } = await stripe.createToken(card, {
            name: form.first_name + ' ' + form.last_name,
        });
        if (error) {
            paymentError.value  = error.message;
            paymentStatus.value = 'error';
            return;
        }
        form.stripe_token = token.id;
    }

    form.post('/cart/pay', {
        onSuccess: () => {
            paymentStatus.value = 'success';
        },
        onError: (errors) => {
            paymentError.value = errors.payment ?? 'Payment failed.';
            paymentStatus.value = 'error';
        },
        onFinish: () => {
            if (paymentStatus.value === 'processing') {
                paymentStatus.value = 'idle';
            }
        },
    });
}
</script>

<template>
    <Head title="Checkout" />
    <div class="min-h-screen bg-gray-50 dark:bg-gray-800 dark:text-gray-300">
        <div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <Link href="/cart" class="text-primary hover:underline text-sm mb-6 inline-block">← Back to Cart</Link>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-8">Checkout</h1>

            <div v-if="paymentError" class="bg-red-50 border border-red-300 text-red-700 rounded-xl p-4 mb-6 flex items-center gap-3">
                <span>❌</span>
                <div>
                    <p class="font-semibold">Payment failed</p>
                    <p class="text-sm mt-0.5">{{ paymentError }}</p>
                </div>
            </div>

            <div v-if="paymentStatus === 'processing'" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center">
                <div class="bg-white rounded-2xl p-10 text-center shadow-2xl">
                    <div class="animate-spin h-12 w-12 border-4 border-primary border-t-transparent rounded-full mx-auto mb-4"></div>
                    <p class="text-lg font-semibold">Processing payment…</p>
                    <p class="text-gray-500 text-sm mt-1">Please do not close this window</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Order Summary -->
                <div class="bg-white dark:bg-gray-700 dark:text-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                    <div class="space-y-3">
                        <div v-for="item in cartItems" :key="item.product.id" class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <img :src="item.product.image" :alt="item.product.name" class="w-12 h-12 object-cover rounded" />
                                <div>
                                    <p class="text-sm font-medium">{{ item.product.name }}</p>
                                    <p class="text-xs text-gray-400">× {{ item.quantity }}</p>
                                </div>
                            </div>
                            <p class="font-semibold">{{ (item.product.price * item.quantity).toFixed(2) }} €</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 mt-4 pt-4 flex justify-between">
                        <span class="text-lg font-semibold">Total</span>
                        <span class="text-xl font-bold text-primary">{{ total.toFixed(2) }} €</span>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-xs text-gray-400 bg-gray-50 rounded-lg p-3">
                        <span>🔒</span>
                        <span>Secured by SSL. Card details go directly to Stripe servers.</span>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="bg-white dark:bg-gray-700 dark:text-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold mb-4">Payment Details</h2>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name</label>
                                <input v-model="form.first_name" type="text" required class="w-full rounded-lg border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white shadow-sm focus:ring-primary focus:border-primary" />
                                <p v-if="form.errors.first_name" class="text-red-500 text-xs mt-1">{{ form.errors.first_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name</label>
                                <input v-model="form.last_name" type="text" required class="w-full rounded-lg border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white shadow-sm focus:ring-primary focus:border-primary" />
                                <p v-if="form.errors.last_name" class="text-red-500 text-xs mt-1">{{ form.errors.last_name }}</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <input v-model="form.email" type="email" required class="w-full rounded-lg border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white shadow-sm focus:ring-primary focus:border-primary" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                            <input v-model="form.phone" type="tel" required class="w-full rounded-lg border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white shadow-sm focus:ring-primary focus:border-primary" />
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Payment Method</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label :class="['flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition', form.payment_method === 'stripe' ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300']">
                                    <input type="radio" v-model="form.payment_method" value="stripe" class="sr-only" />
                                    <div class="text-center">
                                        <div class="text-2xl mb-1">💳</div>
                                        <span class="font-medium text-sm">Credit Card</span>
                                        <p class="text-xs text-gray-400 mt-0.5">via Stripe</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div v-if="form.payment_method === 'stripe'">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Card Details</label>
                            <div id="stripe-card-element" class="w-full rounded-lg border border-gray-300 shadow-sm p-3 bg-white min-h-[42px]"></div>
                            <p v-if="!stripeReady" class="text-xs text-gray-400 mt-1">⏳ Loading payment form…</p>
                            <div class="mt-2 bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-700">
                                <p class="font-semibold mb-1">🧪 Test cards:</p>
                                <p>✅ Success: <code class="bg-amber-100 px-1 rounded">4242 4242 4242 4242</code></p>
                                <p>❌ Decline: <code class="bg-amber-100 px-1 rounded">4000 0000 0000 0002</code></p>
                                <p class="text-amber-600 mt-1">Any future date, any 3-digit CVC</p>
                            </div>
                        </div>

                        <button type="submit" :disabled="form.processing || paymentStatus === 'processing'"
                            class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-3 rounded-lg transition mt-2 flex items-center justify-center gap-2">
                            <span v-if="form.processing || paymentStatus === 'processing'">
                                <span class="animate-spin inline-block h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
                                Processing…
                            </span>
                            <span v-else>🔒 Pay {{ total.toFixed(2) }} €</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>