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
                    ESTUDIANTES MATRICULADOS: Mayo del 2025
                </h3>
            </td>
        </tr>
    </tbody>
</table>


<table style="width: 100%; border: none; border-collapse: collapse; font-size: 13px;">
    <tbody>
        <tr>
            <td style="width: 70%;">
                <b>Grupo:</b> M1012
            </td>
            <td style="width: 30%;">
                <b>Modalidad:</b> PRESENCIAL
            </td>
        </tr>
        <tr>
            <td style="width: 70%;">
                <b>Curso:</b> Programación I de Computadoras Programación I de
            </td>
            <td style="width: 30%;">
                <b>Días:</b> LUN, MIE, VIE
            </td>
        </tr>
        <tr>
            <td style="width: 70%;">
                <b>Docente:</b> Ing. Juan Pérez Gonzales
            </td>
            <td style="width: 30%;">
                <b>Horas:</b> 10:00 a.m. - 12:00 p.m.
            </td>
        </tr>
    </tbody>
</table>

<table style="width: 100%; border: none; border-collapse: collapse; font-size: 13px; margin-top: 10px;">
    <thead>
        <tr style="background-color: #f2f2f2; text-transform: uppercase;">
            <th style="padding: 5px; width: 10px; border: 1px solid #000;">
                N°
            </th>
            <th style="padding: 5px; width: 80px; border: 1px solid #000;">
                CÓDIGO
            </th>
            <th style="padding: 5px; border: 1px solid #000;">
                APELLIDOS Y NOMBRES
            </th>
            <th style="padding: 5px; width: 85px; border: 1px solid #000;">
                CELULAR
            </th>
        </tr>
    </thead>
    <tbody>
        @for($index = 1; $index <= 50; $index++)
            <tr style="text-align: center;">
            <td style="padding: 2px; border: 1px solid #000;">{{ $index }}</td>
            <td style="padding: 2px; border: 1px solid #000;">2025000{{ $index }}</td>
            <td style="padding: 2px; border: 1px solid #000;">Apellidos y Nombres {{ $index }}</td>
            <td style="padding: 2px; border: 1px solid #000;">987 654 321</td>
            </tr>
            @endfor
    </tbody>
</table>