<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button v-if="$can('manage','all')" label="Nuevo Contacto" @click="visible = true, contacto = {};" />
        </template>
    </Toolbar>
    <DataTable ref="dt" :value="contactos" dataKey="id" lazy :totalRecords="totalRecords" :loading="loading"
        :paginator="true" :rows="10" @page="onPage($event)"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[3, 10, 25]"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} contactos">
        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">Gestión de Contactos</h4>
                <IconField>
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText placeholder="Buscar..." v-model="buscar" @change="getContactos()" />
                </IconField>
            </div>
        </template>
        <Column field="id" header="ID"></Column>
        <Column field="nombre_whatsapp" header="NOMBRE WHAPSAPP"></Column>
        <Column field="nro_whatsapp" header="NRO WHAPSAPP"></Column>
        <Column field="created_at" header="CREADO"></Column>
        <Column :exportable="false" style="min-width: 12rem">
            <template #body="slotProps">
                <Button v-if="$can('manage','all')" icon="pi pi-pencil" rounded class="mr-2" @click="editContacto(slotProps.data)" />
                <Button v-if="$can('manage','all')" icon="pi pi-trash" rounded severity="danger" @click="" />
            </template>
        </Column>
    </DataTable>
    <Dialog v-model:visible="visible" modal header="contacto" :style="{ width: '25rem' }">
        <div class="flex flex-col gap-6">
            <div>
                <label for="nombre_whatsapp" class="block font-bold mb-3">Nombre whapsapp</label>
                <InputText id="nombre_whatsapp" v-model="contacto.nombre_whatsapp" :invalid="!contacto.nombre_whatsapp" autofocus
                    placeholder="Ingrese contacto" fluid />
            </div>
            <div>
                <label for="nro_whatsapp" class="block font-bold mb-3">Nro whapsapp</label>
                <InputText id="nro_whatsapp" v-model="contacto.nro_whatsapp" :invalid="!contacto.nro_whatsapp"
                    placeholder="Ingrese correo electronico" fluid />
            </div>
        </div>
        <template #footer>
            <Button type="button" label="Cancelar" severity="secondary" @click="visible = false"></Button>
            <Button type="button" label="Guardar contacto" @click="guardarContacto()"></Button>
        </template>
    </Dialog>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import contactoService from '../../../services/contacto.service';
import Swal from "sweetalert2";

const contactos = ref([]);
const totalRecords = ref(0)
const loading = ref(true)
const contacto = ref({})
const lazyParams = ref({});
const buscar = ref("")
const errors = ref([])
const visible = ref(false)

onMounted(() => {
    lazyParams.value = {
        first: 0,
        rows: 10,
        sortField: null,
        sortOrder: null,
    };

    getContactos();
})

const onPage = (event) => {
    lazyParams.value = event;
    getContactos(event);
};

async function getContactos() {
    try {
        loading.value = true;
        //console.log(lazyParams.value);
        const { data } = await contactoService.listar(lazyParams.value.page + 1, lazyParams.value.rows, buscar.value);

        contactos.value = data.data;
        totalRecords.value = data.total;

        loading.value = false;
    } catch (error) {
        alert("Error al recuperar la lista de contactos")
    }

}

async function guardarContacto() {
    try {
        if (contacto.value.id) {
            await contactoService.modificar(contacto.value.id, contacto.value);
            Swal.fire({
                title: "Contacto Actualizado!",
                text: "continuar!",
                icon: "success",
            });
        } else {
            await contactoService.guardar(contacto.value);
            Swal.fire({
                title: "Contacto Registrado!",
                text: "continuar!",
                icon: "success",
            });
        }
        contacto.value = {};
        getContactos();

        visible.value = false;
    } catch (error) {
        errors.value = error.response.data?.errors;
    }

}

function editContacto(data) {
    contacto.value = data;
    errors.value = [];
    visible.value = true;

}

</script>