<script setup>
import { ref, watch, computed, onMounted } from 'vue'
import axios from 'axios'
import { Chart, registerables } from 'chart.js'
import Rectangle from '../Components/Rectangle.vue'
import SidebarMenu from './SidebarMenu.vue'
import { Head } from '@inertiajs/vue3';

Chart.register(...registerables)

const startDate = ref('')
const endDate = ref('')
const dados = ref([])
const selicMedia = ref(null)
const isLoading = ref(false)
const chartRef = ref(null)
let chartInstance = null
const notification = ref({ show: false, message: '', type: 'error' })

const activePreset = ref('3m')
let debounceTimer = null


function showNotification(message, type = 'error', duration = 4000) {
    notification.value = { show: true, message, type };
    setTimeout(() => {
        notification.value.show = false;
    }, duration);
}

const formatarData = (data) => {
    const d = new Date(data)
    const dia = String(d.getUTCDate()).padStart(2, '0')
    const mes = String(d.getUTCMonth() + 1).padStart(2, '0')
    const ano = d.getUTCFullYear()
    return `${dia}/${mes}/${ano}`
}

const buscarSelic = async () => {
    if (!startDate.value || !endDate.value) {
        return;
    }
    if (new Date(startDate.value) > new Date(endDate.value)) {
        showNotification('A data inicial não pode ser posterior à data final.');
        return;
    }

    isLoading.value = true;
    if (chartInstance) chartInstance.destroy();

    try {
        const response = await axios.get('/api/selic', {
            params: {
                startDate: formatarData(startDate.value),
                endDate: formatarData(endDate.value),
            },
        });

        dados.value = response.data;

        if (dados.value.length > 0) {
            const soma = dados.value.reduce((acc, item) => acc + Number(item.valor), 0);
            selicMedia.value = soma / dados.value.length;
            renderChart();
        } else {
            showNotification('Nenhum dado encontrado para o período selecionado.', 'info');
        }

    } catch (error) {
        console.error(error);
        showNotification('Erro ao buscar dados da Selic. Tente novamente.');
    } finally {
        isLoading.value = false;
    }
}

const stopWatcher = watch([startDate, endDate], () => {
    activePreset.value = ''
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => {
        buscarSelic()
    }, 800)
}, { immediate: false })


const setPeriod = (period) => {
    activePreset.value = period
    const hoje = new Date()
    let inicio = new Date()

    stopWatcher()

    switch (period) {
        case '30d':
            inicio.setDate(hoje.getDate() - 30)
            break
        case '3m':
            inicio.setMonth(hoje.getMonth() - 3)
            break
        case '6m':
            inicio.setMonth(hoje.getMonth() - 6)
            break
        case '1y':
            inicio.setFullYear(hoje.getFullYear() - 1)
            break
    }

    endDate.value = hoje.toISOString().split('T')[0]
    startDate.value = inicio.toISOString().split('T')[0]

    buscarSelic().then(() => {
    })
}


onMounted(() => {
    setPeriod('3m')
});

const renderChart = () => {
    if (!chartRef.value || !dados.value.length) return;
    const dadosAgrupados = {};
    dados.value.forEach(item => {
        const [dia, mes, ano] = item.data.split('/');
        const chave = `${mes}/${ano.slice(-2)}`;
        if (!dadosAgrupados[chave]) dadosAgrupados[chave] = [];
        dadosAgrupados[chave].push(Number(item.valor));
    });
    const labels = Object.keys(dadosAgrupados);
    const valores = labels.map(chave => {
        const soma = dadosAgrupados[chave].reduce((a, b) => a + b, 0);
        return Number((soma / dadosAgrupados[chave].length).toFixed(7));
    });
    const ctx = chartRef.value.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(63, 142, 252, 0.4)');
    gradient.addColorStop(1, 'rgba(63, 142, 252, 0)');
    chartInstance = new Chart(chartRef.value, {
        type: 'line', data: { labels, datasets: [{ label: 'Taxa Selic Média Mensal (%)', data: valores, borderColor: '#3F8EFC', backgroundColor: gradient, tension: 0.4, fill: true, pointBackgroundColor: '#3F8EFC', pointBorderColor: '#fff', pointHoverRadius: 7, pointHoverBackgroundColor: '#fff', pointHoverBorderColor: '#3F8EFC' }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, title: { display: false } }, scales: { y: { ticks: { callback: value => value.toFixed(3) + '%', color: '#6b7280' }, grid: { color: 'rgba(200, 200, 200, 0.2)' }, border: { dash: [5, 5] } }, x: { ticks: { maxTicksLimit: 12, color: '#6b7280' }, grid: { display: false }, } } }
    });
}

const entreDatas = computed(() => {
    if (!startDate.value || !endDate.value) return 0;
    const start = new Date(startDate.value);
    const end = new Date(endDate.value);
    if (end < start) return 0;
    const diffTime = Math.abs(end - start);
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
});

</script>

<template>
    <Head title="SelicTools" />
    <div>
        <SidebarMenu />

        <transition name="toast">
            <div v-if="notification.show" :class="[
                'fixed top-5 right-5 z-[100] p-4 rounded-lg shadow-lg text-white',
                notification.type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            ]">
                {{ notification.message }}
            </div>
        </transition>

        <main class="ml-80 mr-10 p-6 space-y-8">
            <h1 class="text-3xl font-bold text-gray-800 fade-in-up">Dashboard Selic</h1>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 fade-in-up" style="animation-delay: 100ms;">
                <Rectangle :value="entreDatas" label="Quantidade de dias" icon="calendar" />
                <Rectangle :value="selicMedia ? selicMedia.toFixed(4) : 0" label="Média no período (%)" icon="percent" />
                <Rectangle :value="dados.length" label="Total de registros" icon="list" />
            </div>

            <div class="bg-white/50 backdrop-blur-lg border border-white/20 rounded-2xl shadow-lg p-6 space-y-6 fade-in-up" style="animation-delay: 200ms;">
                <div class="flex flex-wrap items-center gap-2 border-b border-white/20 pb-4">
                    <h3 class="text-sm font-semibold text-gray-700 mr-2">Período:</h3>
                    <button @click="setPeriod('30d')" :class="activePreset === '30d' ? 'bg-indigo-600 text-white' : 'bg-white/50 hover:bg-white/80 text-gray-700'" class="px-3 py-1 text-sm rounded-full transition-all">Últimos 30 dias</button>
                    <button @click="setPeriod('3m')" :class="activePreset === '3m' ? 'bg-indigo-600 text-white' : 'bg-white/50 hover:bg-white/80 text-gray-700'" class="px-3 py-1 text-sm rounded-full transition-all">Últimos 3 meses</button>
                    <button @click="setPeriod('6m')" :class="activePreset === '6m' ? 'bg-indigo-600 text-white' : 'bg-white/50 hover:bg-white/80 text-gray-700'" class="px-3 py-1 text-sm rounded-full transition-all">Últimos 6 meses</button>
                    <button @click="setPeriod('1y')" :class="activePreset === '1y' ? 'bg-indigo-600 text-white' : 'bg-white/50 hover:bg-white/80 text-gray-700'" class="px-3 py-1 text-sm rounded-full transition-all">Último ano</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                    <div class="relative md:col-span-5">
                        <label class="block text-sm font-medium text-gray-800 mb-1">Início</label>
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none mt-7">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <input type="date" v-model="startDate" class="w-full rounded-lg border-gray-300 pl-10 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all duration-300" />
                    </div>
                    <div class="relative md:col-span-5">
                        <label class="block text-sm font-medium text-gray-800 mb-1">Fim</label>
                         <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none mt-7">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <input type="date" v-model="endDate" class="w-full rounded-lg border-gray-300 pl-10 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all duration-300" />
                    </div>
                    <div class="relative md:col-span-2 flex items-center gap-4">
                        <button @click="buscarSelic" :disabled="isLoading" title="Forçar busca manual" class="h-10 w-full flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 rounded-lg transition-all duration-300 ease-in-out disabled:opacity-50 transform hover:scale-105">
                            <svg v-if="isLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </button>
                        <a v-if="dados.length" :href="`/api/exportar?startDate=${formatarData(startDate)}&endDate=${formatarData(endDate)}`" class="h-10 flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold p-2.5 rounded-lg transition-all duration-300 transform hover:scale-105" title="Exportar para Excel">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white/50 backdrop-blur-lg border border-white/20 rounded-2xl shadow-lg p-6 min-h-[450px] fade-in-up" style="animation-delay: 300ms;">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Histórico da Taxa Selic</h2>
                <div class="relative h-[350px]">
                    <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center bg-gray-100/30 rounded-lg animate-pulse">
                        <p class="text-gray-500">Carregando dados do gráfico...</p>
                    </div>
                     <canvas v-show="!isLoading && dados.length" ref="chartRef"></canvas>
                     <div v-if="!isLoading && !dados.length" class="absolute inset-0 flex items-center justify-center text-gray-500 text-center p-4">
                        <p>Nenhum dado para exibir. Selecione um período diferente ou ajuste as datas.</p>
                    </div>
                </div>
            </div>

            <div v-if="dados.length" class="bg-white/50 backdrop-blur-lg border border-white/20 rounded-2xl shadow-lg overflow-hidden fade-in-up" style="animation-delay: 400ms;">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800">Dados Detalhados</h2>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <table class="min-w-full">
                        <thead class="sticky top-0 bg-white/70 backdrop-blur-md">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Valor (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/20">
                            <tr v-for="(item, index) in dados" :key="index" class="hover:bg-white/30 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ item.data }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ Number(item.valor).toFixed(5) }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</template>

<style>
.toast-enter-active,
.toast-leave-active {
    transition: transform 0.5s ease, opacity 0.5s ease;
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(-20px) translateX(100%);
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
