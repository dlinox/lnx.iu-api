<style>
    * {
        font-family: 'Arial', sans-serif;
    }
</style>

<table style="width: 100%; border: none; border-collapse: collapse; margin: 10px 0;">
    <tbody>
        <tr>
            <td style="width: 100%; text-align: center;">
                <h3>
                    REPORTE DE NOTAS
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
                PERIODO
            </th>
            <th style="padding: 8px; width: 65px; border: 1px solid #000;">
                GRUPO
            </th>
            <th style="padding: 8px; border: 1px solid #000;">
                CURSO
            </th>

            <th style="padding: 8px; border: 1px solid #000;">
                DOCENTE
            </th>
            <th style="padding: 8px; width: 60px; border: 1px solid #000;">
                NOTA
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($module['grades'] as $i => $grade)
        <tr style="text-align: center;">
            <td style="padding: 5px; border: 1px solid #000;">{{ $i + 1 }}</td>
            <td style="padding: 5px; border: 1px solid #000;">{{ $grade['period'] }}</td>
            <td style="padding: 5px; border: 1px solid #000;">{{ $grade['group'] }}</td>
            <td style="padding: 5px; border: 1px solid #000;">{{ $grade['course'] }}</td>
            <td style="padding: 5px; border: 1px solid #000;">{{ $grade['teacher'] }}</td>
            <td style="padding: 5px; border: 1px solid #000;">{{ $grade['grade'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach
@endforeach
