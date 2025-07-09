import Api from "./api.service"

export default{
    listar: () => {
        return Api().get("permiso")
    },
    mostrar: (id) => {
        return Api().get("permiso/"+id);
    },
    guardar: (datos) => {
        return Api().post("permiso/", datos);
    },
    modificar: (id, datos) => {
        return Api().put("permiso/"+id, datos);
    },
    eliminar: (id) => {
        return Api().delete("permiso/"+id);
    },
}