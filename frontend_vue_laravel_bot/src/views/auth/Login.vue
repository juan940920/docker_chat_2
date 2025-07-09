<template>

    <!-- {{ credenciales }}

    <h1>Ingresar (Login)</h1>
    <form @submit.prevent="funIngresar()">
        <label for="">Ingrese su correo</label>
        <input type="email" v-model="credenciales.email">
        <br>
        <label for="">Ingrese su contraseña</label>
        <input type="password" v-model="credenciales.password">

        <br>
        <input type="submit" value="Ingresar">
    </form> -->

    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
        <div class="flex flex-col items-center justify-center">
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20" style="border-radius: 53px">
                    <div class="text-center mb-8">
                        <img
                        :src="logo"
                        alt="Logo"
                        class="w-40 h-40 mx-auto rounded-full shadow-md border border-gray-200 dark:border-gray-500"
                        />
                        <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mt-4 mb-2">¡Bienvenido!</div>
                        <span class="text-muted-color font-medium">Para continuar, ingrese sus datos</span>
                    </div>

                    <form @submit.prevent="funIngresar()">
                        <label for="email1" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Correo</label>
                        <InputText id="email1" type="text" placeholder="Correo Electronico" class="w-full md:w-[30rem] mb-8" v-model="credenciales.email" />

                        <label for="password1" class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">Contraseña</label>
                        <Password id="password1" v-model="credenciales.password" placeholder="Comtraseña" :toggleMask="true" class="mb-4" fluid :feedback="false"></Password>

                        <div class="flex items-center justify-between mt-2 mb-8 gap-8">
                            <div class="flex items-center">
                                <Checkbox id="rememberme1" binary class="mr-2"></Checkbox>
                                <label for="rememberme1">Remember me</label>
                            </div>
                            <span class="font-medium no-underline ml-2 text-right cursor-pointer text-primary">Forgot password?</span>
                        </div>
                        <Button type="submit" label="Ingresar" class="w-full"></Button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import authService from '../../services/auth.service';
import { useRouter } from 'vue-router';
import ability from './../../casl/ability';
import logo from '@/assets/logo.png';

const credenciales = ref({});
const router = useRouter();

async function funIngresar(){

    try {
        const {data} = await authService.login(credenciales.value)
        console.log(data)
        data.permisos.push({action: 'show', subject: 'auth'})

        localStorage.setItem("access_token", data.access_token)
        localStorage.setItem("permisos", JSON.stringify(data.permisos))

        // (CASL) ability
        ability.update(data.permisos)

        router.push({name: "MiPerfil"})
        
    }catch (error){
        alert("Error")
    }

}

</script>

<style scoped>
.pi-eye {
    transform: scale(1.6);
    margin-right: 1rem;
}

.pi-eye-slash {
    transform: scale(1.6);
    margin-right: 1rem;
}
</style>