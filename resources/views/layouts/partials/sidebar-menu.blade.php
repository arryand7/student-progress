@php
    $user = auth()->user();
    $showAdminMenu = $user->hasPermission('program.create')
        || $user->hasPermission('program.edit')
        || $user->hasPermission('program.deactivate')
        || $user->hasPermission('subject.create')
        || $user->hasPermission('subject.edit')
        || $user->hasPermission('subject.deactivate')
        || $user->hasPermission('component.create')
        || $user->hasPermission('component.edit')
        || $user->hasPermission('component.toggle_active')
        || $user->hasPermission('component.adjust_weight')
        || $user->hasPermission('enrollment.assign_subject')
        || $user->hasPermission('enrollment.deactivate')
        || $user->hasPermission('student.create')
        || $user->hasPermission('student.edit_profile');
@endphp

<!-- Dashboard -->
<a href="{{ route('dashboard') }}" 
   class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-600' : '' }}">
    <span class="material-symbols-outlined mr-3">dashboard</span>
    Dashboard
</a>

@if($showAdminMenu)
    <div class="mt-6">
        <p class="px-4 text-xs font-semibold text-primary-300 uppercase tracking-wider mb-3">Manajemen</p>
        
        @if($user->hasPermission('program.create') || $user->hasPermission('program.edit') || $user->hasPermission('program.deactivate'))
            <a href="{{ route('admin.programs.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('admin.programs.*') ? 'bg-primary-600' : '' }}">
                <span class="material-symbols-outlined mr-3">category</span>
                Program
            </a>
        @endif
        
        @if(
            $user->hasPermission('subject.create')
            || $user->hasPermission('subject.edit')
            || $user->hasPermission('subject.deactivate')
            || $user->hasPermission('component.create')
            || $user->hasPermission('component.edit')
            || $user->hasPermission('component.toggle_active')
            || $user->hasPermission('component.adjust_weight')
        )
            <a href="{{ route('admin.subjects.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('admin.subjects.*') ? 'bg-primary-600' : '' }}">
                <span class="material-symbols-outlined mr-3">menu_book</span>
                Mata Pelajaran
            </a>

            <a href="{{ route('admin.pembina-assignments.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('admin.pembina-assignments.*') ? 'bg-primary-600' : '' }}">
                <span class="material-symbols-outlined mr-3">group</span>
                Penugasan Pembina
            </a>
        @endif
        
        @if($user->hasPermission('enrollment.assign_subject') || $user->hasPermission('enrollment.deactivate'))
            <a href="{{ route('admin.enrollments.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('admin.enrollments.*') ? 'bg-primary-600' : '' }}">
                <span class="material-symbols-outlined mr-3">people</span>
                Pendaftaran
            </a>
        @endif

        @if($user->hasPermission('student.create') || $user->hasPermission('student.edit_profile'))
            <a href="{{ route('admin.students.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('admin.students.*') ? 'bg-primary-600' : '' }}">
                <span class="material-symbols-outlined mr-3">person</span>
                Siswa
            </a>
        @endif
    </div>
@endif

@if($user->isSuperadmin() || $user->isPembina())
    <div class="mt-6">
        <p class="px-4 text-xs font-semibold text-primary-300 uppercase tracking-wider mb-3">Evaluasi</p>
        
        @if($user->hasPermission('evaluation.view_all_scoped') || $user->hasPermission('evaluation.create'))
            <a href="{{ route('pembina.evaluations.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('pembina.evaluations.*') ? 'bg-primary-600' : '' }}">
                <span class="material-symbols-outlined mr-3">assignment</span>
                Input Evaluasi
            </a>
        @endif
        
        @if($user->hasPermission('analytics.view_trend') || $user->hasPermission('analytics.compare_students'))
            <a href="{{ route('pembina.progress.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('pembina.progress.*') ? 'bg-primary-600' : '' }}">
                <span class="material-symbols-outlined mr-3">trending_up</span>
                Progress Siswa
            </a>
        @endif
    </div>
@endif

@if($user->isStudent())
    <div class="mt-6">
        <p class="px-4 text-xs font-semibold text-primary-300 uppercase tracking-wider mb-3">Progress Saya</p>
        
        @if($user->hasPermission('analytics.view_trend'))
            <a href="{{ route('student.dashboard') }}" 
               class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('student.*') ? 'bg-primary-600' : '' }}">
                <span class="material-symbols-outlined mr-3">insights</span>
                Lihat Progress
            </a>
        @endif
    </div>
@endif

@if($user->isSuperadmin())
    <div class="mt-6">
        <p class="px-4 text-xs font-semibold text-primary-300 uppercase tracking-wider mb-3">Superadmin</p>
        
        @if($user->hasPermission('access.manage_permissions'))
            <a href="{{ route('superadmin.permissions.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('superadmin.permissions.*') ? 'bg-primary-600' : '' }}">
                <span class="material-symbols-outlined mr-3">admin_panel_settings</span>
                Hak Akses
            </a>
        @endif
        
        @if($user->hasPermission('access.view_audit_log'))
            <a href="{{ route('superadmin.audit-logs.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('superadmin.audit-logs.*') ? 'bg-primary-600' : '' }}">
                <span class="material-symbols-outlined mr-3">history</span>
                Audit Log
            </a>
        @endif
    </div>
@endif
