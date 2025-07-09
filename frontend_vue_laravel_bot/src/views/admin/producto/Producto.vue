<template>
<div class="card" v-if="loading">
    <ProgressBar mode="indeterminate" style="height: 16px"></ProgressBar>
</div>
<div v-else>
    <div v-if="productos.length">
        <Toolbar class="mb-6">
            <template #start>
                <Button v-if="$can('manage','all')" type="button" label="Nuevo Producto" @click="visible = true, producto = {};"></Button>
            </template>
        </Toolbar>
        <DataTable :value="productos" tableStyle="min-width: 50rem">
            <template #header>
                <div class="flex flex-wrap gap-2 items-center justify-between">
                    <h4 class="m-0">Gestión de Productos</h4>
                </div>
            </template>
        <Column field="id" header="ID"></Column>
        <Column field="nombre" header="NOMBRE"></Column>
        <Column field="precio" header="PRECIO"></Column>
        <Column field="stock" header="STOCK"></Column>
        <Column field="categoria.nombres" header="CATEGORIA"></Column>
        <Column field="descripcion" header="DESCRIPCIÓN"></Column>
        <Column :exportable="false" style="min-width: 12rem">
                <template #body="slotProps">
                    <Button v-if="$can('manage','all')" icon="pi pi-pencil" rounded class="mr-2" @click="editProducto(slotProps.data)" />
                    <Button v-if="$can('manage','all')" icon="pi pi-trash" rounded severity="danger" @click="" />
                </template>
                </Column>
        </DataTable>
    </div>
</div>
<Dialog v-model:visible="visible" modal header="producto" :style="{ width: '25rem' }">
        <div class="flex flex-col gap-6">
            <div>
                <label for="categoria" class="font-semibold mb-3">Categoria</label>
                <Select v-model="producto.categoria_id" :options="categorias"
                    :optionLabel="(categoria) => categoria.nombres" optionValue="id"
                    placeholder="Selecciona categoria" class="w-full md:w-56" fluid />
            </div>
            <div>
                <label for="nombre" class="block font-bold mb-3">Nombre</label>
                <InputText id="nombre" v-model="producto.nombre" :invalid="!producto.nombre" autofocus
                    placeholder="Ingrese nombre producto" fluid />
            </div>
            <div>
                <label for="precio" class="block font-bold mb-3">Precio</label>
                <InputNumber id="precio" v-model="producto.precio" :invalid="!producto.precio" mode="decimal" :minFractionDigits="2"
                    :maxFractionDigits="2" :useGrouping="true" fluid />
            </div>
            <div>
                <label for="stock" class="block font-bold mb-3">stock</label>
                <InputNumber id="stock" v-model="producto.stock" :invalid="!producto.stock" :useGrouping="true" fluid />
            </div>
        </div>
        <template #footer>
            <Button type="button" label="Cancelar" severity="secondary" @click="visible = false"></Button>
            <Button type="button" label="Guardar producto" @click="guardarProducto()"></Button>
        </template>
    </Dialog>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import Swal from 'sweetalert2';
import productoService from '../../../services/producto.service.js';
import categoriaService from '../../../services/categoria.service.js';

const productos = ref([])
const producto = ref({})
const errors = ref([])
const loading = ref(true)
const visible = ref(false)
const categorias = ref([])



onMounted(() => {
    getProducto();
    getCategoria();
})

async function getProducto() {
    loading.value = true;
    const {data} = await productoService.listar();
    productos.value = data;
    
    loading.value = false;

}

async function getCategoria() {
    const { data } = await categoriaService.listar();
    categorias.value = data;
}

async function guardarProducto() {
    try {
        if (producto.value.id) {
            await productoService.modificar(producto.value.id, producto.value);
            Swal.fire({
                title: "Producto Actualizado!",
                text: "continuar!",
                icon: "success",
            });
        } else {
            await productoService.guardar(producto.value);
            Swal.fire({
                title: "Producto Registrado!",
                text: "continuar!",
                icon: "success",
            });
        }
        producto.value = {};
        getProducto();

        visible.value = false;
    } catch (error) {
        errors.value = error.response.data?.errors;
    }

}

function editProducto(data) {
    producto.value = data;
    errors.value = [];
    visible.value = true;

}

</script>