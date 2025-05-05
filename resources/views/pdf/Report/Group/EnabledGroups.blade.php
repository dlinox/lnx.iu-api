<table style="width: 100%; border: none; border-collapse: collapse; margin: 10px 0;">
    <tbody>
        <tr>
            <td style="width: 100%; text-align: center;">
                <h3>
                    LISTA DE GRUPOS DEL MES DE: {{ $period->period }}
                </h3>
            </td>
        </tr>
    </tbody>
</table>





@foreach ($data as $item)
<table style="width: 100%; border: none; border-collapse: collapse; font-size: 12px; margin-top: 10px;">
    <thead>
        <tr>
            <th style="width:80px; background-color: #E2E2E2; padding: 8px;border: 1px solid #000; text-align: left;">
                {{ $item['name'] }}
            </th>
        </tr>
    </thead>
</table>
@foreach ($item['modules'] as $module)
<table style="width: 100%; border: none; border-collapse: collapse; font-size: 12px;">
    <thead>
        <tr>
            <th style="width:80px; background-color: #E2E2E2; padding: 8px;border: 1px solid #000; text-align: left;">
                MODULO
            </th>
            <th style="padding: 8px; background-color: #f2f2f2; border: 1px solid #000;text-align: left; text-align: left;">
                <strong> {{ $module['name'] }}</strong>
            </th>
            <th style="width:110px; background-color: #E2E2E2; padding: 8px;border: 1px solid #000; text-align: left;">
                EXTRACURRICULAR
            </th>
            <th style="width:60px; background-color: #f2f2f2;  padding: 8px;border: 1px solid #000; text-align: center;">
                {{ $module['isExtracurricular'] }}
            </th>
        </tr>
    </thead>
</table>
<table style="width: 100%; border: none; border-collapse: collapse; font-size: 11px;">
    <thead>
        <tr style="background-color: #f2f2f2; text-transform: uppercase;">
            <th style="padding: 8px; width: 10px; border: 1px solid #000;">
                N°
            </th>
            <th style="padding: 8px; width: 80px; border: 1px solid #000;">
                GRUPO
            </th>
            <th style="padding: 8px; width: 30%; border: 1px solid #000;">
                CURSO
            </th>
            <th style="padding: 8px; width: 30%; border: 1px solid #000;">
                DOCENTE
            </th>
            <th style="padding: 8px; width: 80px; border: 1px solid #000;">
                HORARIO
            </th>
            <th style="padding: 8px; width: 50px; border: 1px solid #000;">
                ALUMNOS
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($module['groups'] as $i => $group)
        <tr style="text-align: center;">
            <td style="padding: 5px; border: 1px solid #000;">{{ $i + 1 }}</td>
            <td style="padding: 5px; border: 1px solid #000;">{{ $group['group'] }}</td>
            <td style="padding: 5px; border: 1px solid #000;">{{ $group['course'] }}</td>
            <td style="padding: 5px; border: 1px solid #000;">
                @if ($group['teacherName'])
                {{ $group['teacherLastName'] }}, {{ $group['teacherName'] }}
                @else
                -
                @endif
            </td>
            <td style="padding: 5px; border: 1px solid #000;">
                @if ($group['schedule'] !== null)
                {{ $group['schedule']['days'] }}<br> {{ $group['schedule']['startHour'] }} - {{ $group['schedule']['endHour'] }}
                @else
                <span style="color: red;">No tiene horario asignado</span>
                @endif
            </td>
            <td style="padding: 5px; border: 1px solid #000;">
                {{ $group['students'] ?? '' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endforeach
@endforeach