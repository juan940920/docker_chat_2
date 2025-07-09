import Api from "./api.service"

export default{
    listar: () => {
        return Api().get("categoria")
    },
    mostrar: (id) => {
        return Api().get("categoria/"+id);
    },
    guardar: (datos) => {
        return Api().post("categoria/", datos);
    },
    modificar: (id, datos) => {
        return Api().put("categoria/"+id, datos);
    },
    eliminar: (id) => {
        return Api().delete("categoria/"+id);
    }
}