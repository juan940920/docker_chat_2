const axios = require('axios');
const fs = require('fs');
const path = require('path');
const { API_BASE_URL, SANCTUM_TOKEN } = require('./config.js');

const api = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        Authorization: `Bearer ${SANCTUM_TOKEN}`
    }
});

async function getContacto(id) {
    try {
        const res = await api.get(`/contacto/${id}`);
        return res.data;
    } catch (err) {
        if (err.response && err.response.status === 404) {
            return null;
        }
        throw err;
    }
}

async function crearContacto(nombre, id) {
    const res = await api.post(`/contacto`, {
        nombre_whatsapp: nombre,
        nro_whatsapp: id
    });
    return res.data;
}

async function obtenerProductos(categoria = "") {
    try {
        const url = categoria
            ? `/producto?categoria=${encodeURIComponent(categoria)}`
            : `/producto`;

        const response = await api.get(url);
        return response.data;
    } catch (err) {
        console.error("❌ Error al obtener productos:", err.message);
        return [];
    }
}

async function crearVenta(ventaData) {
    try {
        const response  = await api.post(`/venta`, ventaData);
        return response.data;
    } catch (err) {
        console.error("❌ Error al registrar la venta:", err.response?.data || err.message);
        return null;
    }
}

async function descargarPDFVentaEnMemoria(ventaId) {
    try {
        const url = `${API_BASE_URL}/venta/reporte-pdf/${ventaId}`;
        const response = await axios.get(url, {
            responseType: 'arraybuffer'
        });
        return Buffer.from(response.data);
    } catch (err) {
        console.error("❌ Error al descargar PDF en memoria:", err.message);
        return null;
    }
}

module.exports = {
    getContacto,
    crearContacto,
    obtenerProductos,
    crearVenta,
    descargarPDFVentaEnMemoria
};
