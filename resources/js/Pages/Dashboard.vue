<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import { Chart, controllers, registerables } from 'chart.js'

Chart.register(...registerables)

const startDate = ref('')
const endDate = ref('')
const dados = ref([])
const chartRef = ref(null)
let chartInstance = null

const formatarData = (data) => {
    const d = typeof data === 'string' ? data : data.value
    const [ano, mes, dia] = d.split('-')
    return `${dia}/${mes}/${ano}`
}

const isLoading = ref(false)

const buscarSelic = async () => {
    if (!startDate.value || !endDate.value) {
        alert('Por favor, selecione ambas as datas.');
        return;
    }
    if (new Date(startDate.value) > new Date(endDate.value)) {
        alert('A data inicial não pode ser posterior à data final.');
        return;
    }
    isLoading.value = true
    try {
        const response = await axios.get('/api/selic', {
            params: {
                startDate: formatarData(startDate.value),
                endDate: formatarData(endDate.value),
            },
        })
        console.log('Dados recebidos:', response.data)
        dados.value = response.data
        renderChart()
    } catch (error) {
        console.error(error)
        alert('Erro ao buscar dados da Selic.')
    } finally {
        isLoading.value = false
    }
}

const renderChart = () => {
    if (!chartRef.value || !dados.value.length) return

    const dadosAgrupados = {}
    dados.value.forEach(item => {
        const [dia, mes, ano] = item.data.split('/')
        const chave = `${mes}/${ano.slice(-2)}`

        if (!dadosAgrupados[chave]) {
            dadosAgrupados[chave] = []
        }
        dadosAgrupados[chave].push(Number(item.valor))
    })

    const labels = Object.keys(dadosAgrupados)
    const valores = labels.map(chave => {
        const soma = dadosAgrupados[chave].reduce((a, b) => a + b, 0)
        const media = soma / dadosAgrupados[chave].length
        return Number(media.toFixed(7))
    })

    if (chartInstance) {
        chartInstance.destroy()
    }

    chartInstance = new Chart(chartRef.value, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Taxa Selic Média (%) por Mês',
                data: valores,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.5,
                fill: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Histórico da Taxa Selic por Mês' }
            },
            scales: {
                y: {
                    ticks: {
                        callback: (value) => value.toFixed(3) + '%'
                    }
                },
                x: {
                    ticks: {
                        maxTicksLimit: 10
                    }
                }
            }
        }
    })
}
</script>

<template>
    <div class="p-6 max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Dashboard SELIC</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Data Início</label>
                <input type="date" v-model="startDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Data Final</label>
                <input type="date" v-model="endDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            </div>
            <div class="flex items-end gap-4">
                <button @click="buscarSelic"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md shadow disabled:opacity-50"
                    :disabled="isLoading">
                    {{ isLoading ? 'Buscando...' : 'Buscar' }}
                </button>
                <div class="mt-6" v-if="dados.length">
                    <a :href="`/api/exportar?startDate=${formatarData(startDate)}&endDate=${formatarData(endDate)}`"
                        class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow">
                        Exportar
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <canvas ref="chartRef"></canvas>
        </div>

        <div v-if="dados.length" class="overflow-x-auto mt-4 border rounded-md shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor (%)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(item, index) in dados" :key="index">
                        <td class="px-6 py-4 text-sm text-gray-700">{{ item.data }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ Number(item.valor).toFixed(5) }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
input[type="date"] {
    padding: 0.5rem;
    border: 1px solid #d1d5db;
}
</style>
