import Api from "./api.service"

export default{
    listar: (page = 1, limit = 5, q = "") => {
        return Api().get(`/contacto?page=${page}&limit=${limit}&q=`+q)
    },
    mostrar: (id) => {
        return Api().get("contacto/"+id);
    },
    guardar: (datos) => {
        return Api().post("contacto/", datos);
    },
    modificar: (id, datos) => {
        return Api().put("contacto/"+id, datos);
    },
    eliminar: (id) => {
        return Api().delete("contacto/"+id);
    }
}