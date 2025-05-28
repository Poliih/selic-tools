<template>
  <div class="max-w-4xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Histórico da Taxa Selic</h1>

    <form @submit.prevent="buscarSelic" class="flex flex-wrap gap-4 mb-6 items-end">
      <div class="flex flex-col">
        <label for="startDate" class="font-semibold mb-1">Data Inicial</label>
        <input
          id="startDate"
          v-model="startDate"
          type="date"
          class="border rounded px-3 py-2"
          required
        />
      </div>

      <div class="flex flex-col">
        <label for="endDate" class="font-semibold mb-1">Data Final</label>
        <input
          id="endDate"
          v-model="endDate"
          type="date"
          class="border rounded px-3 py-2"
          required
        />
      </div>

      <button
        type="submit"
        class="bg-blue-600 text-white rounded px-6 py-2 hover:bg-blue-700 transition disabled:opacity-50"
        :disabled="loading"
      >
        {{ loading ? 'Buscando...' : 'Buscar Histórico' }}
      </button>

      <button
        type="button"
        class="bg-green-600 text-white rounded px-6 py-2 hover:bg-green-700 transition disabled:opacity-50"
        @click="exportarCSV"
        :disabled="!dados.length"
      >
        Exportar CSV
      </button>
    </form>

    <ul class="divide-y divide-gray-200 border rounded max-h-96 overflow-auto">
      <li
        v-for="item in dados"
        :key="item.data"
        class="flex justify-between px-4 py-2"
      >
        <span>{{ formatarData(item.data) }}</span>
        <span class="font-semibold text-gray-800">{{ formatarValor(item.valor) }}</span>
      </li>
      <li v-if="!dados.length && !loading" class="text-center py-4 text-gray-500">
        Nenhum dado carregado.
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const startDate = ref('');
const endDate = ref('');
const dados = ref([]);
const loading = ref(false);

function formatarData(data) {
  return data;
}

function formatarValor(valor) {
  const num = Number(String(valor).replace(',', '.'));
  if (isNaN(num)) return valor;
  return `${num.toFixed(2)}%`;
}

async function buscarSelic() {
  if (!startDate.value || !endDate.value) return;

  loading.value = true;
  dados.value = [];

  try {
    const formatBR = (dateStr) => {
      const date = new Date(dateStr);
      return new Intl.DateTimeFormat('pt-BR').format(date);
    };

    const res = await axios.get('/api/selic', {
      params: {
        startDate: formatBR(startDate.value),
        endDate: formatBR(endDate.value),
      },
    });

    dados.value = res.data;
  } catch (e) {
    alert('Erro ao buscar dados Selic.');
  } finally {
    loading.value = false;
  }
}

async function exportarCSV() {
  if (!startDate.value || !endDate.value) {
    return alert('Informe as datas para exportar.');
  }

  try {
    const response = await axios.get('/exportar', {
      params: {
        startDate: startDate.value,
        endDate: endDate.value,
      },
      responseType: 'blob',
    });

    const blob = new Blob([response.data], { type: 'text/csv;charset=utf-8;' });
    const url = window.URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'selic.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
  } catch (error) {
    alert('Erro ao exportar CSV.');
    console.error(error);
  }
}


</script>
