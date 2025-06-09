<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-100 via-purple-100 to-blue-100 px-4 py-12">

        <div class="w-full max-w-md bg-white/50 backdrop-blur-lg border border-white/20 rounded-2xl shadow-xl p-8 space-y-6 fade-in-up">

            <h1 class="text-4xl font-extrabold text-center bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-blue-500">
                Bem-vindo!
            </h1>

            <div v-if="status" class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-lg">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div class="relative">
                    <InputLabel for="email" value="Email" class="text-gray-800" />
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none mt-7">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                    </div>
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full pl-10"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4 relative">
                    <InputLabel for="password" value="Senha" class="text-gray-800" />
                     <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none mt-7">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full pl-10"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between mt-4">
                    <label class="flex items-center">
                        <Checkbox v-model:checked="form.remember" name="remember" />
                        <span class="ms-2 text-sm text-slate-600">Lembrar de mim</span>
                    </label>

                    <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-slate-600 hover:text-slate-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Esqueceu sua senha?
                    </Link>
                </div>

                <div class="pt-4">
                    <button type="submit"
                            :class="{ 'opacity-50': form.processing }" :disabled="form.processing"
                            class="w-full flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-300 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 hover:shadow-lg">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                           <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                           <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Log in
                    </button>
                </div>

                 <div class="text-center mt-6">
                    <p class="text-sm text-slate-600">
                        Não tem uma conta?
                        <Link :href="route('register')" class="font-semibold text-indigo-600 hover:text-indigo-800">
                            Cadastre-se
                        </Link>
                    </p>
                </div>
            </form>
        </div>
    </div>
</template>
