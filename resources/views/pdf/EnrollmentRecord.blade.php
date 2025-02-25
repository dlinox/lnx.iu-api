<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de matricula</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;

        }

        .record {
            height: 524px;
            width: 100vw;
            padding: 1rem 2rem;
        }

        .header-record {
            width: 100%;
            text-align: center;
            border-bottom: 2px solid #000;
        }

        .header-record span {
            font-size: 12px;
        }

        .footer-record {
            width: 100%;
            text-align: center;
            border-top: 2px solid #000;
        }

        .title {
            background-color: #0000AA44;
            margin: 1rem 0;
            width: 100vw;
            padding: .5rem;
            text-align: center;
            font-weight: 800;
        }

        .record-body {
            border: 1px solid #000;
            display: flex;


        }

        .record-body .left {
            width: 49%;
            background-color: #0000AA44;
        }

        .record-body .right {
            width: 49%;
            background-color: #AA00AA44;
        }

        p {
            font-size: 16px;
            line-height: 24px;
        }

        .signal {
            margin: 3rem;
            border-top: 1px solid #000;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="record" style="border-bottom: 1px dashed #999;">
        <div class="header-record">
            <span style="text-align: center; width: 100%;">
                <strong>
                    UNIVERSIDAD NACIONAL DEL ALTIPLANO
                </strong>
                <br>
                INSTITUTO DE INFORMÁTICA
            </span>
        </div>
        <div class="title">
            FICHA DE MATRÍCULA
        </div>
        <table width="100%" style="border-collapse: collapse;">
            <tr>
                <td width="50%" style="vertical-align: top; padding: 2px; ">

                    <p>
                        <strong>Estudiante:</strong> YHON KENYO WILLIAM PARI JARECCA
                    </p>
                    <p>
                        <strong>Modulo:</strong> AUTOCAD
                    </p>
                    <p>
                        <strong>Grupo:</strong> PD3
                    </p>
                    <p>
                        <strong>Tipo de Comprobante:</strong> BANCO.NACION
                    </p>
                    <p>
                        <strong>Costo Matrícula:</strong>
                    </p>
                    <p>
                        <strong>Mes:</strong>
                    </p>
                </td>
                <td width="50%" style="vertical-align: top; padding: 2px; ">

                    <p>
                        <strong>Código:</strong> YHON KENYO WILLIAM PARI JARECCA
                    </p>
                    <p>
                        <strong>Curso:</strong> AUTOCAD
                    </p>
                    <p>
                        <strong>Horario:</strong> PD3
                    </p>
                    <p>
                        <strong>Nro Comprobante:</strong> BANCO.NACION
                    </p>
                    <p>
                        <strong>Costo Mensualidad::</strong>
                    </p>
                    <p>
                        <strong>Tipo Estu.:</strong>
                    </p>
                </td>
            </tr>
        </table>
        <table width="100%" style="border-collapse: collapse;">
            <tr>
                <td width="50%" style="vertical-align: top; padding: 20px; ">

                    <div class="signal">
                        Firma del estudiante
                        <br>
                        DNI: 75865034
                    </div>

                </td>
                <td width="50%" style="vertical-align: top; padding: 20px; ">

                    <div class="signal">
                        Coordinación Académica
                        <br>
                        INFOUNA
                    </div>

                </td>
            </tr>
        </table>
        <div class="footer-record">
            <table width="100%" style="border-collapse: collapse;">
                <tr>
                    <td width="50%" style="vertical-align: top; padding: 10px; ">
                    </td>
                    <td width="50%" style="vertical-align: top; padding: 10px; text-align: right;">
                        <strong>Fecha:</strong> {{now()}}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="record">
        <div class="header-record">
            <span style="text-align: center; width: 100%;">
                <strong>
                    UNIVERSIDAD NACIONAL DEL ALTIPLANO
                </strong>
                <br>
                INSTITUTO DE INFORMÁTICA
            </span>
        </div>
        <div class="title">
            FICHA DE MATRÍCULA
        </div>
        <table width="100%" style="border-collapse: collapse;">
            <tr>
                <td width="50%" style="vertical-align: top; padding: 2px; ">

                    <p>
                        <strong>Estudiante:</strong> YHON KENYO WILLIAM PARI JARECCA
                    </p>
                    <p>
                        <strong>Modulo:</strong> AUTOCAD
                    </p>
                    <p>
                        <strong>Grupo:</strong> PD3
                    </p>
                    <p>
                        <strong>Tipo de Comprobante:</strong> BANCO.NACION
                    </p>
                    <p>
                        <strong>Costo Matrícula:</strong>
                    </p>
                    <p>
                        <strong>Mes:</strong>
                    </p>
                </td>
                <td width="50%" style="vertical-align: top; padding: 2px; ">

                    <p>
                        <strong>Código:</strong> YHON KENYO WILLIAM PARI JARECCA
                    </p>
                    <p>
                        <strong>Curso:</strong> AUTOCAD
                    </p>
                    <p>
                        <strong>Horario:</strong> PD3
                    </p>
                    <p>
                        <strong>Nro Comprobante:</strong> BANCO.NACION
                    </p>
                    <p>
                        <strong>Costo Mensualidad::</strong>
                    </p>
                    <p>
                        <strong>Tipo Estu.:</strong>
                    </p>
                </td>
            </tr>
        </table>
        <table width="100%" style="border-collapse: collapse;">
            <tr>
                <td width="50%" style="vertical-align: top; padding: 20px; ">

                    <div class="signal">
                        Firma del estudiante
                        <br>
                        DNI: 75865034
                    </div>

                </td>
                <td width="50%" style="vertical-align: top; padding: 20px; ">

                    <div class="signal">
                        Coordinación Académica
                        <br>
                        INFOUNA
                    </div>

                </td>
            </tr>
        </table>
        <div class="footer-record">
            <table width="100%" style="border-collapse: collapse;">
                <tr>
                    <td width="50%" style="vertical-align: top; padding: 10px; ">
                    </td>
                    <td width="50%" style="vertical-align: top; padding: 10px; text-align: right;">
                        <strong>Fecha:</strong> {{now()}}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>