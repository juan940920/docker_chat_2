<template>
<div class="card" v-if="loading">
    <ProgressBar mode="indeterminate" style="height: 16px"></ProgressBar>
</div>
<div v-else>
    <div v-if="categorias.length">
        <Toolbar class="mb-6">
            <template #start>
                <Button v-if="$can('manage','all')" type="button" label="Nueva Categoria" @click="visible = true, categoria = {};"></Button>
            </template>
        </Toolbar>
        <DataTable :value="categorias" tableStyle="min-width: 50rem">
            <template #header>
                <div class="flex flex-wrap gap-2 items-center justify-between">
                    <h4 class="m-0">Gestión de Categorias</h4>
                </div>
            </template>
        <Column field="id" header="ID"></Column>
        <Column field="nombres" header="NOMBRE"></Column>
        <Column :exportable="false" style="min-width: 12rem">
                <template #body="slotProps">
                    <Button v-if="$can('manage','all')" icon="pi pi-pencil" rounded class="mr-2" @click="editCategoria(slotProps.data)" />
                    <Button v-if="$can('manage','all')" icon="pi pi-trash" rounded severity="danger" @click="" />
                </template>
                </Column>
        </DataTable>
    </div>
</div>
<Dialog v-model:visible="visible" modal header="categoria" :style="{ width: '25rem' }">
        <div class="flex flex-col gap-6">
            <div>
                <label for="nombres" class="block font-bold mb-3">Nombre</label>
                <InputText id="nombres" v-model="categoria.nombres" :invalid="!categoria.nombres" autofocus
                    placeholder="Ingrese nombre categoria" fluid />
            </div>
        </div>
        <template #footer>
            <Button type="button" label="Cancelar" severity="secondary" @click="visible = false"></Button>
            <Button type="button" label="Guardar categoria" @click="guardarCategoria()"></Button>
        </template>
    </Dialog>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import Swal from 'sweetalert2';
import categoriaService from '../../../services/categoria.service.js';

const categorias = ref([])
const categoria = ref({})
const errors = ref([])
const loading = ref(true)
const visible = ref(false)



onMounted(() => {
    getCategoria()
})

async function getCategoria() {
    loading.value = true;
    const {data} = await categoriaService.listar();
    categorias.value = data;
    
    loading.value = false;

}

async function guardarCategoria() {
    try {
        if (categoria.value.id) {
            await categoriaService.modificar(categoria.value.id, categoria.value);
            Swal.fire({
                title: "Categoria Actualizado!",
                text: "continuar!",
                icon: "success",
            });
        } else {
            await categoriaService.guardar(categoria.value);
            Swal.fire({
                title: "Categoria Registrado!",
                text: "continuar!",
                icon: "success",
            });
        }
        categoria.value = {};
        getCategoria();

        visible.value = false;
    } catch (error) {
        errors.value = error.response.data?.errors;
    }

}

function editCategoria(data) {
    categoria.value = data;
    errors.value = [];
    visible.value = true;

}

</script>