INSERT INTO enrollment_unit_grades (grade, enrollment_grade_id, `order`)
SELECT 
    CASE 
        WHEN t.row_num = 1 THEN eg.grade * 0.9  -- capacity_avg
        ELSE FLOOR(RAND() * 3)                 -- attitude (0,1,2)
    END AS grade, 
    eg.id AS enrollment_grade_id,
    t.row_num AS `order`
FROM enrollment_grades eg
JOIN (SELECT 1 AS row_num UNION SELECT 2) t ON 1=1
ORDER BY eg.id, t.row_num;