const { default: makeWASocket, useMultiFileAuthState, DisconnectReason } = require("baileys");
const qrcode = require("qrcode");
const express = require("express");
const { getContacto, crearContacto, obtenerProductos, crearVenta, descargarPDFVentaEnMemoria } = require('./api.js');

const app = express();
const port = 3001;

let qrBase64 = "";
const estadosUsuario = {}; // Guardar estados temporales si quieres
const userContext = {};    // Guarda contexto por usuario (menús, etc)
const productosUsuario = {};    // Lista de productos que ve el usuario
const carritoUsuario = {};      // Lista de compras por usuario
const contactoIds = {};          // Almacena el contacto_id  por usuario

app.get("/qr", (req, res) => {
    if (qrBase64) {
        res.send(`
            <html>
                <body>
                    <h2>Escanea el código QR para iniciar sesión en WhatsApp:</h2>
                    <img src="${qrBase64}" />
                </body>
            </html>
        `);
    } else {
        res.send("⏳ QR aún no generado. Intenta en unos segundos.");
    }
});

app.listen(port, () => {
    console.log(`🌐 Servidor QR iniciado en http://localhost:${port}/qr`);
});

async function connectToWhatsApp () {
    const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys');
    const sock = makeWASocket({ auth: state, printQRInTerminal: false });

    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            try {
                qrBase64 = await qrcode.toDataURL(qr);
                console.log(`✅ QR generado. Ve a http://localhost:${port}/qr`);
            } catch (err) {
                console.error("❌ Error generando QR:", err);
            }
        }

        if (connection === 'close') {
            const shouldReconnect = (lastDisconnect.error)?.output?.statusCode !== DisconnectReason.loggedOut;
            console.log('Conexion cerrada ', lastDisconnect.error, ', reconectando ', shouldReconnect);
            if (shouldReconnect) connectToWhatsApp();
        } else if (connection === 'open') {
            console.log('✅ CONEXIÓN ABIERTA!!');
        }
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('messages.upsert', async event => {
    for (const m of event.messages) {
        const id = m.key.remoteJid;
        const nombre = m.pushName || "Usuario";

        if (event.type !== 'notify' || m.key.fromMe || id.includes('@g.us') || id.includes('@broadcast')) return;

        await sock.readMessages([m.key]);

        let mi_contacto;
        try {
            mi_contacto = await getContacto(id);
            if (!mi_contacto) {
                mi_contacto = await crearContacto(nombre, id);
            }
            contactoIds[id] = mi_contacto.id;
        } catch (error) {
            console.error("❌ Error al consultar o crear contacto:", error.message);
            return;
        }

        await sock.sendPresenceUpdate("composing", id);
        await sleep(2000);

        let mensaje = m.message?.conversation || m.message?.extendedTextMessage?.text || "";
        mensaje = mensaje.toUpperCase();

        if (!userContext[id]) {
            userContext[id] = { menuActual: "main" };
            await enviarMenu(sock, id, "main", nombre);
            return;
        }

        console.log("Mensaje recibido: ", mensaje);

        // 💡 Manejo cuando esperamos que el usuario envíe la categoría
        if (userContext[id].esperandoCategoria) {
            const categoria = mensaje.trim().toUpperCase();
            try {
                const productos = await obtenerProductos(categoria);

                if (!productos || productos.length === 0) {
                    await sock.sendMessage(id, {
                        text: `⚠️ No se encontraron productos en la categoría *${categoria}*. Intenta con otra.`
                    });
                    return;
                }

                productosUsuario[id] = productos;
                carritoUsuario[id] = [];

                for (let i = 0; i < productos.length; i++) {
                    const producto = productos[i];
                    await sock.sendMessage(id, {
                        text: `*${i + 1}. ${producto.nombre}* (💲${producto.precio} / 📦${producto.stock})\n${producto.descripcion || "Sin desc."}\n👉 Escribe *COMPRAR ${i + 1} x cantidad* para agregar al carrito`
                        //text: `*${i + 1}. ${producto.nombre}* - 💲${producto.precio} | 📦${producto.stock}\n${producto.descripcion || "Sin descripción"}\n👉 *COMPRAR ${i + 1} x cant.*`

                    });
                }

                await sock.sendMessage(id, {
                    text: "👉 Cuando termines, escribe *FINALIZAR COMPRA* para confirmar tu pedido."
                });

                // ✅ Ya no esperamos categoría
                userContext[id].esperandoCategoria = false;
            } catch (e) {
                console.error("❌ Error al obtener productos por categoría:", e.message);
                await sock.sendMessage(id, {
                    text: "❌ Ocurrió un error al buscar productos. Intenta más tarde."
                });
            }
            return;
        }


        const menuActual = userContext[id].menuActual;
        const menu = menuData[menuActual];
        const opcion = menu.options[mensaje];

        const matchCompra = mensaje.match(/^COMPRAR\s+(\d+)\s*[Xx*]\s*(\d+)$/i);

        // 🛒 MANEJAR COMPRAR X
        if (matchCompra && productosUsuario[id]) {
            const index = parseInt(matchCompra[1]) - 1;
            const cantidad = parseInt(matchCompra[2]);

            if (isNaN(index) || isNaN(cantidad) || cantidad <= 0) {
                await sock.sendMessage(id, { text: "❌ Por favor usa el formato correcto: *COMPRAR número_producto x cantidad*. Ej: *COMPRAR 2 x 3*" });
                return;
            }
            const producto = productosUsuario[id][index];

            if (!producto) {
                await sock.sendMessage(id, { text: "❌ Número de producto inválido." });
                return;
            }

            if (!carritoUsuario[id]) carritoUsuario[id] = [];

            const item = {
                producto_id: producto.id,
                cantidad_vendida: cantidad
            };

            carritoUsuario[id].push(item);

            console.log(`🛒 Producto agregado al carrito por ${id}:`, item);
            console.log(`🧺 Carrito actual de ${id}:`, carritoUsuario[id]);

            await sock.sendMessage(id, {
                text: `✅ *${producto.nombre}* agregado al carrito. Puedes seguir agregando más productos o escribir *FINALIZAR COMPRA*.`
            });

            return;
        }

        // ✅ MANEJAR FINALIZAR COMPRA
        if (mensaje === "FINALIZAR COMPRA") {
            const contacto_id = contactoIds[id];
            const items = carritoUsuario[id];

            if (!contacto_id || !items || items.length === 0) {
                await sock.sendMessage(id, {
                    text: "❌ No tienes productos en tu carrito. Usa *COMPRAR X* para agregarlos."
                });
                return;
            }

            const ventaData = {
                contacto_id,
                items
            };

            console.log("🛒 Datos de la venta que se enviarán a la API:");
            console.log(JSON.stringify(ventaData, null, 2));

            try {
                const resultado = await crearVenta(ventaData);

                console.log(resultado.venta);
                

                if (resultado && resultado.venta.id) {
                    await sock.sendMessage(id, {
                        text: "🧾 ¡Tu compra fue registrada con éxito! 🎉\nGenerando tu nota de compra en PDF..."
                    });

                    const buffer = await descargarPDFVentaEnMemoria(resultado.venta.id);
                    
                    if (buffer) {
                        await sock.sendMessage(id, {
                            document: buffer,
                            mimetype: 'application/pdf',
                            fileName: `nota_venta_${resultado.venta.id}.pdf`,
                            caption: '📄 Aquí tienes tu nota de compra en PDF.'
                        });
                    } else {
                        await sock.sendMessage(id, {
                            text: "⚠️ La venta fue registrada, pero hubo un error al generar tu PDF. Puedes solicitarlo luego."
                        });
                    }
                    carritoUsuario[id] = []; // Limpia carrito
                    
                    // ✅ NUEVA LÍNEA: volver al menú `chatbots_ventas`
                    userContext[id].menuActual = "chatbots_ventas";
                    await enviarMenu(sock, id, "chatbots_ventas");
                } else {
                    throw new Error("Respuesta vacía");
                }
            } catch (e) {
                console.error("❌ Error al procesar venta:", e.message);
                await sock.sendMessage(id, {
                    text: "❌ Ocurrió un error al procesar tu compra. Intenta de nuevo más tarde."
                });
            }

            return;
        }

        // Resto del manejo de menús
        if (opcion) {
            if (mensaje === "1" && menuActual === "chatbots_ventas") {
                userContext[id].esperandoCategoria = true;
                await sock.sendMessage(id, {
                    text: "🔍 Por favor, escribe la *categoría* de producto que deseas ver. Ejemplo: *COMPUTADORAS*, *ELECTRONICOS*, *CELULARES*, etc."
                });
                return;
            }

            if (opcion.respuesta) {
                const tipo = opcion.respuesta.tipo;
                const msg = opcion.respuesta.msg;

                if (tipo === "text") {
                    await sock.sendMessage(id, { text: msg });
                } else if (tipo === "image") {
                    await sock.sendMessage(id, {
                        image: { url: msg.url },
                        caption: "📘 Aquí tienes el catálogo técnico."
                    });
                } else if (tipo === "location") {
                    await sock.sendMessage(id, {
                        location: {
                            degreesLatitude: parseFloat(msg.degreesLatitude),
                            degreesLongitude: parseFloat(msg.degreesLongitude),
                            address: msg.address
                        }
                    });
                } else if (tipo === "contacto") {
                    await sock.sendMessage(id, {
                        text: "Gracias por contactarnos. Un técnico se comunicará con usted a la brevedad. Si requiere atención inmediata, puede contactarnos directamente al siguiente número:"
                    });

                    const vcard = 'BEGIN:VCARD\n'
                        + 'VERSION:3.0\n'
                        + 'FN:Juan Carlos\n'
                        + 'TEL;type=CELL;type=VOICE;waid=59161127371:+591 61127371\n'
                        + 'END:VCARD';

                    await sock.sendMessage(id, {
                        contacts: {
                            displayName: 'Juan Carlos',
                            contacts: [{ vcard }]
                        }
                    });
                    return;
                }
            }

            if (opcion.submenu) {
                userContext[id].menuActual = opcion.submenu;
                await enviarMenu(sock, id, opcion.submenu, nombre);
            }
        } else {
            await sock.sendMessage(id, { text: "Por favor elige una opción válida del menú." });
            await enviarMenu(sock, id, menuActual);
        }
    }
});

}

connectToWhatsApp();

function sleep(ms){
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Cambia aquí la función enviarMenu
async function enviarMenu(sock, id, menuKey, nombre = "Usuario") {
    const menu = menuData[menuKey];

    // Personaliza el mensaje con el nombre del usuario
    const mensajePersonalizado = typeof menu.mensaje === "function"
    ? menu.mensaje(nombre)
    : menu.mensaje.replace("{nombre}", nombre);

    const optionText = Object.entries(menu.options)
        .map(([key, option]) => `👉 *${key}*: ${option.text}`)
        .join("\n");

    const menuMensaje = `${mensajePersonalizado}\n${optionText}\n\n> *Por favor, indícanos la opción de tu interés.*`;

    await sock.sendMessage(id, { text: menuMensaje });
}


const menuData = {
    main: {
        mensaje: (nombre) => `
        ¡Hola ${nombre} 👋! ¿Te interesa *automatizar tus procesos* y *aumentar tus ventas* sin complicaciones?
        Te presento nuestro *chatbot inteligente* conectado a una *API* y a una *base de datos*, ideal para tu negocio:
            - *Disponible 24/7*: responde consultas, envía información y gestiona pedidos automáticamente.  
            - *Impulsado con inteligencia artificial*: analiza conversaciones, registra datos y toma decisiones.  
            - *Integración con tu sistema*: conéctalo fácilmente a tu CRM, ERP o cualquier sistema vía API.  
            - *Asistencia humana*: si el cliente lo necesita, se transfiere a un asesor real.  
        Funciona perfecto para ventas, soporte, reservas, agendas médicas y más.
        Selecciona una opción para ver un demo de ventas. 👇
        `.split('\n').map(line => line.trim()).join('\n'),

        options: {
            A: {
                text: "Ver cómo responde el chatbot dinamico 🔁",
                submenu: "chatbots_ventas"
            },
            B: {
                text: "Ver cómo responde el chatbot con IA 🧠",
                respuesta: {
                    tipo: "text",
                    msg: `💬 Actualmente estamos actualizando la demo de IA.
                        Pero no te preocupes, la IA permite:
                        - Entender lo que escribe tu cliente (incluso con errores).
                        - Guardar sus respuestas en tablas o formularios.
                        - Tomar decisiones automáticas.
                        📲 Contáctanos si deseas ver una demo personalizada.
                        `.split('\n').map(line => line.trim()).join('\n')
                }
            },
            C: {
                text: "Si deseas conocer más detalles sobre el demo chatbot con API REST o acceder a una demostración funcional, por favor contacta al desarrollador📲",
                respuesta: {
                    tipo: "contacto", // Marca personalizada para saber que es una vCard
                    msg: null
                }
            }
        }
    },
    chatbots_ventas: {
        mensaje: (nombre) => `¡Hola ${nombre} 👋! Este es un demo de chatboot dinámico en ventas.
        Bienvenido a *[Nombre de la Empresa]*, especialistas en tecnología y soluciones informáticas.  
        Nuestro *asistente virtual* está disponible para ayudarte a consultar productos, ubicación, asesoría o catálogo técnico.
        `.split('\n').map(line => line.trim()).join('\n'),
  
        options: {
            1: {
                text: "Ver productos disponibles 🖥️"
            },
            2: {
                text: "Ver ubicación de nuestras oficinas 📍",
                respuesta: {
                    tipo: "location",
                    msg: {
                        degreesLatitude: "19.432608",
                        degreesLongitude: "-99.133209",
                        address: "Av. Paseo de la Reforma 123, Ciudad de México, CDMX."
                    }
                }
            },
            3: {
                text: "Descargar catálogo técnico de productos 📘",
                respuesta: {
                    tipo: "image",
                    msg: {
                    url: "https://static.vecteezy.com/system/resources/previews/015/792/198/non_2x/catalog-or-catalogue-or-product-catalog-template-vector.jpg"
                    }
                }
            },
            4: {
                text: "Hablar con un asesor de ventas 👨‍💼",
                respuesta: {
                    tipo: "text",
                    msg: `✅ Un asesor se pondrá en contacto contigo en breve.

                    También puedes escribirnos directamente a: +1234567890 📞`
                }
            },
            5: {
                text: "Volver al menú principal 🔙",
                submenu: "main"
            }
        }
    }


}
