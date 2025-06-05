<table style="width: 100%; border: none; border-collapse: collapse; margin: 10px 0;">
    <tbody>
        <tr>
            <td style="width: 100%; text-align: center;">
                <h3>
                    MONTO DE RECAUDACIÓN DEL MES: {{ $period->period }}
                </h3>
            </td>
        </tr>
    </tbody>
</table>


<table style="width: 100%; border: none; border-collapse: collapse; font-size: 14px;">
    <tbody>
        <tr>
            <td style="width:70%;">
                Reporte Por: GRUPOS

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
                GRUPO
            </th>
            <th style="padding: 8px;  border: 1px solid #000;">
                CURSO
            </th>
            <th style="padding: 8px; width: 120px; border: 1px solid #000;">
                MATRICULAS
            </th>
            <th style="padding: 8px; width: 120px; border: 1px solid #000;">
                MENSUALIDADES
            </th>
            <th style="padding: 8px; width: 120px; border: 1px solid #000;">
                TOTAL
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($groups as $index => $item)
        <tr style="text-align: center;">
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000; text-align: left;">{{ $index + 1 }}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000; text-align: left;">{{$item->groupName}}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000; text-align: left;">{{$item->courseName}}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000; text-align: right;">{{$item->enrollment}}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000; text-align: right;">{{$item->enrollmentGroup}}</td>
            <td style="font-size: 12px; padding: 5px; border: 1px solid #000; text-align: right;">{{$item->total}}</td>
        </tr>
        @endforeach
        <tr style="text-align: center; background-color: #f2f2f2;">
            <td colspan="5" style="font-size: 14px; padding: 10px; border: 1px solid #000; border-right: none; text-align: right;">
                <strong>
                    TOTAL:
                </strong>
            </td>
            <td style="font-size: 14px; padding: 10px; border: 1px solid #000; text-align: right; border-left: none;">
                <strong>
                  S/.  {{ $total }}
                </strong>
            </td>
        </tr>

    </tbody>
</table>