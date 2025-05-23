<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExtractDataCommand extends Command
{

    protected $signature = 'extract-data';
    protected $description = 'Command description';

    public function handle()
    {
        $this->info('Starting data extraction...');

        $this->insertConstants();
        $this->insertMasters();
        $this->insertStudents();
        $this->insertTeachers();
        $this->insertCurriculum();
        $this->insertAreas();
        $this->insertModules();
        $this->insertModulePrices();
        $this->insertCourses();

        $this->insertCousePrices();
        $this->insertPeriods();

        $this->insertGroups();
        $this->insertSchedules();
        $this->insertEnrollmentGroups();
        $this->insertEnrollments();
        $this->insertEnrollmentGrades();
        $this->insertEnrollmentUnitGrades();

        $this->insertPayment();

        $this->insertCurriculum();

        $this->info('Data extraction completed.');
    }

    private function insertConstants()
    {
        try {
            $this->info('Constants inserted in database...');
            //verificar si la tabla ya tiene datos
            if (DB::table('days')->count() == 0) {
                DB::table('days')->insert([
                    ['id' => 1, 'name' => 'Lunes', 'short_name' => 'LUN'],
                    ['id' => 2, 'name' => 'Martes', 'short_name' => 'MAR'],
                    ['id' => 3, 'name' => 'Miércoles', 'short_name' => 'MIE'],
                    ['id' => 4, 'name' => 'Jueves', 'short_name' => 'JUE'],
                    ['id' => 5, 'name' => 'Viernes', 'short_name' => 'VIE'],
                    ['id' => 6, 'name' => 'Sábado', 'short_name' => 'SAB'],
                    ['id' => 7, 'name' => 'Domingo', 'short_name' => 'DOM'],
                ]);
            }

            if (DB::table('months')->count() == 0) {

                DB::table('months')->insert([
                    ['id' => 1, 'name' => 'Enero', 'short_name' => 'ENE'],
                    ['id' => 2, 'name' => 'Febrero', 'short_name' => 'FEB'],
                    ['id' => 3, 'name' => 'Marzo', 'short_name' => 'MAR'],
                    ['id' => 4, 'name' => 'Abril', 'short_name' => 'ABR'],
                    ['id' => 5, 'name' => 'Mayo', 'short_name' => 'MAY'],
                    ['id' => 6, 'name' => 'Junio', 'short_name' => 'JUN'],
                    ['id' => 7, 'name' => 'Julio', 'short_name' => 'JUL'],
                    ['id' => 8, 'name' => 'Agosto', 'short_name' => 'AGO'],
                    ['id' => 9, 'name' => 'Septiembre', 'short_name' => 'SEP'],
                    ['id' => 10, 'name' => 'Octubre', 'short_name' => 'OCT'],
                    ['id' => 11, 'name' => 'Noviembre', 'short_name' => 'NOV'],
                    ['id' => 12, 'name' => 'Diciembre', 'short_name' => 'DIC'],
                ]);
            }

            if (DB::table('genders')->count() == 0) {
                DB::table('genders')->insert([
                    ['id' => 1, 'name' => 'Masculino', 'short_name' => 'M', 'is_enabled' => 1],
                    ['id' => 2, 'name' => 'Femenino', 'short_name' => 'F', 'is_enabled' => 1],
                    ['id' => 3, 'name' => 'No Binario', 'short_name' => 'NB', 'is_enabled' => 0],
                    ['id' => 4, 'name' => 'Otro', 'short_name' => 'OTRO', 'is_enabled' => 0],
                ]);
            }
            $this->info('Constants inserted successfully.');
        } catch (\Exception $e) {
            $this->error('Error inserting constants: ' . $e->getMessage());
            return;
        }
    }

    private function insertMasters()
    {
        try {

            $this->info('Inserting masters...');

            if (DB::table('student_types')->count() == 0) {
                $studentTypes = DB::table('siga200.tatipoestudiante')->get();
                DB::table('student_types')->insert($studentTypes->map(function ($item) {
                    return [
                        'id' => $item->id_TipoEstudiante,
                        'name' => $item->NombreTipo,
                        'is_enabled' => 1,
                    ];
                })->toArray());
            }

            if (DB::table('payment_types')->count() == 0) {
                $paymentTypes = DB::table('siga200.tatipopago')->get();
                DB::table('payment_types')->insert($paymentTypes->map(function ($item) {
                    return [
                        'id' => $item->id_TipoPago,
                        'name' => $item->NombreTipo,
                        'is_enabled' => 1,
                    ];
                })->toArray());

                DB::table('payment_types')->insert([
                    'id' => 9,
                    'name' => 'PAGALO.PE',
                    'commission' => 1.00,
                    'is_enabled' => true,
                ]);
            }

            if (DB::table('laboratories')->count() == 0) {
                $laboratories = DB::table('siga200.talaboratorio')->get();
                DB::table('laboratories')->insert($laboratories->map(function ($item) {
                    return [
                        'id' => $item->id_Laboratorio,
                        'name' => $item->NombreLaboratorio,
                        'type' => 'LABORATORIO',
                        'virtual_link' => null,
                        'is_enabled' => 1,
                    ];
                })->toArray());
            }
            if (DB::table('document_types')->count() == 0) {
                $documentTypes = DB::table('siga200.tatipodocumento')->get();
                DB::table('document_types')->insert($documentTypes->map(function ($item) {
                    return [
                        'id' => $item->id_TipoDocumento,
                        'name' => $item->NombreDocumento,
                        'is_enabled' => 1,
                    ];
                })->toArray());
            }
            $this->info('Masters inserted successfully.');
        } catch (\Exception $e) {
            $this->error('Error inserting masters: ' . $e->getMessage());
            return;
        }
    }

    private function insertStudents()
    {
        try {
            $this->info('Inserting students...');

            if (DB::table('students')->count() > 0) {
                $this->info('Students already exist in the database. Skipping insertion.');
                return;
            }

            DB::statement("
                INSERT INTO students (
                    id, ref, student_type_id, code, document_type_id, document_number,
                    name, last_name_father, last_name_mother, gender_id, phone,
                    date_of_birth, address, email, location_id, country_id
                )
                SELECT
                    MIN(es.id_Estudiante) AS id,
                    GROUP_CONCAT(DISTINCT es.id_Estudiante) AS ref,  -- ref después de id
                    MAX(CASE
                        WHEN es.id_TipoEstudiante = 0 THEN NULL
                        ELSE TRIM(es.id_TipoEstudiante)
                    END) AS student_type_id,
                    pe.id_CodigoPersona AS code,
                    CASE
                        WHEN NULLIF(TRIM(pe.NumeroDocumento), '') IS NULL THEN NULL
                        ELSE CASE
                            WHEN pe.id_TipoDocumento = 0 THEN NULL
                            ELSE TRIM(pe.id_TipoDocumento)
                        END
                    END AS document_type_id,
                    NULLIF(TRIM(pe.NumeroDocumento), '') AS document_number,
                    TRIM(pe.Nombres) AS name,
                    TRIM(pe.ApellidoPa) AS last_name_father,
                    TRIM(pe.ApellidoMa) AS last_name_mother,
                    CASE
                        WHEN pe.id_Genero = 0 THEN NULL
                        ELSE TRIM(pe.id_Genero)
                    END AS gender_id,
                    NULLIF(TRIM(pe.Telefono), '') AS phone,
                    NULL AS date_of_birth,
                    NULLIF(TRIM(pe.Direccion), '') AS address,
                    NULLIF(TRIM(pe.Email), '') AS email,
                    NULL AS location_id,
                    NULL AS country_id
                FROM siga200.taestudiante es
                JOIN siga200.tapersona pe ON es.id_CodigoPersona = pe.id_CodigoPersona
                GROUP BY pe.id_CodigoPersona, pe.id_TipoDocumento, pe.NumeroDocumento,
                        pe.Nombres, pe.ApellidoPa, pe.ApellidoMa, pe.id_Genero,
                        pe.Telefono, pe.Direccion, pe.Email
                ORDER BY id     
                ;
            ");
            $this->info('Students inserted successfully.');
        } catch (\Exception $e) {
            $this->error('Error inserting students: ' . $e->getMessage());
            return;
        }
    }

    private function insertTeachers()
    {
        try {
            $this->info('Inserting teachers...');

            if (DB::table('teachers')->count() > 0) {
                $this->info('Teachers already exist in the database. Skipping insertion.');
                return;
            }

            DB::statement("
                INSERT INTO teachers (
                    id, code, document_type_id, document_number,
                    name, last_name_father, last_name_mother, gender_id,
                    phone, date_of_birth, address, email, location_id, country_id
                )
                SELECT
                    MIN(docente.id_Docentes) AS id,
                    pe.id_CodigoPersona AS code,
                    CASE 
                        WHEN NULLIF(TRIM(pe.NumeroDocumento), '') IS NULL THEN NULL 
                        ELSE CASE 
                            WHEN pe.id_TipoDocumento = 0 THEN NULL 
                            ELSE TRIM(pe.id_TipoDocumento) 
                        END 
                    END AS document_type_id,
                    NULLIF(TRIM(pe.NumeroDocumento), '') AS document_number,
                    TRIM(pe.Nombres) AS name,
                    TRIM(pe.ApellidoPa) AS last_name_father,
                    TRIM(pe.ApellidoMa) AS last_name_mother,
                    CASE 
                        WHEN pe.id_Genero = 0 THEN NULL 
                        ELSE TRIM(pe.id_Genero) 
                    END AS gender_id,
                    NULLIF(TRIM(pe.Telefono), '') AS phone,
                    NULL AS date_of_birth,
                    NULLIF(TRIM(pe.Direccion), '') AS address,
                    NULLIF(TRIM(pe.Email), '') AS email,
                    NULL AS location_id,
                    NULL AS country_id
                FROM siga200.tadocentes docente
                JOIN siga200.tapersona pe ON docente.id_CodigoPersona = pe.id_CodigoPersona
                GROUP BY 
                    pe.id_CodigoPersona, pe.id_TipoDocumento, pe.NumeroDocumento,
                    pe.Nombres, pe.ApellidoPa, pe.ApellidoMa, pe.id_Genero,
                    pe.Telefono, pe.Direccion, pe.Email
                ORDER BY id;
            ");
            $this->info('Teachers inserted successfully.');
        } catch (\Exception $e) {
            $this->error('Error inserting teachers: ' . $e->getMessage());
            return;
        }
    }

    private function insertCurriculum()
    {
        try {
            $this->info('Inserting curriculum...');

            if (DB::table('curriculums')->count() > 0) {
                $this->info('Curriculum already exists in the database. Skipping insertion.');
                return;
            }
            DB::statement("
                INSERT INTO curriculums ( id, name, is_enabled)
                VALUES
                (1, 'CURRICULO 2010-2025', true);
            ");
            $this->info('Curriculum inserted successfully.');
        } catch (\Exception $e) {
            $this->error('Error inserting curriculum: ' . $e->getMessage());
            return;
        }
    }

    private function insertAreas()
    {

        try {
            $this->info('Inserting areas...');

            if (DB::table('areas')->count() > 0) {
                $this->info('Areas already exist in the database. Skipping insertion.');
                return;
            }

            DB::statement("
                INSERT INTO areas (id, name, description, curriculum_id, is_enabled, created_at, updated_at)
                SELECT
                    taareacurso.id_AreaCurso AS id,
                    TRIM(taareacurso.NombreCurso) AS name,
                    NULL AS description,
                    1 AS curriculum_id,
                    1 AS is_enabled,
                    NOW() AS created_at,
                    NOW() AS updated_at
                FROM siga200.taareacurso;
            ");
            $this->info('Areas inserted successfully.');
        } catch (\Exception $e) {
            $this->error('Error inserting areas: ' . $e->getMessage());
            return;
        }
    }
    private function insertModules()
    {

        $this->info('Inserting modules...');
        if (DB::table('modules')->count() > 0) {
            $this->info('Modules already exist in the database. Skipping insertion.');
            return;
        }

        DB::statement("
            INSERT INTO modules (id, name, description, code, curriculum_id, is_extracurricular, is_enabled, level)
            SELECT
                modulo.id_Modulos AS id,
                TRIM(modulo.NombreModulo) AS name,
                NULL AS description,
                '' AS code,
                1 AS curriculum_id,
                CASE
                    WHEN modulo.id_Modulos = 8 THEN 1
                    ELSE 0
                END AS is_extracurricular,
                1 AS is_enabled,
                modulo.id_Modulos AS level
            FROM siga200.tamodulos modulo;
        ");
        $this->info('Modules inserted successfully.');
    }

    private function insertModulePrices()
    {
        $this->info('Inserting module prices...');
        if (DB::table('module_prices')->count() > 0) {
            $this->info('Module prices already exist in the database. Skipping insertion.');
            return;
        }

        DB::statement("
            INSERT INTO module_prices (id, module_id,student_type_id, price)
            SELECT 
            co.id_Costos AS 'id',
            mo.id AS 'module_id',
            st.id AS 'student_type_id',
            co.CostoMatricula AS 'price'
            FROM siga200.tacostos co 
            JOIN modules mo ON co.id_Modulos = mo.id
            JOIN student_types st ON st.id =  co.id_TipoEstudiante
            ORDER BY co.id_Costos;
        ");
        $this->info('Module prices inserted successfully.');
    }

    private function insertCousePrices()
    {
        $this->info('Inserting course prices...');
        if (DB::table('course_prices')->count() > 0) {
            $this->info('Course prices already exist in the database. Skipping insertion.');
            return;
        }

        DB::statement("
            INSERT INTO course_prices (course_id, presential_price,virtual_price, student_type_id)
            SELECT 
            DISTINCT 
            cur.id AS course_id,
            co.CostoMensualidad AS presential_price,
            co.CostoMensualidad AS virtual_price,
            co.id_TipoEstudiante AS student_type_id
            FROM
                siga200.tacostos co
            JOIN modules mo ON mo.id =  co.id_Modulos
            JOIN courses cur ON cur.module_id = mo.id
            JOIN student_types st ON st.id = co.id_TipoEstudiante;
        ");
        $this->info('Course prices inserted successfully.');
    }

    //periods
    private function insertPeriods()
    {
        $this->info('Inserting periods...');
        if (DB::table('periods')->count() > 0) {
            $this->info('Periods already exist in the database. Skipping insertion.');
            return;
        }

        DB::statement("
            INSERT INTO periods (year, month)
            SELECT DISTINCT a.NombreAnioMatricula, gru.id_MesMatricula
            FROM siga200.tagrupo gru
            JOIN siga200.taaniomatricula a ON a.id_AnioMatricula = gru.id_AnioMatricula
            ORDER BY a.NombreAnioMatricula, gru.id_MesMatricula;
        ");
        $this->info('Periods inserted successfully.');
    }

    private function insertCourses()
    {

        $this->info('Inserting courses...');
        if (DB::table('courses')->count() > 0) {
            $this->info('Courses already exist in the database. Skipping insertion.');
            return;
        }

        DB::statement("
            INSERT INTO courses (id, name, description, code, hours_practice, hours_theory, credits, `order`, units, area_id, module_id, curriculum_id, pre_requisite_id, is_enabled, created_at, updated_at)
            SELECT
                curso.id_Cursos AS id,
                TRIM(curso.NombreCurso) AS name,
                NULL AS description,
                TRIM(ifnull(curso.TagGrupo, '')) AS code,
                0 AS hours_practice,
                0 AS hours_theory,
                0 AS credits,
                curso.PrioridadCurso AS `order`,
                2 AS units,
                curso.id_AreaCurso AS area_id,
                curso.id_Modulos AS module_id,
                1 AS curriculum_id,
                NULL AS pre_requisite_id,
                curso.EstadoCurso AS is_enabled,
                NOW() AS created_at,
                NOW() AS updated_at
            FROM siga200.tacursos curso;
        ");
        $this->info('Courses inserted successfully.');
    }

    private function insertGroups()
    {

        $this->info('Inserting groups...');
        if (DB::table('groups')->count() > 0) {
            $this->info('Groups already exist in the database. Skipping insertion.');
            return;
        }

        DB::statement("
            INSERT INTO `groups` (id, name, period_id, teacher_id, laboratory_id, course_id, created_at, updated_at)
            SELECT 
                gru.id_Grupo AS id,
                TRIM(gru.NombreGrupo) AS name,
                pe.id AS period_id,
                NULLIF(d.id_Docentes, 0) AS teacher_id,
                gru.id_Laboratorio AS laboratory_id,
                cu.id AS course_id,
                NOW() AS created_at,
                NOW() AS updated_at
            FROM 
                siga200.tagrupo gru
            LEFT JOIN siga200.tadocentes d ON d.id_Docentes = gru.id_Docentes
            JOIN courses cu ON cu.id =  gru.id_Cursos
            JOIN siga200.taaniomatricula a ON a.id_AnioMatricula = gru.id_AnioMatricula
            JOIN periods pe ON pe.year = a.NombreAnioMatricula AND pe.`month` = gru.id_MesMatricula
            ORDER BY gru.id_Grupo;
        ");
        $this->info('Groups inserted successfully.');
    }

    private function insertSchedules()
    {
        $this->info('Inserting schedules...');
        if (DB::table('schedules')->count() > 0) {
            $this->info('Schedules already exist in the database. Skipping insertion.');
            return;
        }
        DB::statement("
            CREATE OR REPLACE VIEW vista_horas AS 
            SELECT 
                ho.id_Hora AS id,
                TIME(STR_TO_DATE(TRIM(REPLACE(ho.HoraInicio, '.', '')), '%h:%i %p')) AS start_hour,
                TIME(STR_TO_DATE(TRIM(REPLACE(ho.HoraFinal, '.', '')), '%h:%i %p')) AS end_hour
            FROM siga200.tahora ho;

            ");

        DB::statement("
            CREATE OR REPLACE VIEW vista_horarios AS
            WITH RECURSIVE CTE AS (
                SELECT 
                    id_Horarios,
                    SUBSTRING_INDEX(NombreHorario, '-', 1) AS Dia,
                    SUBSTRING(NombreHorario, LOCATE('-', NombreHorario) + 1) AS Resto
                FROM siga200.tahorarios
                WHERE NombreHorario LIKE '%-%'

                UNION ALL

                SELECT 
                    id_Horarios,
                    SUBSTRING_INDEX(Resto, '-', 1) AS Dia,
                    CASE 
                        WHEN LOCATE('-', Resto) > 0 THEN SUBSTRING(Resto, LOCATE('-', Resto) + 1)
                        ELSE NULL
                    END AS Resto
                FROM CTE
                WHERE Resto IS NOT NULL
            )
            SELECT id_Horarios, Dia FROM CTE

            UNION

            SELECT id_Horarios, NombreHorario AS Dia 
            FROM siga200.tahorarios 
            WHERE NombreHorario NOT LIKE '%-%';
        ");

        DB::statement("
            INSERT INTO schedules (group_id, day, start_hour, end_hour)
            SELECT 
                    gr.id_Grupo AS group_id,
                    vh.Dia AS day,
                    vh2.start_hour AS start_hour,
                    vh2.end_hour AS end_hour
                FROM 
                    siga200.tagrupo gr
                JOIN `groups` gro ON gro.id = gr.id_Grupo
                JOIN vista_horarios vh ON vh.id_Horarios = gr.id_Horarios
                JOIN unap_infouna.vista_horas vh2 ON vh2.id = gr.id_Hora
                ORDER BY gr.id_Grupo, day;
            ");

        DB::statement("DROP VIEW IF EXISTS vista_horas;");
        DB::statement("DROP VIEW IF EXISTS vista_horarios;");
        $this->info('Schedules inserted successfully.');
    }

       //enrollment groups
    private function insertEnrollmentGroups()
    {
        $this->info('Inserting enrollment groups...');
        if (DB::table('enrollment_groups')->count() > 0) {
            $this->info('Enrollment groups already exist in the database. Skipping insertion.');
            return;
        }

        DB::statement("
            INSERT INTO enrollment_groups (id, student_id, group_id, period_id, `status`, enrollment_modality, special_enrollment, with_enrollment)
            SELECT
                DISTINCT mat.id_CodigoMatricula AS id,
                st.id AS student_id,
                gr.id AS group_id,
                pe.id AS period_id,
                'MATRICULADO' AS `status`,
                'PRESENCIAL' AS enrollment_modality,
                CASE WHEN mo.id = 8 THEN 1 ELSE 0 END AS special_enrollment,
                CASE WHEN mat.CostoMatricula > 0 THEN 1 
                    WHEN st.student_type_id = 3 AND mo.id = 4 AND mat.CostoMensualidad = 86.0 THEN 1 
                    ELSE 0 
                    END 
                AS with_enrollment
            FROM
                siga200.tacursosaperturadosestudiante ap
                JOIN siga200.taregistramatricula mat ON mat.id_CursosAperturadosEstu = ap.id_CursosAperturadosEstu
                JOIN v_student_references vsr ON ap.id_Estudiante = vsr.referenced_student_id
                JOIN students st ON vsr.primary_student_id = st.id
                JOIN `groups` gr ON gr.id = ap.id_Grupo
                JOIN siga200.taaniomatricula ani ON ani.id_AnioMatricula = ap.id_AnioMatricula
                JOIN periods pe ON pe.`year` = ani.NombreAnioMatricula
                AND pe.`month` = ap.id_MesMatricula
                JOIN courses cur ON cur.id = gr.course_id
                JOIN modules mo ON mo.id = cur.module_id
            ORDER BY
                id,
                period_id,
                group_id;
        ");
        $this->info('Enrollment groups inserted successfully.');
    }

    //enrollment
    private function insertEnrollments()
    {
        $this->info('Inserting enrollments...');
        if (DB::table('enrollments')->count() > 0) {
            $this->info('Enrollments already exist in the database. Skipping insertion.');
            return;
        }

        DB::statement("
                CREATE OR REPLACE VIEW v_student_references AS
                SELECT
                    s.id AS primary_student_id,
                    CAST(jt.referenced_id AS UNSIGNED) AS referenced_student_id
                FROM
                    students s
                    JOIN JSON_TABLE(
                        CONCAT('[\"', REPLACE(TRIM(s.ref), ',', '\",\"'), '\"]'),
                        '$[*]' COLUMNS (
                            referenced_id VARCHAR(50) PATH '$'
                        )
                    ) AS jt
                WHERE
                    s.ref IS NOT NULL AND s.ref != ''

                UNION ALL

                SELECT
                    s.id AS primary_student_id,
                    s.id AS referenced_student_id
                FROM
                    students s
                WHERE
                    s.ref IS NULL OR s.ref = '';
        ");

        DB::statement("
            INSERT INTO enrollments (student_id, module_id, created_at)
            SELECT DISTINCT
                st.id AS student_id,
                mo.id AS module_id,
                fe.full_date AS created_at
            FROM
                siga200.tacursosaperturadosestudiante ap
            JOIN v_student_references vsr ON ap.id_Estudiante = vsr.referenced_student_id
            JOIN students st ON vsr.primary_student_id = st.id
            JOIN siga200.tagrupo gru ON gru.id_Grupo = ap.id_Grupo
            JOIN courses cur ON cur.id = gru.id_Cursos
            JOIN modules mo ON mo.id = cur.module_id
            LEFT JOIN (
                SELECT
                    eg.student_id, 
                    co.module_id, 
                    MIN(STR_TO_DATE(CONCAT(pe.`year`, '-', pe.`month`, '-01 00:00:00'), '%Y-%m-%d %H:%i:%s')) AS full_date
                FROM enrollment_groups eg
                JOIN `groups` gr ON gr.id = eg.group_id
                JOIN courses co ON co.id = gr.course_id
                JOIN periods pe ON pe.id = eg.period_id
                WHERE co.module_id != 8
                GROUP BY eg.student_id, co.module_id
            ) fe ON fe.student_id = st.id AND fe.module_id = mo.id
            WHERE ap.id_CursosAperturadosEstu IN (
                SELECT DISTINCT mat.id_CursosAperturadosEstu
                FROM siga200.taregistramatricula mat
            )
            AND mo.id NOT IN (8)
            ORDER BY created_at ASC;
        ");

        // DB::statement("DROP VIEW IF EXISTS v_student_references;");

        $this->info('Enrollments inserted successfully.');
    }
 
    //enrollment grades
    private function insertEnrollmentGrades()
    {
        $this->info('Inserting enrollment grades...');
        if (DB::table('enrollment_grades')->count() > 0) {
            $this->info('Enrollment grades already exist in the database. Skipping insertion.');
            return;
        }

        DB::statement("
            INSERT INTO enrollment_grades(id, enrollment_group_id, grade, created_at)
            SELECT 
                ca.id_Calificacion AS id,
                ca.id_CodigoMatricula AS enrollment_group_id,
                CASE 
                    WHEN ca.Nota REGEXP '^[0-9]+(\.[0-9]+)?$' THEN ca.Nota  
                    ELSE '00' 
                END AS grade,
                CONCAT(ca.FechaCalificacion, ' 00:00:00') AS created_at
            FROM siga200.tacalificacion ca
            JOIN enrollment_groups gr ON gr.id = ca.id_CodigoMatricula;
        ");
        $this->info('Enrollment grades inserted successfully.');
    }

    private function insertEnrollmentUnitGrades()
    {
        $this->info('Inserting enrollment unit grades...');
        if (DB::table('enrollment_unit_grades')->count() > 0) {
            $this->info('Enrollment unit grades already exist in the database. Skipping insertion.');
            return;
        }

        DB::statement("
            INSERT INTO enrollment_unit_grades ( grade, enrollment_grade_id, `order`)
            SELECT
            CASE 
                WHEN t.row_num = 1 THEN ROUND((eg.grade - 2) / 0.9, 2)
                ELSE 2
            END AS grade,
            eg.id AS enrollment_grade_id,
            t.row_num AS `order`
            FROM enrollment_grades eg
            JOIN (SELECT 1 AS row_num UNION SELECT 2) t ON 1=1
            ORDER BY eg.id, t.row_num;
        ");
        $this->info('Enrollment unit grades inserted successfully.');
    }

    private function insertPayment()
    {
        $this->info('Inserting payments...');
        if (DB::table('payments')->count() > 0) {
            $this->info('Payments already exist in the database. Skipping insertion.');
            return;
        }

        DB::statement("
            UPDATE siga200.taregistramatricula
            SET Fecha = '2010-01-01'
            WHERE Fecha like '0000-00-00';
        ");

        DB::statement("SET @counter := 0;");

        DB::statement("
        
            INSERT INTO payments (
                student_id,
                `date`,
                amount,
                ref,
                sequence_number,
                payment_type_id,
                enrollment_id,
                is_used,
                is_enabled
            )
            SELECT
                cas.id_Estudiante AS student_id,
                rm.Fecha  AS `date`,
                (rm.CostoMensualidad + rm.CostoMatricula) AS amount,
                rm.NumeroComprobante AS ref,
                LPAD(@counter := @counter + 1, 7, '0') AS sequence_number,
                IF(rm.id_TipoPago = 0, 1, rm.id_TipoPago) AS payment_type_id,
                eg.id AS enrollment_id,
                1 AS is_used,
                0 AS is_enabled
            FROM siga200.taregistramatricula AS rm
            JOIN siga200.tacursosaperturadosestudiante cas ON cas.id_CursosAperturadosEstu = rm.id_CursosAperturadosEstu
            JOIN enrollment_groups eg ON eg.id = rm.id_CodigoMatricula
            join students s on s.id = cas.id_Estudiante
            WHERE rm.id_CodigoMatricula IN (
                SELECT min(rm2.id_CodigoMatricula)
                FROM siga200.taregistramatricula rm2
                GROUP BY rm2.NumeroComprobante, rm2.Fecha, (rm2.CostoMensualidad + rm2.CostoMatricula)
                HAVING COUNT(*) > 1
            );

        ");


        DB::statement("
            INSERT INTO payments (
                `student_id`,
                `date`,
                `amount`,
                `ref`,
                `sequence_number`,
                `payment_type_id`,
                `enrollment_id`,
                `is_used`,
                `is_enabled`
            )
            SELECT
                cas.id_Estudiante AS student_id,
                rm.Fecha  AS `date`,
                (rm.CostoMensualidad + rm.CostoMatricula) AS amount,
                rm.NumeroComprobante AS ref,
                CASE 
                    WHEN rm.NumeroComprobante REGEXP '^[0-9]{6}-[0-9]{1}$' THEN SUBSTRING_INDEX(rm.NumeroComprobante, '-', 1)
                    WHEN rm.NumeroComprobante REGEXP '^[0-9]+$' THEN rm.NumeroComprobante
                    ELSE LPAD(@counter := @counter + 1, 7, '0')
                END AS sequence_number,
                IF(rm.id_TipoPago = 0, 1, rm.id_TipoPago) AS payment_type_id,
                eg.id AS enrollment_id,
                1 AS is_used,
                0 AS is_enabled
            FROM siga200.taregistramatricula AS rm
            JOIN siga200.tacursosaperturadosestudiante cas ON cas.id_CursosAperturadosEstu = rm.id_CursosAperturadosEstu
            JOIN enrollment_groups eg ON eg.id = rm.id_CodigoMatricula
            JOIN students s ON s.id = cas.id_Estudiante
            WHERE rm.id_CodigoMatricula IN (
                SELECT MIN(rm2.id_CodigoMatricula)
                FROM siga200.taregistramatricula rm2
                GROUP BY rm2.NumeroComprobante, rm2.Fecha, (rm2.CostoMensualidad + rm2.CostoMatricula)
                HAVING COUNT(*) = 1
            );
        ");

        $this->info('Payments inserted successfully.');
    }
}
