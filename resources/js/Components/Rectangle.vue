<script setup>
defineProps({
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
    }
})

const calcPercent = (value, previous) => {
    const val = Number(value)
    const prev = Number(previous)
    if (!prev || prev === 0) return '0.00%'
    const diff = ((val - prev) / prev) * 100
    const prefix = diff >= 0 ? '+' : ''
    return `${prefix}${diff.toFixed(2)}%`
}
</script>

<template>
    <div
        class="w-full min-w-[200px] p-6 rounded-2xl shadow-xl drop-shadow-lg transition-all duration-300 hover:scale-[1.02] text-white bg-gradient-to-br from-[#87BFFF] to-[#3F8EFC] flex flex-col items-center text-center">
        <div class="text-s font-bold opacity-90 text-center w-full">{{ label }}</div>

        <div class="mt-2 gap-y-1">
            <div class="text-3xl font-bold tracking-tight">
                {{ Number(value).toLocaleString('pt-BR') }}
            </div>

            <!-- <div class="text-xs font-semibold px-2 py-1 rounded-full">
                {{ calcPercent(value, previous) }}
            </div> -->
        </div>
    </div>

</template>
