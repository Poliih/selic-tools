<script setup>
import { ref, watch, computed } from 'vue'
import axios from 'axios'
import { Chart, registerables } from 'chart.js'
import Rectangle from '../Components/Rectangle.vue'

Chart.register(...registerables)

const startDate = ref('')
const endDate = ref('')
const dados = ref([])
const selicMedia = ref(null)
const isLoading = ref(false)
const chartRef = ref(null)
let chartInstance = null

const formatarData = (data) => {
    const d = typeof data === 'string' ? data : data.value
    const [ano, mes, dia] = d.split('-')
    return `${dia}/${mes}/${ano}`
}

const buscarSelic = async () => {
    if (!startDate.value || !endDate.value) {
        alert('Por favor, selecione ambas as datas.')
        return
    }

    if (new Date(startDate.value) > new Date(endDate.value)) {
        alert('A data inicial não pode ser posterior à data final.')
        return
    }

    isLoading.value = true

    try {
        const response = await axios.get('/api/selic', {
            params: {
                startDate: formatarData(startDate.value),
                endDate: formatarData(endDate.value),
            },
        })

        dados.value = response.data

        if (dados.value.length > 0) {
            const soma = dados.value.reduce((acc, item) => acc + Number(item.valor), 0)
            selicMedia.value = soma / dados.value.length
        }

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
        if (!dadosAgrupados[chave]) dadosAgrupados[chave] = []
        dadosAgrupados[chave].push(Number(item.valor))
    })

    const labels = Object.keys(dadosAgrupados)
    const valores = labels.map(chave => {
        const soma = dadosAgrupados[chave].reduce((a, b) => a + b, 0)
        return Number((soma / dadosAgrupados[chave].length).toFixed(7))
    })

    if (labels.length < 2) {
        alert('Selecione um intervalo de datas maior para exibir o gráfico.')
        return
    }

    if (chartInstance) chartInstance.destroy()

    chartInstance = new Chart(chartRef.value, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Taxa Selic Média (%) por Mês',
                data: valores,
                borderColor: '#2667FF',
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
                        callback: value => value.toFixed(3) + '%'
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

const entreDatas = computed(() => {
    if (!startDate.value || !endDate.value) return 0

    const start = new Date(startDate.value)
    const end = new Date(endDate.value)
    if (end < start) return 0

    const diffTime = Math.abs(end - start)
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1
})

const ontem = computed(() => {
    const hoje = new Date()
    const ontemDate = new Date(hoje)
    ontemDate.setDate(hoje.getDate() - 1)
    return ontemDate.toISOString().substring(0, 10)
})

watch([startDate, endDate], () => {
    console.log('Dias entre datas:', entreDatas.value)
})
</script>

<template>
    <div class="p-6 max-w-5xl mx-auto m-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-10 px-4">
            <Rectangle :value="entreDatas" label="Quantidade de dias" />
            <Rectangle :value="selicMedia" label="Média por período" />
            <Rectangle :value="dados.length" label="Total de registros" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Data Início</label>
                <input type="date" v-model="startDate"
                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Data Final</label>
                <input type="date" v-model="endDate"
                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div class="flex items-end gap-4">
                <button @click="buscarSelic" class="w-full py-2 px-4 rounded-md shadow disabled:opacity-50
                text-white text-center flex flex-col items-center
                bg-gradient-to-br from-[#87BFFF] to-[#3F8EFC]
                hover:brightness-110" :disabled="isLoading">
                    {{ isLoading ? 'Buscando...' : 'Buscar' }}
                </button>
                <div class="mt-6" v-if="dados.length">
                    <a :href="`/api/exportar?startDate=${formatarData(startDate)}&endDate=${formatarData(endDate)}`"
                        class="inline-flex items-center text-white px-4 py-2 rounded-md shadow
                        bg-gradient-to-br from-[#7EDC98] to-[#28B463]
                        hover:brightness-110 transition duration-300 ease-in-out">
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
                <thead class="bg-[#2667FF]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Valor (%)</th>
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
