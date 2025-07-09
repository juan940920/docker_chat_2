import Api from "./api.service"

export default{
    listar: (page = 1, limit = 5, q = "") => {
        return Api().get(`/venta?page=${page}&limit=${limit}&q=`+q)
    },
    mostrar: (id) => {
        return Api().get("venta/"+id);
    },
    guardar: (datos) => {
        return Api().post("venta/", datos);
    },
    modificar: (id, datos) => {
        return Api().put("venta/"+id, datos);
    },
    eliminar: (id) => {
        return Api().delete("venta/"+id);
    },
    imprimirCompra: (id) => {
        return Api({ responseType: "blob" }).get("/venta/reporte-pdf/"+id);
    }
}