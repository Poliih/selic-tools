<template>
  <div class="max-w-md mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-6 text-center">Simulador Selic</h1>

    <form @submit.prevent="simular" class="space-y-4">
      <div>
        <label class="block mb-1 font-semibold" for="valor">Valor Inicial (R$)</label>
        <input
          id="valor"
          v-model.number="valor"
          type="number"
          min="0"
          step="0.01"
          required
          class="w-full border rounded px-3 py-2"
        />
      </div>

      <div>
        <label class="block mb-1 font-semibold" for="inicio">Data Início</label>
        <input
          id="inicio"
          v-model="inicio"
          type="date"
          required
          class="w-full border rounded px-3 py-2"
        />
      </div>

      <div>
        <label class="block mb-1 font-semibold" for="fim">Data Fim</label>
        <input
          id="fim"
          v-model="fim"
          type="date"
          required
          class="w-full border rounded px-3 py-2"
        />
      </div>

      <button
        type="submit"
        class="w-full bg-indigo-600 text-white rounded py-2 font-semibold hover:bg-indigo-700 transition"
        :disabled="loading"
      >
        {{ loading ? 'Calculando...' : 'Simular' }}
      </button>
    </form>

    <div v-if="resultado !== null" class="mt-6 p-4 bg-green-100 rounded text-green-900 font-semibold text-lg text-center">
      Resultado final: R$ {{ resultado.toFixed(2) }}
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const valor = ref(1000);
const inicio = ref('');
const fim = ref('');
const resultado = ref(null);
const loading = ref(false);

async function simular() {
  if (!valor.value || !inicio.value || !fim.value) {
    alert('Preencha todos os campos corretamente');
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
        inicio: formatBR(inicio.value),
        fim: formatBR(fim.value),
      },
    });
    resultado.value = res.data.valorfinal;
  } catch {
    alert('Erro ao calcular a simulação');
  } finally {
    loading.value = false;
  }
}
</script>
