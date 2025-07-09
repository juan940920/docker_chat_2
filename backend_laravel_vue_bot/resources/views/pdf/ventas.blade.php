<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura de Venta #{{ $venta['id'] }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }

        .encabezado {
            text-align: center;
            margin-bottom: 30px;
        }

        .empresa-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .empresa-info h3 {
            margin: 0;
        }

        .cliente-info {
            margin-bottom: 20px;
        }

        .cliente-info, .venta-info {
            border: 1px solid #ddd;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .total {
            text-align: right;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>

    <div class="encabezado">
        <h2>Factura de Venta</h2>
        <p><strong>Factura Nº:</strong> {{ $venta['id'] }}</p>
    </div>

    <div class="empresa-info">
        <h3>Nombre de la Empresa</h3>
        <p>Dirección: Calle Ficticia 123, Ciudad XYZ</p>
        <p>NIT: 123456789</p>
        <p>Tel: (+591) 76543210</p>
    </div>

    <div class="cliente-info">
        <strong>Cliente:</strong> {{ $venta['contacto']['nombre_whatsapp'] }}<br>
        <strong>WhatsApp:</strong> {{ $venta['contacto']['nro_whatsapp'] }}<br>
        <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta['created_at'])->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio Unidad (Bs)</th>
                <th>Subtotal (Bs)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta['detalle_ventas'] as $detalle)
                <tr>
                    <td>{{ $detalle['producto']['nombre'] }}</td>
                    <td>{{ $detalle['producto']['descripcion'] ?? 'Sin descripción' }}</td>
                    <td>{{ $detalle['cantidad_vendida'] }}</td>
                    <td>{{ number_format($detalle['precio_unidad'], 2, ',', '.') }}</td>
                    <td>{{ number_format($detalle['subtotal'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="total">Total:</td>
                <td>Bs {{ number_format($venta['total'], 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Esta factura no tiene valor fiscal y fue generada electrónicamente.<br>
        Gracias por su compra.
    </div>

</body>
</html>
