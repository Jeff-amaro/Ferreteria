<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');
        $brands = Brand::pluck('id', 'name');
        $suppliers = Supplier::pluck('id', 'ruc');

        $products = [
            ['HERR-001', 'Martillo de uña 16 oz', 'Herramientas manuales', 'Truper', '20512345678', 38.90, 24.00, 42, 10, 'unidad', 'Martillo de acero forjado con mango de fibra de vidrio.', 'Peso: 16 oz | Mango: fibra de vidrio | Acabado: pulido'],
            ['HERR-002', 'Juego de destornilladores 6 piezas', 'Herramientas manuales', 'Stanley', '20512345678', 64.50, 41.00, 28, 8, 'juego', 'Set de destornilladores planos y Phillips con puntas magnéticas.', '6 piezas | Acero cromo vanadio | Mango bimaterial'],
            ['HERR-003', 'Llave ajustable 10"', 'Herramientas manuales', 'Truper', '20512345678', 45.00, 29.50, 6, 12, 'unidad', 'Llave ajustable de acero al cromo con apertura máxima de 30 mm.', 'Medida: 10" | Acero: cromo vanadio | Acabado: niquelado'],
            ['HERR-004', 'Cinta métrica 8 m', 'Herramientas manuales', 'Stanley', '20512345678', 27.90, 16.00, 55, 15, 'unidad', 'Cinta métrica con freno y clip metálico.', 'Longitud: 8 m | Ancho: 25 mm | Carcasa: ABS'],
            ['ELEC-001', 'Taladro percutor 650W', 'Herramientas eléctricas', 'Bosch', '20611223344', 329.00, 245.00, 14, 5, 'unidad', 'Taladro percutor de 650W con velocidad variable y mandril de 13 mm.', 'Potencia: 650W | Mandril: 13 mm | 2 funciones'],
            ['ELEC-002', 'Amoladora angular 4 1/2"', 'Herramientas eléctricas', 'Makita', '20611223344', 289.00, 210.00, 9, 4, 'unidad', 'Amoladora de 720W para corte y desbaste.', 'Potencia: 720W | Disco: 4 1/2" | 11.000 rpm'],
            ['ELEC-003', 'Sierra circular 7 1/4"', 'Herramientas eléctricas', 'Dewalt', '20611223344', 459.00, 355.00, 3, 4, 'unidad', 'Sierra circular profesional con guía láser.', 'Potencia: 1.400W | Disco: 7 1/4" | Profundidad: 65 mm'],
            ['ELEC-004', 'Atornillador inalámbrico 12V', 'Herramientas eléctricas', 'Bosch', '20611223344', 249.00, 180.00, 18, 6, 'unidad', 'Atornillador con batería de litio y dos velocidades.', 'Voltaje: 12V | Torque: 30 Nm | Batería: 2.0 Ah'],
            ['PLOM-001', 'Tubo PVC SAP 1/2" x 5 m', 'Plomería', 'Pavco', '20699887766', 18.50, 11.20, 120, 30, 'unidad', 'Tubo PVC para agua potable, presión 10 bar.', 'Diámetro: 1/2" | Longitud: 5 m | Norma: NTP 399.002'],
            ['PLOM-002', 'Codo PVC 1/2" x 90°', 'Plomería', 'Pavco', '20699887766', 1.20, 0.60, 300, 80, 'unidad', 'Codo de PVC para conexión de agua a presión.', 'Diámetro: 1/2" | Ángulo: 90° | Material: PVC'],
            ['PLOM-003', 'Llave de paso esférica 1/2"', 'Plomería', 'Pavco', '20699887766', 24.90, 14.00, 34, 10, 'unidad', 'Válvula esférica de bronce con palanca.', 'Diámetro: 1/2" | Material: bronce | Presión: 16 bar'],
            ['PLOM-004', 'Inodoro one piece blanco', 'Plomería', 'Pavco', '20699887766', 389.00, 290.00, 7, 3, 'unidad', 'Inodoro de una pieza con sistema dual flush.', 'Consumo: 4.8/3 L | Acabado: blanco | Asiento incluido'],
            ['ELEC-005', 'Cable THW 2.5 mm² x 100 m', 'Electricidad', '3M', '20512345678', 189.00, 140.00, 22, 8, 'rollo', 'Cable de cobre para instalaciones eléctricas.', 'Sección: 2.5 mm² | Longitud: 100 m | 450/750V'],
            ['ELEC-006', 'Interruptor simple 10A', 'Electricidad', '3M', '20512345678', 8.90, 4.50, 90, 25, 'unidad', 'Interruptor de pared simple, línea blanca.', 'Corriente: 10A | Voltaje: 220V | Acabado: blanco'],
            ['ELEC-007', 'Tomacorriente doble con tierra', 'Electricidad', '3M', '20512345678', 12.50, 6.80, 75, 20, 'unidad', 'Tomacorriente doble de 16A con puesta a tierra.', 'Corriente: 16A | Voltaje: 220V | Universal'],
            ['PINT-001', 'Pintura látex blanca 1 galón', 'Pinturas', 'Sika', '20587654321', 59.90, 39.00, 48, 12, 'galón', 'Pintura látex lavable para interiores, rendimiento 30 m².', 'Color: blanco | Acabado: mate | Rendimiento: 30 m²'],
            ['PINT-002', 'Esmalte sintético negro 1 galón', 'Pinturas', 'Sika', '20587654321', 68.00, 45.00, 4, 10, 'galón', 'Esmalte de alta resistencia para metal y madera.', 'Color: negro | Acabado: brillante | Secado: 4 h'],
            ['PINT-003', 'Brocha de cerda 4"', 'Pinturas', 'Truper', '20512345678', 14.90, 8.00, 65, 15, 'unidad', 'Brocha de cerda natural con mango de madera.', 'Ancho: 4" | Cerda: natural | Mango: madera'],
            ['CONS-001', 'Cemento Portland tipo I 42.5 kg', 'Materiales de construcción', 'Sika', '20587654321', 34.90, 27.00, 85, 40, 'bolsa', 'Cemento de uso general para construcción.', 'Peso: 42.5 kg | Tipo: I | Resistencia: 42.5 MPa'],
            ['CONS-002', 'Yeso de construcción 25 kg', 'Materiales de construcción', 'Sika', '20587654321', 22.50, 15.00, 38, 15, 'bolsa', 'Yeso para tarrajeo y acabados interiores.', 'Peso: 25 kg | Fraguado: 20 min'],
            ['CONS-003', 'Plancha drywall 1.22 x 2.44 m', 'Materiales de construcción', 'Sika', '20587654321', 42.00, 30.00, 12, 20, 'unidad', 'Plancha de yeso laminado para tabiques.', 'Medidas: 1.22 x 2.44 m | Espesor: 1/2"'],
            ['TORN-001', 'Tornillo autorroscante 8 x 1" (100 u)', 'Tornillería', 'Truper', '20512345678', 9.90, 5.20, 150, 30, 'caja', 'Tornillos autorroscantes de acero galvanizado.', 'Medida: 8 x 1" | Cantidad: 100 | Acabado: galvanizado'],
            ['TORN-002', 'Perno hexagonal 1/2" x 3"', 'Tornillería', 'Truper', '20512345678', 3.50, 1.80, 200, 50, 'unidad', 'Perno hexagonal grado 5 con tuerca y arandela.', 'Medida: 1/2" x 3" | Grado: 5 | Incluye tuerca'],
            ['SEG-001', 'Casco de seguridad blanco', 'Seguridad industrial', '3M', '20512345678', 32.90, 20.00, 26, 10, 'unidad', 'Casco de seguridad con suspensión ajustable.', 'Norma: ANSI Z89.1 | Ajuste: ratchet | Color: blanco'],
            ['SEG-002', 'Guantes de nitrilo talla L (par)', 'Seguridad industrial', '3M', '20512345678', 12.90, 6.50, 5, 20, 'par', 'Guantes de nitrilo resistentes a químicos.', 'Talla: L | Material: nitrilo | Recubrimiento: palma'],
            ['SEG-003', 'Lentes de seguridad antiempañante', 'Seguridad industrial', '3M', '20512345678', 18.90, 10.00, 40, 12, 'unidad', 'Lentes de policarbonato con protección UV.', 'Norma: ANSI Z87.1 | Protección: UV | Antirayas'],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['sku' => $p[0]],
                [
                    'name' => $p[1],
                    'category_id' => $categories[$p[2]] ?? null,
                    'brand_id' => $brands[$p[3]] ?? null,
                    'supplier_id' => $suppliers[$p[4]] ?? null,
                    'price' => $p[5],
                    'cost' => $p[6],
                    'stock' => $p[7],
                    'min_stock' => $p[8],
                    'unit' => $p[9],
                    'description' => $p[10],
                    'specs' => $p[11],
                    'active' => true,
                ]
            );
        }
    }
}
