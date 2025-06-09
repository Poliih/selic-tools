<script setup>
import { computed } from 'vue'

const props = defineProps({
    value: {
        type: [Number, String],
        default: 0
    },
    previous: {
        type: [Number, String],
        default: 0
    },
    label: {
        type: String,
        default: 'Indicador'
    },
    icon: {
        type: String,
        default: 'default'
    }
})

const percentageChange = computed(() => {
    const val = Number(props.value)
    const prev = Number(props.previous)

    if (!prev || prev === 0 || val === prev) {
        return { text: '0.00%', class: 'text-gray-200', icon: 'minus' };
    }

    const diff = ((val - prev) / prev) * 100;

    if (diff > 0) {
        return {
            text: `+${diff.toFixed(2)}%`,
            class: 'text-green-300',
            icon: 'up'
        };
    } else {
        return {
            text: `${diff.toFixed(2)}%`,
            class: 'text-red-300',
            icon: 'down'
        };
    }
});
</script>

<template>
    <div class="relative w-full min-w-[200px] p-6 rounded-2xl shadow-xl drop-shadow-lg text-white
                bg-gradient-to-br from-[#87BFFF] to-[#3F8EFC]
                transition-all duration-300 ease-in-out
                hover:-translate-y-1 hover:shadow-2xl hover:shadow-blue-500/30">

        <div class="flex justify-between items-start">
            <div class="flex flex-col">
                <div class="text-sm font-medium opacity-80">{{ label }}</div>
                <div class="text-3xl font-bold tracking-tight mt-1">
                    {{ typeof value === 'number' ? value.toLocaleString('pt-BR') : value }}
                </div>
            </div>

            <div class="absolute top-4 right-4 text-white/20">
                <svg v-if="icon === 'calendar'" xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <svg v-if="icon === 'percent'" xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l4-4m0 0l4 4m-4-4v12a2 2 0 002 2h.5a2 2 0 002-2V8m0 0a2 2 0 100-4 2 2 0 000 4zm0 12a2 2 0 100-4 2 2 0 000 4z" /></svg>
                 <svg v-if="icon === 'list'" xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
            </div>
        </div>

        <div v-if="previous > 0" class="mt-4">
             <div class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-white/20"
                  :class="percentageChange.class">

                <svg v-if="percentageChange.icon === 'up'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                <svg v-if="percentageChange.icon === 'down'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>

                {{ percentageChange.text }}
            </div>
        </div>
    </div>
</template>
