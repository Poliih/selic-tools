<script setup>
import SidebarItem from './SidebarItem.vue'
import { Link } from '@inertiajs/vue3'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import { faChartLine, faCalculator, faUserCircle, faRightFromBracket } from '@fortawesome/free-solid-svg-icons'

library.add(faChartLine, faCalculator, faUserCircle, faRightFromBracket)
</script>

<template>
    <aside class="fixed top-5 left-5 h-[calc(100vh-3rem)] w-64 z-50
                   bg-gradient-to-br from-[#87BFFF] to-[#3F8EFC]
                   rounded-2xl shadow-2xl shadow-blue-500/30 p-6
                   flex flex-col fade-in-up">

        <div class="fade-in-up" style="animation-delay: 100ms;">
            <h1 class="text-2xl font-bold text-center text-white/90">
                Selic Tools
            </h1>
        </div>

        <nav class="flex flex-col gap-3 mt-10">
            <div class="fade-in-up" style="animation-delay: 200ms;">
                <SidebarItem label="Dashboard" :active="route().current('dashboard')">
                    <template #icon>
                        <font-awesome-icon icon="fa-solid fa-chart-line" class="w-5 h-5" />
                    </template>
                    <Link :href="route('dashboard')" class="absolute inset-0 z-10" />
                </SidebarItem>
            </div>

            <div class="fade-in-up" style="animation-delay: 300ms;">
                <SidebarItem label="Simulador" :active="route().current('simulador')">
                    <template #icon>
                        <font-awesome-icon icon="fa-solid fa-calculator" class="w-5 h-5" />
                    </template>
                    <Link :href="route('simulador')" class="absolute inset-0 z-10" />
                </SidebarItem>
            </div>
        </nav>

        <div class="mt-auto flex flex-col gap-3">
            <div class="fade-in-up" style="animation-delay: 400ms;">
                <SidebarItem label="Meu Perfil" :active="route().current('profile.show')">
                     <template #icon>
                        <font-awesome-icon icon="fa-solid fa-user-circle" class="w-5 h-5" />
                    </template>
                    <Link :href="route('profile.show')" class="absolute inset-0 z-10" />
                </SidebarItem>
            </div>

            <div class="fade-in-up" style="animation-delay: 500ms;">
                 <SidebarItem label="Sair">
                     <template #icon>
                        <font-awesome-icon icon="fa-solid fa-right-from-bracket" class="w-5 h-5" />
                    </template>
                    <Link :href="route('logout')" method="post" as="button" class="absolute inset-0 z-10" />
                </SidebarItem>
            </div>
        </div>

    </aside>
</template>
