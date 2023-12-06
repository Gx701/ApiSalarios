<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalarioController extends Controller
{
    public function calcularIgss(Request $request)
    {
        $salario = $request->input('salario');
        $porcentajeIgss = 4.83; // Puedes ajustar este valor según las regulaciones locales

        $igss = $salario * ($porcentajeIgss / 100);

        return response()->json(['igss' => $igss]);
    }

    public function calcularTrabajo(Request $request)
    {
        // Validar los parámetros de entrada
        $request->validate([
            'salario_base' => 'required|numeric',
            'dias_trabajados' => 'required|integer|min:1',
            'valor_ventas' => 'required|integer|min:0',
        ]);

        // Obtener los parámetros
        $salarioBase = $request->input('salario_base');
        $diasTrabajados = $request->input('dias_trabajados');
        $valorVentas = $request->input('valor_ventas');

        // Calcular los valores
        $salarioCalculado = $this->calcularProrrateo($salarioBase, $diasTrabajados, $valorVentas);
        $comisionesGanadas = $this->calcularComisiones($valorVentas);
        $porcentajeProrrateo = $this->calcularPorcentajeProrrateo($salarioBase, $diasTrabajados);

        // Construir la respuesta JSON
        $response = [
            'salario_base' => $salarioBase,
            'dias_trabajados' => $diasTrabajados,
            'valor_ventas' => $valorVentas,
            'salario_calculado' => $salarioCalculado,
            'comisiones_ganadas' => $comisionesGanadas,
            'porcentaje_prorrateo' => $porcentajeProrrateo,
        ];

        return response()->json($response);
    }

    // Función para calcular el salario prorrateado
    private function calcularProrrateo($salarioBase, $diasTrabajados, $valorVentas)
    {
        // Lógica de cálculo del salario prorrateado
        //salario mas comisiones
        $salarioWComisiones = (($salarioBase / 30) * $diasTrabajados) + $this->calcularComisiones($valorVentas);
        //deduccion en base a los dias no trabajados
        $DeduccionParroteado = $salarioWComisiones*($this->calcularPorcentajeProrrateo($salarioBase, $diasTrabajados)/100);
        //resta del sueldo ganado - porcentaje dias no trabajados
        $prorrateo=$salarioWComisiones-$DeduccionParroteado;

        return $prorrateo;
    }

    // Función para calcular las comisiones
    private function calcularComisiones($valorVentas)
    {
        //topes de venta para calcular porcentajes
        $limiteVenta1 = 1000;
        $limiteVenta2 = 5000;

        // Calcular porcentaje de comisión según las ventas
        if ($valorVentas <= $limiteVenta1) {
            $porcentajeComision = 0.01;
        } elseif ($valorVentas > $limiteVenta1 && $valorVentas <= $limiteVenta2) {
            $porcentajeComision = 0.05;
        } else {
            $porcentajeComision = 0.1;
        }
        // Lógica de cálculo de comisiones
        $comisiones = $valorVentas * $porcentajeComision;

        return $comisiones;
    }

    // Función para calcular el porcentaje de prorrateo
    private function calcularPorcentajeProrrateo($salarioBase, $diasTrabajados)
    {
         // Verificar si se trabajaron al menos 30 días
        if(($diasTrabajados < 30))
        {
            $porcentajeProrrateo = ($diasTrabajados / 30) * 100;
        }
        else 
        {
            $porcentajeProrrateo =0;
        }
        // Lógica de cálculo del porcentaje de prorrateo
        // Puedes ajustar esta lógica según tus necesidades específicas
        

        return $porcentajeProrrateo;
    }
}
