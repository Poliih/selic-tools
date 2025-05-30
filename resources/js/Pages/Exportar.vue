<template>
  <div>
    <h1>Exportar CSV Selic</h1>
    <input v-model="inicio" placeholder="Data início" />
    <input v-model="fim" placeholder="Data fim" />
    <button @click="exportar">Baixar CSV</button>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const inicio = ref('01/01/2010');
const fim = ref('31/12/2016');

function exportar() {
  window.open(`/api/selic/exportar?start=${inicio.value}&end=${fim.value}`, '_blank');
}
</script>
