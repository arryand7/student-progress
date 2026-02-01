<?php

namespace App\Support;

class DefaultRolePermissions
{
    public static function permissions(): array
    {
        return [
            // Dashboard
            ['name' => 'dashboard.view_aggregate_stats', 'category' => 'dashboard', 'description' => 'View aggregate statistics on dashboard', 'is_configurable' => true],
            ['name' => 'dashboard.view_cross_subject', 'category' => 'dashboard', 'description' => 'View cross-subject data', 'is_configurable' => true],

            // Program & Subject
            ['name' => 'program.create', 'category' => 'program', 'description' => 'Create new programs', 'is_configurable' => true],
            ['name' => 'program.edit', 'category' => 'program', 'description' => 'Edit existing programs', 'is_configurable' => true],
            ['name' => 'program.deactivate', 'category' => 'program', 'description' => 'Deactivate programs', 'is_configurable' => true],
            ['name' => 'subject.create', 'category' => 'subject', 'description' => 'Create new subjects', 'is_configurable' => true],
            ['name' => 'subject.edit', 'category' => 'subject', 'description' => 'Edit existing subjects', 'is_configurable' => true],
            ['name' => 'subject.deactivate', 'category' => 'subject', 'description' => 'Deactivate subjects', 'is_configurable' => true],

            // Academic Components
            ['name' => 'component.create', 'category' => 'component', 'description' => 'Create new components', 'is_configurable' => true],
            ['name' => 'component.edit', 'category' => 'component', 'description' => 'Edit existing components', 'is_configurable' => true],
            ['name' => 'component.toggle_active', 'category' => 'component', 'description' => 'Toggle component active status', 'is_configurable' => true],
            ['name' => 'component.adjust_weight', 'category' => 'component', 'description' => 'Adjust component weights', 'is_configurable' => true],

            // Student & Enrollment
            ['name' => 'student.create', 'category' => 'student', 'description' => 'Create new student profiles', 'is_configurable' => true],
            ['name' => 'student.edit_profile', 'category' => 'student', 'description' => 'Edit student profiles', 'is_configurable' => true],
            ['name' => 'enrollment.assign_subject', 'category' => 'enrollment', 'description' => 'Assign students to subjects', 'is_configurable' => true],
            ['name' => 'enrollment.deactivate', 'category' => 'enrollment', 'description' => 'Deactivate student enrollments', 'is_configurable' => true],

            // Evaluations
            ['name' => 'evaluation.create', 'category' => 'evaluation', 'description' => 'Create new evaluations', 'is_configurable' => true],
            ['name' => 'evaluation.edit_before_lock', 'category' => 'evaluation', 'description' => 'Edit evaluations before lock', 'is_configurable' => true],
            ['name' => 'evaluation.add_notes', 'category' => 'evaluation', 'description' => 'Add notes to evaluations', 'is_configurable' => true],
            ['name' => 'evaluation.view_all_scoped', 'category' => 'evaluation', 'description' => 'View all evaluations in scope', 'is_configurable' => true],

            // Analytics
            ['name' => 'analytics.view_trend', 'category' => 'analytics', 'description' => 'View progress trends', 'is_configurable' => true],
            ['name' => 'analytics.compare_students', 'category' => 'analytics', 'description' => 'Compare student progress', 'is_configurable' => true],
            ['name' => 'analytics.view_component_breakdown', 'category' => 'analytics', 'description' => 'View component breakdown analytics', 'is_configurable' => true],

            // Reports
            ['name' => 'report.export_pdf', 'category' => 'report', 'description' => 'Export reports as PDF', 'is_configurable' => true],
            ['name' => 'report.export_csv', 'category' => 'report', 'description' => 'Export reports as CSV', 'is_configurable' => true],

            // Access Control
            ['name' => 'access.manage_permissions', 'category' => 'access', 'description' => 'Manage role permissions', 'is_configurable' => true],
            ['name' => 'access.manage_settings', 'category' => 'access', 'description' => 'Manage system settings', 'is_configurable' => true],
            ['name' => 'access.manage_users', 'category' => 'access', 'description' => 'Manage users', 'is_configurable' => true],
            ['name' => 'access.view_audit_log', 'category' => 'access', 'description' => 'View audit logs', 'is_configurable' => true],
            ['name' => 'access.impersonate_user', 'category' => 'access', 'description' => 'Impersonate other users', 'is_configurable' => true],
        ];
    }

    public static function roleDefaults(): array
    {
        return [
            'admin' => [
                'dashboard.view_aggregate_stats',
                'dashboard.view_cross_subject',
                'subject.create',
                'subject.edit',
                'subject.deactivate',
                'component.create',
                'component.edit',
                'component.toggle_active',
                'student.create',
                'student.edit_profile',
                'enrollment.assign_subject',
                'enrollment.deactivate',
                'evaluation.view_all_scoped',
                'analytics.view_trend',
                'analytics.compare_students',
                'analytics.view_component_breakdown',
                'report.export_pdf',
                'report.export_csv',
            ],
            'pembina' => [
                'dashboard.view_aggregate_stats',
                'component.create',
                'component.edit',
                'component.toggle_active',
                'component.adjust_weight',
                'enrollment.assign_subject',
                'enrollment.deactivate',
                'evaluation.create',
                'evaluation.edit_before_lock',
                'evaluation.add_notes',
                'evaluation.view_all_scoped',
                'analytics.view_trend',
                'analytics.compare_students',
                'analytics.view_component_breakdown',
                'report.export_pdf',
                'report.export_csv',
            ],
            'student' => [
                'analytics.view_trend',
                'report.export_pdf',
            ],
        ];
    }
}
