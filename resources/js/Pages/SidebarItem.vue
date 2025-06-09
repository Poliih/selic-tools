<script setup>
defineProps({
  label: String,
  active: Boolean
})
</script>

<template>
    <div class="relative">
        <slot />

        <div :class="[
                'w-full py-3 ps-5 pe-4 rounded-lg flex items-center gap-4 relative z-0 transition-all duration-300 ease-in-out',
                active
                    ? 'bg-blue-100/90 text-indigo-800'
                    : 'text-indigo-900/60 hover:bg-white/70 hover:text-indigo-900/90 hover:translate-x-1'
            ]">

            <div v-if="active"
                 class="absolute left-0 top-2 bottom-2 w-1 bg-indigo-500 rounded-r-full transition-all duration-300">
            </div>

            <div :class="[
                    'transition-colors duration-300',
                    active ? 'text-indigo-600' : ''
                ]">
                <slot name="icon">
                    <div class="w-6 h-6 bg-gray-300 rounded-md"></div>
                </slot>
            </div>

            <span :class="['text-sm transition-colors duration-300', active ? 'font-bold' : 'font-semibold']">
                {{ label }}
            </span>
        </div>
    </div>
</template>
