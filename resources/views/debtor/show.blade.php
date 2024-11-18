@extends('layouts.app')

@section('content')
    <h2>Lista de Deudores</h2>
    <table id="debtorTable1" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Cuit</th>
                <th>Situación Más Desfavorable</th>
                <th>Suma de Préstamos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($debtors as $debtor)
                <tr>
                    <td>{{ $debtor['cuit']['N'] ?? 'No disponible' }}</td>
                    <td>{{ $debtor['worst_situation']['N'] ?? 'No disponible' }}</td>
                    <td>{{ $debtor['loan_sum']['N'] ?? 'No disponible' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-2">
    <h2>Instituciones</h2>
    <table id="institutionTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>cod</th>
                <th>monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($institutions as $institution)
                <tr>
                    <td>{{ $institution['institution_code']['N'] ?? 'No disponible'}}</td>
                    <td>{{ $institution['loan_amounts']['N'] ?? 'No disponible' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            $('#debtorTable1').DataTable({
                "paging": true,        // Habilita la paginación
                "ordering": true,      // Habilita la ordenación de las columnas
                "order": [[2, 'desc']], // Ordena por la columna 2 (Suma de préstamos) de forma descendente por defecto
                "pageLength": 10,      // Número de registros por página
                "columnDefs": [
                    { "type": "num", "targets": [2, 1] }, // Define que las columnas 1 y 2 son numéricas para ordenar correctamente
                ]
            });

            $('#institutionTable').DataTable({
                "paging": true,
                "ordering": true,
                "order": [[0, 'asc']],
                "pageLength": 10,
            });
        });
    </script>
@endsection
