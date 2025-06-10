<script setup>
import { ref } from 'vue';
import axios from 'axios';
import SidebarMenu from './SidebarMenu.vue';
import { Head } from '@inertiajs/vue3';

const valor = ref(1000);
const inicio = ref('');
const fim = ref('');
const resultado = ref(null);
const loading = ref(false);
const errorMsg = ref('');

async function simular() {
    resultado.value = null;
    errorMsg.value = '';

    if (!valor.value || !inicio.value || !fim.value) {
        errorMsg.value = 'Por favor, preencha todos os campos.';
        return;
    }

    loading.value = true;
    try {
        function formatBR(date) {
            return new Date(date).toLocaleDateString('pt-BR', { timeZone: 'UTC' });
        }

        const res = await axios.get('/api/simulacao', {
            params: {
                valor: valor.value,
                startDate: formatBR(inicio.value),
                endDate: formatBR(fim.value),
            },
        });
        resultado.value = res.data.valorfinal;
    } catch (err) {
        errorMsg.value = 'Erro ao calcular. Tente novamente mais tarde.';
        console.error(err);
    } finally {
        loading.value = false;
    }
}
</script>

<template>
     <Head title="SelicTools" />
    <SidebarMenu />

    <main class="min-h-screen flex items-center justify-center px-4 py-12 ml-64">

        <div class="w-full max-w-md bg-white/50 backdrop-blur-lg border border-white/20 rounded-2xl shadow-xl p-8 space-y-6 fade-in-up">

            <h1 class="text-4xl font-extrabold text-center bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-blue-500">
                Simulador Selic
            </h1>

            <form @submit.prevent="simular" class="space-y-5">
                <div class="relative">
                    <label for="valor" class="block text-sm font-medium text-gray-800 mb-1">Valor Inicial</label>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none mt-7">
                         <span class="text-gray-500">R$</span>
                    </div>
                    <input id="valor" v-model.number="valor" type="number" min="0" step="0.01" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all duration-300" />
                </div>

                <div class="relative">
                    <label for="inicio" class="block text-sm font-medium text-gray-800 mb-1">Data de Início</label>
                     <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none mt-7">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <input id="inicio" v-model="inicio" type="date" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all duration-300" />
                </div>

                <div class="relative">
                    <label for="fim" class="block text-sm font-medium text-gray-800 mb-1">Data Final</label>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none mt-7">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <input id="fim" v-model="fim" type="date" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all duration-300" />
                </div>

                <button type="submit"
                        class="w-full flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-300 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 hover:shadow-lg"
                        :disabled="loading">
                    <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ loading ? 'Calculando...' : 'Simular' }}
                </button>
            </form>

            <div class="mt-6 text-center">
                <transition name="pop">
                    <div v-if="errorMsg" class="p-3 bg-red-100 rounded-lg text-red-700 font-medium text-sm">
                        {{ errorMsg }}
                    </div>
                </transition>

                <transition name="pop">
                     <div v-if="resultado !== null" class="mt-6 p-5 bg-green-100 border border-green-200 rounded-lg text-green-900">
                        <span class="block text-sm text-green-900/80">Valor Final Corrigido</span>
                        <span class="text-3xl font-bold">R$ {{ resultado.toFixed(2).replace('.', ',') }}</span>
                    </div>
                </transition>
            </div>
        </div>
    </main>
</template>

<style>
.pop-enter-active,
.pop-leave-active {
    transition: transform 0.4s cubic-bezier(0.5, 0, 0.5, 1), opacity 0.4s ease;
}

.pop-enter-from,
.pop-leave-to {
    opacity: 0;
    transform: scale(0.9) translateY(-10px);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
}
</style>
