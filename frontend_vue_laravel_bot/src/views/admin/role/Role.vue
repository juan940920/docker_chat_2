<template>
<div class="card" v-if="loading">
    <ProgressBar mode="indeterminate" style="height: 16px"></ProgressBar>
</div>
<div v-else>
    <div v-if="roles.length>0 && $can('index','role')">
        <Toolbar class="mb-6">
            <template #start>
                <Button v-if="$can('create','role')" type="button" label="Nuevo Role" @click="visible_editar_role = true"></Button>
            </template>
        </Toolbar>
        <DataTable :value="roles" tableStyle="min-width: 50rem">
            <template #header>
                <div class="flex flex-wrap gap-2 items-center justify-between">
                    <h4 class="m-0">Gestión de Roles</h4>
                </div>
            </template>
        <Column field="id" header="ID"></Column>
        <Column field="name" header="NOMBRE"></Column>
        <Column field="detalle" header="DETALLE"></Column>
        <Column :exportable="false" style="min-width: 12rem">
                <template #body="slotProps">
                    <Button v-if="$can('show','role')" icon="pi pi-box" rounded class="mr-2" @click="editRole(slotProps.data)" />
                    <Button v-if="$can('edit','role')" icon="pi pi-pencil" rounded class="mr-2" @click="modificarRole(slotProps.data)" />
                    <Button v-if="$can('delete','role')" icon="pi pi-trash" rounded severity="danger" @click="confirmDeleteRole(slotProps.data)" />
                </template>
                </Column>
        </DataTable>
    </div>
</div>
<Dialog v-model:visible="dialogVisible" :header= "`Permisos para: ${role.name}`" :style="{ width: '75vw' }" maximizable modal :contentStyle="{ height: '300px' }">
    
    <label for="permiso" class="font-semibold w-24">Nuevo permiso</label>
    <div class="flex items-center gap-4 mb-8">
        <InputText id="nam" class="flex-auto" autocomplete="off" v-model="permiso.name" :invalid="!permiso.name" placeholder="Ingrese nombre"/>
        <InputText id="act" class="flex-auto" autocomplete="off" v-model="permiso.action" :invalid="!permiso.action" placeholder="Ingrese action"/>
        <InputText id="sub" class="flex-auto" autocomplete="off" v-model="permiso.subject" :invalid="!permiso.subject" placeholder="Ingrese subjet"/>
        <InputText id="per" class="flex-auto" autocomplete="off" v-model="permiso.permiso" :invalid="!permiso.permiso" placeholder="Ingrese permiso"/>
        <InputText id="desc" class="flex-auto" autocomplete="off" v-model="permiso.descripcion" placeholder="Ingrese descripcion"/>
    </div>
    <Button v-if="$can('create', 'permiso')" label="Guardar permiso" icon="pi pi-check" @click="guardarPermiso()" />

    <PickList v-model="permisos" dataKey="id" breakpoint="1400px">
        {{ permisos }}
    <template #option="{ option  }">
        {{ option.name }}
    </template>
    </PickList>

    <template #footer>
        <Button label="Guardar cambios" icon="pi pi-check" @click="guardarPermisosRoles()" />
    </template>

</Dialog>

<Dialog v-model:visible="visible_editar_role" modal header="Role" :style="{ width: '25rem' }">
    <div class="flex flex-col gap-6">
        <div>
            <label for="name" class="block font-bold mb-3">Nombre</label>
            <InputText id="name" v-model="role.name" :invalid="!role.name" autofocus fluid />
            <small v-if="errors.name" class="text-red-500">{{ Array.isArray(errors.name) ? errors.name[0] : errors.name }}</small>
        </div>
        <div>
            <label for="desc" class="block font-bold mb-3">Descripción</label>
            <InputText id="desc" v-model="role.detalle" fluid />
        </div>
    </div>
    <template #footer>
        <Button type="button" label="Cancelar" severity="secondary" @click="visible_editar_role = false"></Button>
        <Button type="button" label="Guardar Cambios" @click="guardarCambios()"></Button>
    </template>
</Dialog>

</template>

<script setup>
import { onMounted, ref } from 'vue';
import roleService from '../../../services/role.service.js';
import Swal from 'sweetalert2';
import permisoService from '../../../services/permiso.service.js';

const roles = ref([]);
const lista_permisos = ref([])
const permisos = ref([])
const dialogVisible = ref(false)
const role = ref({})
const permiso = ref({name: "", action: "", subject:"", permiso:"", descripcion:"" })
const visible_editar_role = ref(false)
const errors = ref([])
const loading = ref(true)



onMounted(() => {
    getRoles()
})

const getRoles = async() => {
    loading.value = true;
    const {data} = await roleService.listar();
    roles.value = data.roles;
    lista_permisos.value = data.permisos
    permisos.value = [data.permisos, []];
    loading.value = false;

}


const editRole = (data) => {
    dialogVisible.value = true

    const nuevosDatos = []
    permisos.value[0] = [...lista_permisos.value]

    data.permisos.forEach(element => {

        const {pivot, ...rest} = element;

        const existe = permisos.value[0].some(obj => obj.id == element.id)
        if (existe) {
            permisos.value[0].splice(permisos.value[0].findIndex(obj => obj.id == element.id), 1)
            nuevosDatos.push(rest);
        }
        
    });

    role.value = data
    permisos.value[1] = nuevosDatos
}

const guardarPermisosRoles = async () => {
    dialogVisible.value = false;

    try {
       const {data} = await roleService.actualizarPersmisos(role.value.id, permisos.value[1]);
       roles.value = [];
       console.log(data);
       getRoles();
       Swal.fire({
        title: "Permisos actualizados!",
        text: "Ok Para contuniar ",
        icon: "success"
        });
    } catch (error) {
        alert("Error al actualizar los permisos");
    }

}

const guardarPermiso = async () => {
    await permisoService.guardar(permiso.value)
    getRoles();
    permiso.value = {};
}

const modificarRole = async (data) => {
    visible_editar_role.value = true;
    errors.value =  [];
    role.value = data
}

const guardarCambios = async () => {
    try {
        if (role.value.id) {
            await roleService.modificar(role.value.id, role.value);
            Swal.fire({
                title: "Role Actualizado!",
                text: "continuar!",
                icon: "success",
            });
        } else {
            await roleService.guardar(role.value);
            Swal.fire({
                title: "Role Registrado!",
                text: "continuar!",
                icon: "success",
            });
        }
        role.value = {};
        getRoles();

        visible_editar_role.value = false;
    } catch (error) {
        errors.value = error.response.data?.errors;
    }
}
</script>