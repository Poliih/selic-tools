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
      <div class="flex items-end">
        <button
          @click="buscarSelic"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md shadow"
        >
          Buscar
        </button>
      </div>
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

    <div class="mt-6" v-if="dados.length">
      <a
        :href="`/api/exportar?startDate=${startDate}&endDate=${endDate}`"
        class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow"
      >
        Exportar CSV
      </a>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const startDate = ref('')
const endDate = ref('')
const dados = ref([])

const formatarData = (data) => {
  const [ano, mes, dia] = data.split('-');
  return `${dia}/${mes}/${ano}`;
};

const buscarSelic = async () => {
  if (!startDate.value || !endDate.value) {
    alert('Por favor, selecione ambas as datas.');
    return;
  }
  try {
    const response = await axios.get('/api/selic', {
      params: {
        startDate: formatarData(startDate.value),
        end: formatarData(endDate.value),
      },
    })
    dados.value = response.data
  } catch (error) {
    console.error(error)
    alert('Erro ao buscar dados da Selic.')
  }
}

</script>

<style scoped>
input[type="date"] {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
}
</style>
