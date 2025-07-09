<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button v-if="$can('manage','all')" label="Nueva Venta" @click="visible = true, venta = {};" />
        </template>
    </Toolbar>
    <DataTable ref="dt" :value="ventas" dataKey="id" lazy :totalRecords="totalRecords" :loading="loading"
        :paginator="true" :rows="10" @page="onPage($event)"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[3, 10, 25]"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} ventas">
        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">Gestión de ventas</h4>
                <IconField>
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText placeholder="Buscar..." v-model="buscar" @change="getVentas()" />
                </IconField>
            </div>
        </template>
        <Column field="id" header="ID"></Column>
        <Column field="contacto.nro_whatsapp" header="NRO WHAPSAPP"></Column>
        <Column field="total" header="TOTAL"></Column>
        <Column :exportable="false" style="min-width: 12rem">
            <template #body="slotProps">
                <Button icon="pi pi-print" rounded severity="info" class="ml-2"
                    @click="imprimirCompra(slotProps.data)" />
                <Button icon="pi pi-trash" rounded severity="danger" @click="" />
            </template>
        </Column>
    </DataTable>
    <Dialog v-model:visible="visible" modal header="Registrar Venta" :style="{ width: '50rem' }">
        <div class="flex flex-col gap-4">

            {{ venta }}
            <div>
                <label for="contacto_id" class="block font-bold mb-2">Seleccionar Contacto</label>
                <Dropdown v-model="venta.contacto_id" :options="contactos" optionLabel="nro_whatsapp" optionValue="id"
                    placeholder="Seleccione un contacto" />
            </div>
            <div>
                <label class="block font-bold mb-2">Productos</label>

                <div v-for="(item, index) in venta.items" :key="index" class="flex flex-col gap-1 mb-4">
                    <div class="flex gap-2 items-center">
                        <!-- Dropdown con nombre y precio -->
                        <Dropdown v-model="item.producto_id" :options="productos" optionValue="id"
                            placeholder="Seleccione un producto" class="w-1/2">
                            <!-- Opción seleccionada -->
                            <template #value="slotProps">
                                <span v-if="slotProps.value">
                                    {{
                                        productos.find(p => p.id === slotProps.value)?.nombre
                                    }} -
                                    ${{productos.find(p => p.id === slotProps.value)?.precio}}
                                </span>
                                <span v-else>Seleccione un producto</span>
                            </template>

                            <!-- Opciones desplegables -->
                            <template #option="slotProps">
                                <div>
                                    {{ slotProps.option.nombre }} - ${{ slotProps.option.precio }}
                                </div>
                            </template>
                        </Dropdown>

                        <!-- Cantidad vendida -->
                        <InputNumber v-model="item.cantidad_vendida" placeholder="Cantidad" class="w-1/3" />

                        <!-- Botón para eliminar item -->
                        <Button icon="pi pi-trash" severity="danger" text @click="eliminarItem(index)" />
                    </div>
                </div>

                <!-- ✅ Botón para agregar un nuevo producto -->
                <Button icon="pi pi-plus" label="Agregar Producto" @click="agregarItem" class="mt-2" />
            </div>

        </div>

        <template #footer>
            <Button label="Cancelar" severity="secondary" @click="visible = false" />
            <Button label="Guardar Venta" @click="guardarVenta()" />
        </template>
    </Dialog>

</template>

<script setup>
import { onMounted, ref } from 'vue';
import ventaService from '../../../services/venta.service';
import Swal from "sweetalert2";
import contactoService from '../../../services/contacto.service';
import productoService from '../../../services/producto.service';

const ventas = ref([]);
const totalRecords = ref(0);
const loading = ref(true);
const venta = ref({});
const lazyParams = ref({});
const buscar = ref("");
const errors = ref([]);
const visible = ref(false);
const contactos = ref([]);
const productos = ref([]);

onMounted(() => {
    lazyParams.value = {
        first: 0,
        rows: 10,
        sortField: null,
        sortOrder: null,
    };

    getVentas();
    cargarDatos();
})

const onPage = (event) => {
    lazyParams.value = event;
    getVentas(event);
};

async function cargarDatos() {
    try {
        const resContactos = await contactoService.listar(); // Asume que tienes contactoService
        contactos.value = resContactos.data.data;
        console.log(resContactos.data.data);


        const resProductos = await productoService.listar(); // Asume que tienes productoService
        productos.value = resProductos.data;
    } catch (error) {
        console.error("Error cargando contactos/productos");
    }
}

async function getVentas() {
    try {
        loading.value = true;
        //console.log(lazyParams.value);
        const { data } = await ventaService.listar(lazyParams.value.page + 1, lazyParams.value.rows, buscar.value);

        ventas.value = data.data;
        totalRecords.value = data.total;

        loading.value = false;
    } catch (error) {
        alert("Error al recuperar al listar de ventas")
    }

}

async function guardarVenta() {
    try {
        if (venta.value.id) {
            await ventaService.modificar(venta.value.id, venta.value);
            Swal.fire({
                title: "venta Actualizado!",
                text: "continuar!",
                icon: "success",
            });
        } else {
            await ventaService.guardar(venta.value);
            Swal.fire({
                title: "venta Registrado!",
                text: "continuar!",
                icon: "success",
            });
        }
        venta.value = {};
        getVentas();

        visible.value = false;
    } catch (error) {
        errors.value = error.response.data?.errors;
    }

}

function agregarItem() {
    if (!venta.value.items) venta.value.items = [];
    venta.value.items.push({ producto_id: null, cantidad_vendida: 1 });
}

function eliminarItem(index) {
    venta.value.items.splice(index, 1);
}

async function imprimirCompra(venta) {
    try {
        const response = await ventaService.imprimirCompra(venta.id);

        // console.log(blob);
        // Abre el PDF en una nueva pestaña
        window.open(response.request.responseURL);
    } catch (error) {
        console.error("Error al imprimir:", error);
        Swal.fire("Error", "No se pudo generar el PDF", "error");
    }
}

</script>