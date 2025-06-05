<!-- SendCredentials -->

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>
        Matrícula realizada con éxito
    </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }

        .container {
            max-width: 500px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        .header {
            font-size: 22px;
            font-weight: bold;
            color: #040466;
            margin-bottom: 20px;
        }

        .code {
            font-size: 28px;
            font-weight: bold;
            color: #040466;
            padding: 10px;
            border: 2px dashed #040466;
            display: inline-block;
            margin: 15px 0;
        }

        .footer {
            font-size: 12px;
            color: #666;
            margin-top: 20px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .logo {
            width: 100px;
        }
    </style>
</head>

<body>
    <div class="container">
        <table style="width: 100%; border-bottom: 1px solid #eee; margin-bottom: 20px; margin-top: 20px;">
            <tr>
                <td>
                    <img src="https://main.infouna.unap.edu.pe/assets/img/logo-unap.png" alt="Logo UNAP" style="width: 50px; height: auto; margin: 0; padding: 0;">
                </td>
                <td>
                    <h4 style="margin: 0; padding: 0; text-align: center;">
                        UNIVERSIDAD NACIONAL DEL ALTIPLANO
                    </h4>
                    <h5 style="margin: 10px 0 0 0; padding: 0; text-align: center;">
                        INSTITUTO DE INFORMÁTICA
                    </h5>
                </td>
                <td style="text-align: right;">
                    <img src="https://main.infouna.unap.edu.pe/assets/img/logo-info.jpg" alt="Logo UNAP" style="width: 50px; height: auto; margin: 0; padding: 0;">
                </td>
            </tr>
        </table>

        <div class="header">
            @if ($updated)
            Su matrícula ha sido actualizada con éxito
            @else
            Matrícula realizada con éxito
            @endif
        </div>
        <p>
            Estimado(a) {{ $student->name }},
        </p>
        <div style="background-color: #f9f9f9; padding: 5px 15px; border-radius: 5px; margin-top: 10px;">
            <p>
                <b>
                    Detalles de su matrícula:
                </b>
            </p>
            <p>
                <b>
                    Curso:
                </b>
                {{ $details->courseName }}
            </p>
            <p>
                <b>
                    Grupo:
                </b>
                {{ $details->groupName }} - <b> {{ $details->modality }}</b>
            </p>
            <p>
                <b>
                    Horario:
                </b>
                {{ $details->schedule['days'] }} - {{ $details->schedule['startHour'] }} a {{ $details->schedule['endHour'] }}
            </p>
        </div>


        <a href="https://matriculas.infouna.unap.edu.pe/" target="_blank" style="text-decoration: none; color: #fff; background-color: #040466; padding: 10px 20px; border-radius: 5px; display: inline-block; margin-top: 20px;">
            Sistema de matrículas
        </a>

        <p>
            <i>
                <small>

                    En caso de requerir rectificaciones o modificaciones en su matrícula, le solicitamos ingresar al sistema de matrículas. Le recordamos que el plazo para realizar dichos ajustes estará habilitado hasta la finalización del periodo de matrículas. Si necesita asistencia adicional, puede comunicarse con el coordinador académico correspondiente o acudir personalmente a la oficina administrativa.
                </small>
            </i>
        </p>
        <div class="footer">
            Instituto de Informática - UNA Puno <br>
            © {{ date('Y') }} Universidad Nacional del Altiplano Puno

        </div>
</body>

</html>