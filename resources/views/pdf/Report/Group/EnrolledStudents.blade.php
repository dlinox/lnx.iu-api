<table style="width: 100%; border: none; border-collapse: collapse; margin: 10px 0;">
    <tbody>
        <tr>
            <td style="width: 100%; text-align: center;">
                <h3>
                    ESTUDIANTES MATRICULADOS: {{ $group['period'] }}
                </h3>
            </td>
        </tr>
    </tbody>
</table>


<table style="width: 100%; border: none; border-collapse: collapse; font-size: 14px;">
    <tbody>
        <tr>
            <td style="width:70%;">
                <b>Grupo:</b> {{ $group['group'] }} <b>Curso:</b> {{ $group['course'] }}
            </td>
            <td style="width: 30%;">
                <b>Dias:</b> {{ $group['schedule']['days'] }}
            </td>
        </tr>
        <tr>
            <td style="width: 70%;">
                <b>Docente:</b> {{ $group['teacherLastName'] }}, {{ $group['teacherName'] }}
            </td>
            <td style="width: 30%;">
                <b>Horas:</b> {{ $group['schedule']['startHour'] }} - {{ $group['schedule']['endHour'] }}
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
                CÓDIGO
            </th>
            <th style="padding: 8px; width: 90px; border: 1px solid #000;">
                TIPO DE ESTUDIANTE
            </th>
            <th style="padding: 8px; border: 1px solid #000;">
                ESTUDIANTE
            </th>
            <th style="padding: 8px; width: 70px; border: 1px solid #000;">
                TELEFONO
            </th>
            <th style="padding: 8px; width: 80px; border: 1px solid #000;">
                MODALIDAD MATRÍCULA
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($students as $index => $item)
        <tr style="text-align: center;">
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000;">{{ $index + 1 }}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000;">{{$item['code']}}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000;">{{$item['type']}}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000;">{{$item['lastName']}}, {{$item['name']}}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000;">{{$item['phone']}}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000;">{{$item['enrollmentModality']}}</td>
        </tr>
        @endforeach

    </tbody>
</table>