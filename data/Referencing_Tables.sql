SELECT table_name,
  constraint_name,
  status,
  owner
FROM all_constraints
WHERE r_owner          = 'JWL_HRIS_APR5'
AND constraint_type    = 'R'
AND r_constraint_name IN
  (SELECT constraint_name
  FROM all_constraints
  WHERE constraint_type IN ('P', 'U')
  AND table_name         = 'HRIS_EMPLOYEES'
  AND owner              = 'JWL_HRIS_APR5'
  )
ORDER BY table_name,
  constraint_name;