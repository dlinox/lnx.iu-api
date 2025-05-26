<?php
/*
[
  menuItem({
    label: "Inicio",
    key: "Dashboard",
    route: "Dashboard",
    iconName: "home",
  }),

  {
    label: "Gestión Académica",
    key: "academic-management",
    icon: renderIcon("book"),
    children: [
      {
        type: "group",
        label: "Estudiantes y Docentes",
        key: "people",
        children: [
          menuItem({
            label: "Estudiantes",
            key: "Student",
            route: "Student",
            iconName: "personalcard",
          }),
          menuItem({
            label: "Docentes",
            key: "Teacher",
            route: "Teacher",
            iconName: "teacher",
          }),
        ],
      },
      {
        type: "group",
        label: "Planificación Académica",
        key: "academic-planning",
        children: [
          menuItem({
            label: "Periodos Académicos",
            key: "Period",
            route: "Period",
            iconName: "calendar-2",
          }),
          menuItem({
            label: "Planes de Estudio",
            key: "Curriculum",
            route: "Curriculum",
            iconName: "book-1",
          }),
          menuItem({
            label: "Áreas",
            key: "Area",
            route: "Area",
            iconName: "book-square",
          }),
          menuItem({
            label: "Módulos",
            key: "Module",
            route: "Module",
            iconName: "bookmark",
          }),
          menuItem({
            label: "Cursos",
            key: "Course",
            route: "Course",
            iconName: "book-saved",
          }),
        ],
      },
    ],
  },

  {
    label: "Carga Académica",
    key: "academic-workload",
    icon: renderIcon("folder-open"),
    children: [
      menuItem({
        label: "Apertura de Grupos",
        key: "Group",
        route: "Group",
        iconName: "calendar-add",
      }),
      menuItem({
        label: "Gestión de Grupos",
        key: "GroupManager",
        route: "GroupManager",
        iconName: "data",
      }),
    ],
  },
  menuItem({
    label: "Supervisión Académica",
    key: "academic-supervision",
    route: "AcademicSupervision",
    iconName: "speedometer",
  }),
  {
    label: "Notas",
    key: "grades",
    icon: renderIcon("archive-book"),
    children: [
      menuItem({
        label: "Acta de Notas",
        key: "AcademicRecord",
        route: "AcademicRecord",
        iconName: "archive-book",
      }),
      menuItem({
        label: "Habilitaciones",
        key: "grade-deadline",
        route: "GradeDeadline",
        iconName: "calendar-2",
      }),
    ],
  },

  {
    label: "Administración",
    key: "administration",
    icon: renderIcon("cpu-setting"),
    children: [
      {
        type: "group",
        label: "Configuraciones Generales",
        key: "general-settings",
        children: [
          menuItem({
            label: "Tipos de Documento",
            key: "DocumentType",
            route: "DocumentType",
            iconName: "tag",
          }),
          menuItem({
            label: "Tipos de Estudiante",
            key: "StudentType",
            route: "StudentType",
            iconName: "tag",
          }),
          menuItem({
            label: "Métodos de Pago",
            key: "PaymentType",
            route: "PaymentType",
            iconName: "tag",
          }),
        ],
      },
      {
        type: "group",
        label: "Infraestructura",
        key: "infrastructure",
        children: [
          menuItem({
            label: "Laboratorios",
            key: "Laboratory",
            route: "Laboratory",
            iconName: "devices",
          }),
        ],
      },
    ],
  },

  {
    label: "Matrículas",
    key: "enrollments",
    icon: renderIcon("folder"),
    children: [
      menuItem({
        label: "Realizar Matrícula",
        key: "enrollment",
        route: "Enrollment",
        iconName: "folder-add",
      }),
      menuItem({
        label: "Matrículas",
        key: "virtual-enrollment",
        route: "EnrollmentVirtual",
        iconName: "folder-cloud",
      }),
      menuItem({
        label: "Convalidaciones",
        key: "Recognition",
        route: "Recognition",
        iconName: "convertshape-2",
      }),
      menuItem({
        label: "Habilitaciones",
        key: "EnrollmentDeadline",
        route: "EnrollmentDeadline",
        iconName: "calendar-2",
      }),
    ],
  },

  {
    label: "Costos",
    key: "financial",
    icon: renderIcon("money"),
    children: [
      menuItem({
        label: "Matrícula (Módulos)",
        key: "ModulePrice",
        route: "ModulePrice",
        iconName: "moneys",
      }),
      menuItem({
        label: "Mensualidad (Cursos)",
        key: "CoursePrice",
        route: "CoursePrice",
        iconName: "moneys",
      }),
    ],
  },

  {
    label: "Reportes",
    key: "reports",
    icon: renderIcon("archive"),
    children: [
      menuItem({
        label: "Reporte de Estudiantes",
        key: "StudentReportView",
        route: "StudentReportView",
        iconName: "document-filter",
      }),
      // GroupReportView
      menuItem({
        label: "Reporte de Grupos",
        key: "GroupReportView",
        route: "GroupReportView",
        iconName: "document-filter",
      }),
    ],
  },
  {
    label: "Seguridad",
    key: "security",
    icon: renderIcon("shield"),
    children: [
      {
        type: "group",
        label: "Administradores",
        key: "security-admins",
        children: [
          menuItem({
            label: "Usuarios Administradores",
            key: "UserAdmin",
            route: "UserAdmin",
            iconName: "security-user",
          }),
          menuItem({
            label: "Roles Administradores",
            key: "RoleAdmin",
            route: "RoleAdmin",
            iconName: "key-square",
          }),
        ],
      },
      {
        type: "group",
        label: "Docentes",
        key: "security-teachers",
        children: [
          menuItem({
            label: "Usuarios Docentes",
            key: "UserTeacher",
            route: "UserTeacher",
            iconName: "security-user",
          }),
        ],
      },
      {
        type: "group",
        label: "Estudiantes",
        key: "security-students",
        children: [
          menuItem({
            label: "Usuarios Estudiantes",
            key: "UserStudent",
            route: "UserStudent",
            iconName: "security-user",
          }),
        ],
      },
    ],
  },
];
*/
return [
    'permissions' => [
        [
            'name' => 'module.dashboard',
            'display_name' => 'Dashboard',
            'group' => null,
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],

        [
            'name' => 'module.student',
            'display_name' => 'Estudiantes',
            'group' => null,
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'student.create',
            'display_name' => 'Crear Estudiante',
            'group' => 'module.student',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'student.edit',
            'display_name' => 'Editar Estudiante',
            'group' => 'module.student',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'student.delete',
            'display_name' => 'Eliminar Estudiante',
            'group' => 'module.student',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],

        [
            'name' => 'student.create-account',
            'display_name' => 'Crear Cuenta de Estudiante',
            'group' => 'module.student',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'module.teacher',
            'display_name' => 'Docentes',
            'group' => null,
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'teacher.create',
            'display_name' => 'Crear Docente',
            'group' => 'module.teacher',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'teacher.edit',
            'display_name' => 'Editar Docente',
            'group' => 'module.teacher',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'teacher.delete',
            'display_name' => 'Eliminar Docente',
            'group' => 'module.teacher',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'teacher.create-account',
            'display_name' => 'Crear Cuenta de Docente',
            'group' => 'module.teacher',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'module.period',
            'display_name' => 'Periodos Académicos',
            'group' => null,
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'period.create',
            'display_name' => 'Crear Periodo Académico',
            'group' => 'module.period',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'period.edit',
            'display_name' => 'Editar Periodo Académico',
            'group' => 'module.period',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'period.delete',
            'display_name' => 'Eliminar Periodo Académico',
            'group' => 'module.period',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],

        [
            'name' => 'module.curriculum',
            'display_name' => 'Planes de Estudio',
            'group' => null,
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'curriculum.create',
            'display_name' => 'Crear Plan de Estudio',
            'group' => 'module.curriculum',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'curriculum.edit',
            'display_name' => 'Editar Plan de Estudio',
            'group' => 'module.curriculum',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'curriculum.delete',
            'display_name' => 'Eliminar Plan de Estudio',
            'group' => 'module.curriculum',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],

        [
            'name' => 'module.area',
            'display_name' => 'Áreas',
            'group' => null,
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'area.create',
            'display_name' => 'Crear Área',
            'group' => 'module.area',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'area.edit',
            'display_name' => 'Editar Área',
            'group' => 'module.area',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'area.delete',
            'display_name' => 'Eliminar Área',
            'group' => 'module.area',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],

        [
            'name' => 'module.module',
            'display_name' => 'Módulos',
            'group' => null,
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'module.create',
            'display_name' => "Crear Módulo",
            "group" => "module.module",
            "model_type" => "admin",
            "guard_name" => "sanctum",
        ],
        [
            "name" => "module.edit",
            "display_name" => "Editar Módulo",
            "group" => "module.module",
            "model_type" => "admin",
            "guard_name" => "sanctum",
        ],
        [
            "name"     => "module.delete",
            "display_name"     => "Eliminar Módulo",
            "group"     => "module.module",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],

        [
            "name"     => "module.course",
            "display_name"     => "Cursos",
            'group' => null,
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],

        [
            "name"     => "course.create",
            "display_name"     => "Crear Curso",
            "group"     => "module.course",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "course.edit",
            "display_name"     => "Editar Curso",
            "group"     => "module.course",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "course.delete",
            "display_name"     => "Eliminar Curso",
            "group"     => "module.course",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],

        [
            'name' => 'module.group',
            'display_name' => 'Apertura de Grupos',
            'group' => null,
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'group.create',
            'display_name' => 'Crear Grupo',
            'group' => 'module.group',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'group.edit',
            'display_name' => 'Editar Grupo',
            'group' => 'module.group',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'group.delete',
            'display_name' => 'Eliminar Grupo',
            'group' => 'module.group',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],

        [
            'name' => 'module.group-manager',
            'display_name' => 'Gestión de Grupos',
            'group' => null,
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'group-manager.create',
            'display_name' => 'Crear Gestión de Grupos',
            'group' => 'module.group-manager',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'group-manager.edit',
            'display_name' =>     'Editar Gestión de Grupos',
            'group' => 'module.group-manager',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'group-manager.delete',
            'display_name' => 'Eliminar Gestión de Grupos',
            'group' => 'module.group-manager',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],

        [
            'name' => 'module.academic-supervision',
            'display_name' => 'Supervisión Académica',
            'group' => null,
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'academic-supervision.create',
            'display_name' => 'Crear Supervisión Académica',
            'group' => 'module.academic-supervision',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'academic-supervision.edit',
            'display_name' => 'Editar Supervisión Académica',
            'group' => 'module.academic-supervision',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'academic-supervision.delete',
            'display_name' =>     'Eliminar Supervisión Académica',
            'group'     =>     'module.academic-supervision',
            'model_type'     =>     'admin',
            'guard_name'     =>     'sanctum',
        ],

        [
            "name"     => "module.academic-record",
            "display_name"     => "Acta de Notas",
            "group"     => "module.academic-record",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"

        ],

        [
            "name"     => "academic-record.view",
            "display_name"     => "Ver Acta de Notas",
            "group"     => "module.academic-record",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "academic-record.print",
            "display_name"     => "Imprimir Acta de Notas",
            "group"     => "module.academic-record",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],

        [
            'name' => 'module.grade-deadline',
            'display_name' =>     'Ver Habilitaciones',
            'group'     =>     null,
            'model_type'     =>     'admin',
            'guard_name'     =>     'sanctum',
        ],
        [
            'name' => 'grade-deadline.create',
            'display_name' => 'Crear Habilitaciones',
            'group' => 'module.grade-deadline',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'grade-deadline.extencion',
            'display_name' => 'Extensión de Habilitaciones',
            'group' => 'module.grade-deadline',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],

        [
            "name"     => "module.enrollment",
            "display_name"     => "Ver Matrícula",
            "group"     => null,
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "enrollment.create",
            "display_name"     => "Crear Matrícula",
            "group"     => "module.enrollment",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "enrollment.edit",
            "display_name"     => "Editar Matrícula",
            "group"     => "module.enrollment",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],


        [
            'name'     =>     'module.enrollment-virtual',
            'display_name'     =>     'Realizar Matrículas',
            'group'     =>     null,
            'model_type'     =>     'admin',
            'guard_name'     =>     'sanctum',
        ],
        [
            'name' => 'enrollment-virtual.create',
            'display_name' => 'Crear Matrículas',
            'group' => 'module.enrollment-virtual',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'enrollment-virtual.edit',
            'display_name' => 'Editar Matrículas',
            'group' => 'module.enrollment-virtual',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],

        [
            "name"     => "module.recognition",
            "display_name"     => "Convalidaciones",
            "group"     => null,
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "recognition.create",
            "display_name"     => "Crear Convalidaciones",
            "group"     => "module.recognition",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "recognition.edit",
            "display_name"     => "Editar Convalidaciones",
            "group"     => "module.recognition",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "recognition.delete",
            "display_name"     =>     "Eliminar Convalidaciones",
            "group"     =>     "module.recognition",
            "model_type"     =>     "admin",
            "guard_name"     =>     'sanctum'
        ],

        [
            'name'     =>     'module.enrollment-deadline',
            'display_name'     =>     'Ver Habilitaciones de Matrícula',
            'group'     =>     null,
            'model_type'     =>     'admin',
            'guard_name'     =>     'sanctum',
        ],

        [
            'name' => 'enrollment-deadline.create',
            'display_name' => 'Crear Habilitaciones de Matrícula',
            'group' => 'module.enrollment-deadline',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'enrollment-deadline.edit',
            'display_name' => 'Editar Habilitaciones de Matrícula',
            'group' => 'module.enrollment-deadline',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'enrollment-deadline.delete',
            'display_name' =>     'Eliminar Habilitaciones de Matrícula',
            'group'     =>     'module.enrollment-deadline',
            'model_type'     =>     'admin',
            'guard_name'     =>     'sanctum',
        ],

        [
            "name"     => "module.module-price",
            "display_name"     => "Matrícula (Módulos)",
            "group"     => null,
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "module.module-price.create",
            "display_name"     => "Crear Matrícula (Módulos)",
            "group"     => "module.module-price",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "module.module-price.edit",
            "display_name"     => "Editar Matrícula (Módulos)",
            "group"     => "module.module-price",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "module.module-price.delete",
            "display_name"     =>     "Eliminar Matrícula (Módulos)",
            "group"     =>     "module.module-price",
            "model_type"     =>     "admin",
            "guard_name"     =>     'sanctum'
        ],

        [
            "name"     => "module.course-price",
            "display_name"     => "Mensualidad (Cursos)",
            "group"     => null,
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],

        [
            "name"     => "module.course-price.create",
            "display_name"     => "Crear Mensualidad (Cursos)",
            "group"     => "module.course-price",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "module.course-price.edit",
            "display_name"     => "Editar Mensualidad (Cursos)",
            "group"     => "module.course-price",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "module.course-price.delete",
            "display_name"     =>     "Eliminar Mensualidad (Cursos)",
            "group"     =>     "module.course-price",
            "model_type"     =>     "admin",
            "guard_name"     =>     'sanctum'
        ],

        [
            'name'     =>     'report.student',
            'display_name'     =>     'Reporte de Estudiantes',
            'group'     =>     null,
            'model_type'     =>     'admin',
            'guard_name'     =>     'sanctum',
        ],
        [
            'name' => 'report.group',
            'display_name' => 'Reporte de Grupos',
            'group' => null,
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],

        [
            'name'     =>     'module.user-admin',
            'display_name'     =>     'Ver Usuarios Administradores',
            'group'     =>     null,
            'model_type'     =>     'admin',
            'guard_name'     =>     'sanctum',
        ],
        [
            'name' => 'user-admin.create',
            'display_name' => 'Crear Usuario Administrador',
            'group' => 'module.user-admin',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'user-admin.edit',
            'display_name' => 'Editar Usuario Administrador',
            'group' => 'module.user-admin',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'user-admin.delete',
            'display_name' => 'Eliminar Usuario Administrador',
            'group' => 'module.user-admin',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],

        [
            'name'     =>     'module.role-admin',
            'display_name'     =>     'Ver Roles Administradores',
            'group'     =>     null,
            'model_type'     =>     'admin',
            'guard_name'     =>     'sanctum',
        ],
        [
            'name' => 'role-admin.create',
            'display_name' => 'Crear Rol Administrador',
            'group' => 'module.role-admin',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'role-admin.edit',
            'display_name' => 'Editar Rol Administrador',
            'group' => 'module.role-admin',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'role-admin.delete',
            'display_name' =>     'Eliminar Rol Administrador',
            'group'     =>     'module.role-admin',
            'model_type'     =>     'admin',
            'guard_name'     =>     'sanctum',
        ],

        [
            "name"     => "module.user-teacher",
            "display_name"     => "Ver Usuarios Docentes",
            "group"     => null,
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "user-teacher.create",
            "display_name"     => "Crear Usuario Docente",
            "group"     => "module.user-teacher",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "user-teacher.edit",
            "display_name"     => "Editar Usuario Docente",
            "group"     => "module.user-teacher",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "user-teacher.delete",
            "display_name"     => "Eliminar Usuario Docente",
            "group"     => "module.user-teacher",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],

        [
            'name'     =>     'module.user-student',
            'display_name'     =>     'Ver Usuarios Estudiantes',
            'group'     =>     null,
            'model_type'     =>     'admin',
            'guard_name'     =>     'sanctum',
        ],
        [
            'name' => 'user-student.create',
            'display_name' => 'Crear Usuario Estudiante',
            'group' => 'module.user-student',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'user-student.edit',
            'display_name' => 'Editar Usuario Estudiante',
            'group' => 'module.user-student',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'user-student.delete',
            'display_name' =>     'Eliminar Usuario Estudiante',
            'group'     =>     'module.user-student',
            'model_type'     =>     'admin',
            'guard_name'     =>     'sanctum',
        ],

        [
            'name' => 'module.document-type',
            'display_name' => 'Tipos de Documento',
            'group' => null,
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'document-type.create',
            'display_name' => 'Crear Tipo de Documento',
            'group' => 'module.document-type',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'document-type.edit',
            'display_name' => 'Editar Tipo de Documento',
            'group' => 'module.document-type',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'document-type.delete',
            'display_name' =>     'Eliminar Tipo de Documento',
            'group'     =>     'module.document-type',
            'model_type'     =>     'admin',
            'guard_name'     =>     'sanctum',
        ],

        [
            "name"     => "module.student-type",
            "display_name"     => "Tipos de Estudiante",
            "group"     => null,
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "student-type.create",
            "display_name"     => "Crear Tipo de Estudiante",
            "group"     => "module.student-type",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "student-type.edit",
            "display_name"     => "Editar Tipo de Estudiante",
            "group"     => "module.student-type",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "student-type.delete",
            "display_name"     =>     "Eliminar Tipo de Estudiante",
            "group"     =>     "module.student-type",
            "model_type"     =>     "admin",
            "guard_name"     =>     'sanctum'
        ],

        [
            "name"     => "module.payment-type",
            "display_name"     => "Métodos de Pago",
            "group"     => null,
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "payment-type.create",
            "display_name"     => "Crear Método de Pago",
            "group"     => "module.payment-type",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "payment-type.edit",
            "display_name"     => "Editar Método de Pago",
            "group"     => "module.payment-type",
            "model_type"     => "admin",
            "guard_name"     => "sanctum"
        ],
        [
            "name"     => "payment-type.delete",
            "display_name"     =>     "Eliminar Método de Pago",
            "group"     =>     "module.payment-type",
            "model_type"     =>     "admin",
            "guard_name"     =>     'sanctum'   
        ],

        [
            'name'     =>     'module.laboratory',
            'display_name'     =>     'Ver Laboratorios',
            'group'     =>     null,
            'model_type'     =>     'admin',
            'guard_name'     =>     'sanctum',
        ],
        [
            'name' => 'laboratory.create',
            'display_name' => 'Crear Laboratorio',
            'group' => 'module.laboratory',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'laboratory.edit',
            'display_name' => 'Editar Laboratorio',
            'group' => 'module.laboratory',
            'model_type' => 'admin',
            'guard_name' => 'sanctum',
        ],
        [
            'name' => 'laboratory.delete',
            'display_name' =>     'Eliminar Laboratorio',
            'group'     =>     'module.laboratory',
            'model_type'     =>     'admin',
            'guard_name'     =>     'sanctum',
        ],

    ]
];
