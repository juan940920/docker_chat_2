import Api from "./api.service"

export default{
    listar: (page = 1, limit = 5, q = "") => {
        return Api().get(`/usuario?page=${page}&limit=${limit}&q=`+q)
    },
    mostrar: (id) => {
        return Api().get("usuario/"+id);
    },
    guardar: (datos) => {
        return Api().post("usuario/", datos);
    },
    modificar: (id, datos) => {
        return Api().put("usuario/"+id, datos);
    },
    eliminar: (id) => {
        return Api().delete("usuario/"+id);
    },
    asignarRoles: (id, roles_id) => {
        return Api().post("usuario/"+id+"/asignar-roles", roles_id);
    },
    generarReportePDF: () => {
        return Api().get("/usuario/reporte-pdf", {responseType: 'blob'});
    },
    asignarDatosPersonales: (datos) => {
        return Api().post( `usuario/${datos.user_id}/asignar-datos-personales`, datos);
    },
    actualizarDatosPersonales: (datos) => {
        return Api().put( `administrativo/${datos.id}`, datos);
    }
}