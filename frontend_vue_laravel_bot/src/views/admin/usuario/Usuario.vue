<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button v-if="$can('create', 'user')" label="Nuevo usuario" @click="visible = true, usuario = {};" />
            <Button v-if="$can('index', 'user')" type="button" label="Generar PDF" @click="generarPDF()"></Button>
            <Button v-if="$can('index', 'user')" type="button" label="Generar PDF 2" @click="generarPDF2()"></Button>
        </template>
    </Toolbar>
    <DataTable ref="dt" :value="usuarios" dataKey="id" lazy :totalRecords="totalRecords" :loading="loading"
        :paginator="true" :rows="10" @page="onPage($event)"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[3, 10, 25]"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} usuarios">
        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">Gestión de Usuarios</h4>
                <IconField>
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText placeholder="Buscar..." v-model="buscar" @change="getUsuarios()" />
                </IconField>
            </div>
        </template>
        <Column field="id" header="ID"></Column>
        <Column field="name" header="USUARIO"></Column>
        <Column field="email" header="CORREO ELECTRONICO"></Column>
        <Column :exportable="false" style="min-width: 12rem" header="PERSONALES">
            <template #body="slotProps">
                <div>
                    <Button :label="`${slotProps.data.administrativo ? 'Actualizar' : 'Asignar'}`"
                        @click="datosPersonales(slotProps.data)" />
                </div>
            </template>
        </Column>
        <Column :exportable="false" style="min-width: 12rem" header="ROLES">
            <template #body="slotProps">
                <div>
                    <ul>
                        <Chip :label="rol.name" v-for="(rol, pos) in slotProps.data.roles" :key="pos" />
                    </ul>
                </div>
            </template>
        </Column>
        <Column field="created_at" header="CREADO POR"></Column>
        <Column :exportable="false" style="min-width: 12rem">
            <template #body="slotProps">
                <Button icon="pi pi-user" rounded class="mr-2" @click="editRoles(slotProps.data)" />
                <Button icon="pi pi-pencil" rounded class="mr-2" @click="editUsuario(slotProps.data)" />
                <Button icon="pi pi-trash" rounded severity="danger" @click="confirmDeleteUsuario(slotProps.data)" />
            </template>
        </Column>
    </DataTable>

    <Dialog v-model:visible="visible_persona" modal header="Guardar datos administrativas" :style="{ width: '25rem' }">
        <span class="text-surface-500 dark:text-surface-400 block mb-8">Guardar información administrativa.</span>
        <div class="flex items-center gap-4 mb-4">
            <label for="nom" class="font-semibold w-24">Nombres</label>
            <InputText id="nom" class="flex-auto" autocomplete="off" v-model="administrativo.nombres" />
        </div>
        <div class="flex items-center gap-4 mb-8">
            <label for="ap" class="font-semibold w-24">Apellidos</label>
            <InputText id="ap" class="flex-auto" autocomplete="off" v-model="administrativo.apellidos" />
        </div>
        <div class="flex items-center gap-4 mb-8">
            <label for="tel" class="font-semibold w-24">Mat. Profesional</label>
            <InputText id="tel" class="flex-auto" autocomplete="off" v-model="administrativo.matricula" />
        </div>
        <div class="flex items-center gap-4 mb-8">
            <label for="dir" class="font-semibold w-24">Consultorio</label>
            <InputText id="dir" class="flex-auto" autocomplete="off" v-model="administrativo.cosultorio" />
        </div>
        <div class="flex items-center gap-4 mb-8">
            <label for="ap" class="font-semibold w-24">Unidad</label>
            <Select v-model="administrativo.unidad_id" :options="unidades"
                :optionLabel="(unidad) => unidad.centro.nombre + ' - ' + unidad.nombre" optionValue="id"
                placeholder="Selecciona unidad" class="w-full md:w-56" />
        </div>
        <div class="flex justify-end gap-2">
            <Button type="button" label="Cancelar" severity="secondary" @click="visible = false"></Button>
            <Button type="button" label="Gusrdar Dato Personales" @click="guardarDatosPersonales()"></Button>
        </div>
    </Dialog>
    <Dialog v-model:visible="visible" modal header="Usuario" :style="{ width: '25rem' }">
        <div class="flex flex-col gap-6">
            <div>
                <label for="username" class="block font-bold mb-3">Usuario</label>
                <InputText id="username" v-model="usuario.name" :invalid="!usuario.name" autofocus
                    placeholder="Ingrese usuario" fluid />
                <small v-if="errors.name" class="text-red-500">
                    {{ Array.isArray(errors.name) ? errors.name[0] : errors.name }}
                </small>
            </div>
            <div>
                <label for="email" class="block font-bold mb-3">Correo Electronico</label>
                <InputText id="email" v-model="usuario.email" :invalid="!usuario.email"
                    placeholder="Ingrese correo electronico" fluid />
                <small v-if="errors.email" class="text-red-500">
                    {{ Array.isArray(errors.email) ? errors.email[0] : errors.email }}
                </small>
            </div>
            <div>
                <label for="pass" class="block font-bold mb-3">Contraseña</label>
                <InputText id="pass" v-model="usuario.password" :invalid="!usuario.password"
                    placeholder="Ingrese contraseña" fluid />
                <small v-if="errors.password" class="text-red-500">
                    {{ Array.isArray(errors.password) ? errors.password[0] : errors.password }}
                </small>
            </div>
        </div>
        <template #footer>
            <Button type="button" label="Cancelar" severity="secondary" @click="visible = false"></Button>
            <Button type="button" label="Guardar Usuario" @click="guardarUsuario()"></Button>
        </template>
    </Dialog>
    <Dialog v-model:visible="visible_roles" modal header="Roles" :style="{ width: '40rem' }">
        <span class="text-surface-500 dark:text-surface-400 block mb-8">Verifique los Roles asignados.</span>
        <div class="flex items-center gap-4 mb-4">
            <label for="username" class="font-semibold w-24">Roles</label>
            <MultiSelect v-model="roleSelecteds" display="chip" track-by="id" :options="roles" optionLabel="name" filter
                placeholder="Seleccionar roles" multiple :maxSelectedLabels="4" class="w-full md:w-100" />
        </div>
        <div class="flex justify-end gap-2">
            <Button type="button" label="Cancelar" severity="secondary" @click="visible_roles = false"></Button>
            <Button type="button" label="Guardar Roles" @click="guardarRolesAsignados()"></Button>
        </div>
    </Dialog>
    <Dialog v-model:visible="visible_pdf" modal header="PDF Visual">
        <Button type="button" label="Descargar PDF" @click="generarPDF()"></Button>
        <VuePDF :pdf="pdf" />

        <div class="flex justify-end gap-2">
            <Button type="button" label="Cerrar" severity="secondary" @click="visible_pdf = false"></Button>
        </div>
    </Dialog>
    <VuePDF :pdf="pdf" />

</template>

<script setup>
import { onMounted, ref } from 'vue';
import usuarioService from '../../../services/usuario.service';
import roleService from '../../../services/role.service';
import { usePDF, VuePDF } from '@tato30/vue-pdf';
import unidadService from '../../../services/unidad.service';
import Swal from "sweetalert2";

const usuarios = ref([]);
const totalRecords = ref(0)
const loading = ref(true)
const usuario = ref({})
const visible = ref(false)
const visible_roles = ref(false)
const roles = ref([])
const roleSelecteds = ref([])
const pdfUrl = ref(null)
const visible_persona = ref(false);
const unidades = ref([]);
const lazyParams = ref({});
const buscar = ref("")
const errors = ref([])

const administrativo = ref({});

const dato_user_id = ref(null);

const visible_pdf = ref(false);
const { pdf, pages } = usePDF(pdfUrl)

onMounted(() => {
    lazyParams.value = {
        first: 0,
        rows: 10,
        sortField: null,
        sortOrder: null,
    };

    getUsuarios();
    getRoles();
    getUnidades();
})

async function getUsuarios() {
    try {
        loading.value = true;
        //console.log(lazyParams.value);
        const { data } = await usuarioService.listar(lazyParams.value.page + 1, lazyParams.value.rows, buscar.value);

        usuarios.value = data.data;
        totalRecords.value = data.total;

        loading.value = false;
    } catch (error) {
        alert("Error al recuperar la lista de usuarios")
    }

}

async function getRoles() {
    const { data } = await roleService.listar();
    roles.value = data.roles;
}

async function getUnidades() {
    const { data } = await unidadService.listar();
    unidades.value = data;
}

const onPage = (event) => {
    lazyParams.value = event;
    getUsuarios(event);
};

async function guardarUsuario() {
    try {
        if (usuario.value.id) {
            await usuarioService.modificar(usuario.value.id, usuario.value);
            Swal.fire({
                title: "Usuario Actualizado!",
                text: "continuar!",
                icon: "success",
            });
        } else {
            await usuarioService.guardar(usuario.value);
            Swal.fire({
                title: "Usuario Registrado!",
                text: "continuar!",
                icon: "success",
            });
        }
        usuario.value = {};
        getUsuarios();

        visible.value = false;
    } catch (error) {
        errors.value = error.response.data?.errors;
    }

}

function editUsuario(data) {
    usuario.value = data;
    errors.value = [];
    visible.value = true;

}

async function confirmDeleteUsuario(data) {
    try {
        if (confirm("¿Esta seguro de eliminar el usuario?")) {
            await usuarioService.eliminar(data.id);
            getUsuarios();
        }
    } catch (error) {

    }
}

const editRoles = (data) => {
    const selectedIds = data.roles.map(r => r.id);

    roleSelecteds.value = roles.value.filter(role =>
        selectedIds.includes(role.id)
    );

    usuario.value = data;
    visible_roles.value = true;
}

const guardarRolesAsignados = async () => {
    if (roleSelecteds.value.length > 0) {
        const roles_id = roleSelecteds.value.map(role => role.id);

        await usuarioService.asignarRoles(usuario.value.id, { roles_id });

        Swal.fire({
            title: "Roles Asignados!",
            text: "continuar!",
            icon: "success",
        });

        visible_roles.value = false;
        getUsuarios();
    }
}

const generarPDF = async () => {
    const respuesta = await usuarioService.generarReportePDF();

    const url = window.URL.createObjectURL(new Blob([respuesta.data], { type: 'application/pdf' }));

    console.log(respuesta);


    const link = document.createElement('a');
    link.href = url;

    link.setAttribute('download', 'lista-usuarios.pdf');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

const generarPDF2 = async () => {
    visible_pdf.value = true;
    const respuesta = await usuarioService.generarReportePDF();

    const blop = new Blob([respuesta.data], { type: 'application//pdf' });
    pdfUrl.value = window.URL.createObjectURL(blop);

    const link = document.createElement('a');
    link.href = url;

    link.setAttribute('download', 'lista-usuarios.pdf');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

const datosPersonales = (user) => {
    visible_persona.value = true;
    dato_user_id.value = user.id;

    if (user.administrativo) {
        administrativo.value = user?.administrativo;
    } else {
        administrativo.value = {};
    }
}

const guardarDatosPersonales = async () => {
    try {
        if (administrativo.value.id) {
            administrativo.value.user_id = dato_user_id.value;
            usuarioService.actualizarDatosPersonales(administrativo.value);
            Swal.fire({
                title: "Dato Personal Actualizado!",
                text: "continuar!",
                icon: "success",
            });
        } else {
            administrativo.value.user_id = dato_user_id.value;
            usuarioService.asignarDatosPersonales(administrativo.value);
            Swal.fire({
                title: "Dato Personal registrado!",
                text: "continuar!",
                icon: "success",
            });
        }

        visible_persona.value = false;

        getUsuarios();
    } catch (error) {
        alert("Error al registrar los datos personales")
    }
}

</script>