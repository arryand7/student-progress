# Tracking Checklist (PRD/RBAC/STORIES)

Status: done / partial / missing

| Area | Item | Status | Evidence path |
|---|---|---|---|
| PRD Scope | Weekly academic evaluations | done | `app/Http/Controllers/Pembina/EvaluationController.php` |
| PRD Scope | Multi-program & multi-subject | done | `app/Models/Program.php` |
| PRD Scope | Flexible components | done | `app/Models/Component.php` |
| PRD Scope | Metrics (score/time/questions) | done | `app/Models/EvaluationDetail.php` |
| PRD Scope | RBAC + configurable permissions | done | `routes/web.php`, `app/Http/Controllers/Superadmin/PermissionController.php` |
| PRD Scope | SSO integration | done | `app/Http/Controllers/Auth/SsoController.php`, `config/sso.php` |
| FR | FR-01 Multi program/subject | done | `app/Models/Program.php` |
| FR | FR-02 Component configuration | done | `app/Http/Controllers/Admin/ComponentController.php` |
| FR | FR-03 Immutable weekly records | partial | `app/Models/Evaluation.php`, `routes/web.php` |
| FR | FR-04 Weighted score | done | `app/Models/Evaluation.php` |
| FR | FR-05 Trend visualization | done | `app/Services/AnalyticsService.php` |
| FR | FR-06 Partial component eval | done | `app/Models/Evaluation.php` |
| RBAC | Students cannot create/edit eval | done | `routes/web.php` |
| RBAC | Admin cannot input/edit eval | done | `routes/web.php` |
| RBAC | Historical evaluations cannot be deleted | done | `routes/web.php` |
| RBAC | Only Superadmin override lock | done | `app/Services/EvaluationService.php`, `routes/web.php` |
| RBAC | Role assignment via SSO | done | `app/Http/Controllers/Auth/SsoController.php`, `config/sso.php` |
| RBAC | Configurable permissions catalog | done | `database/seeders/PermissionSeeder.php` |
| RBAC | Only Superadmin manage permissions | done | `routes/web.php` |
| RBAC | Permission changes logged | done | `app/Services/AuditService.php` |
| RBAC | Permissions enforced per action | done | `routes/web.php` |
| Audit | Permission changes audited | done | `app/Services/AuditService.php` |
| Audit | Evaluation lock/unlock audited | done | `app/Services/EvaluationService.php`, `routes/web.php` |
| Audit | Impersonation audited | done | `app/Http/Controllers/Superadmin/ImpersonationController.php`, `app/Services/AuditService.php` |
| Story | US-SA-01 Manage Programs | done | `app/Http/Controllers/Admin/ProgramController.php` |
| Story | US-SA-05 Manage Permissions | done | `routes/web.php`, `app/Http/Controllers/Superadmin/PermissionController.php` |
| Story | US-AD-01 Manage Students | done | `app/Http/Controllers/Admin/StudentController.php`, `resources/views/admin/students` |
| Story | US-GR-01 Weekly Evaluation Input | done | `app/Http/Controllers/Pembina/EvaluationController.php`, `app/Models/Evaluation.php` |
| Story | US-GR-03 View Progress (scoped) | done | `app/Http/Controllers/Pembina/ProgressController.php` |
| Story | US-ST-01 View Personal Progress | done | `app/Http/Controllers/Student/DashboardController.php` |
| Story | US-SYS-01 RBAC Enforcement | done | `routes/web.php`, `app/Http/Middleware/CheckPermission.php` |
| DoD | Unauthorized access tested | done | `tests/Feature/RbacTest.php` |
| NFR | Performance < 2s analytics | partial | `app/Services/AnalyticsService.php` |
| NFR | Usability < 5 min input | missing | (no UX test) |
| NFR | Security: RBAC + audit | done | `routes/web.php`, `app/Services/AuditService.php` |
