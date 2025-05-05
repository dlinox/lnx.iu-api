<table style="width: 100%; border: none; border-collapse: collapse; margin: 10px 0;">
    <tbody>
        <tr>
            <td style="width: 100%; text-align: center;">
                <h3>
                    REPORTE DE MATRICULAS
                </h3>
            </td>
        </tr>
    </tbody>
</table>


<table style="width: 100%; border: none; border-collapse: collapse; font-size: 14px;">
    <tbody>
        <tr>
            <td style="width: 50%;">
                <b>Codigo:</b> {{ $student->code }}
            </td>
            <td style="width: 50%;">
                <b>Tipo de Estudiante:</b> {{ $student->type }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 100%;">
                <b>Estudiante:</b> {{ $student->lastName }}, {{ $student->name }}
            </td>

        </tr>

    </tbody>
</table>


<table style="width: 100%; border: none; border-collapse: collapse; font-size: 12px; margin-top: 10px;">
    <thead>
        <tr style="background-color: #f2f2f2; text-transform: uppercase;">
            <th style="padding: 8px; width: 10px; border: 1px solid #000;">
                N°
            </th>

            <th style="padding: 8px; width: 70px; border: 1px solid #000;">
                PERÍODO
            </th>
            <th style="padding: 8px; width: 70px; border: 1px solid #000;">
                GRUPO
            </th>
            <th style="padding: 8px;  border: 1px solid #000;">
                CURSO
            </th>
            <th style="padding: 8px; width: 70px; border: 1px solid #000;">
                MODALIDAD MATRÍCULA
            </th>
            <th style="padding: 8px; width: 80px; border: 1px solid #000;">
                ESTADO MATRÍCULA
            </th>
            <th style="padding: 8px; width: 80px; border: 1px solid #000;">
                ESTADO DEL GRUPO
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($enrollments as $index => $item)
        <tr style="text-align: center;">
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000;">{{ $index + 1 }}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000;">{{$item['period']}}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000;">{{$item['group']}}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000;">{{$item['course']}}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000;">{{$item['enrollmentModality']}}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000;">{{$item['enrollmentStatus']}}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000;">{{$item['groupStatus']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>