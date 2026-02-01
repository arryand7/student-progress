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

    $adminGroupActive = request()->routeIs('admin.*');
    $evalGroupActive = request()->routeIs('pembina.*');
    $studentGroupActive = request()->routeIs('student.*');
    $superadminGroupActive = request()->routeIs('superadmin.*');
@endphp

<!-- Dashboard -->
<a href="{{ route('dashboard') }}" 
   class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-600' : '' }}">
    <span class="material-symbols-outlined mr-3">dashboard</span>
    Dashboard
</a>

@if($showAdminMenu)
    <div class="mt-6" x-data="{ open: {{ $adminGroupActive ? 'true' : 'false' }} }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-4 text-xs font-semibold text-primary-300 uppercase tracking-wider mb-3">
            <span>Manajemen</span>
            <span class="material-symbols-outlined text-base transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
        </button>
        
        <div x-show="open" x-cloak class="space-y-1">
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
    </div>
@endif

@if($user->isSuperadmin() || $user->isPembina())
    <div class="mt-6" x-data="{ open: {{ $evalGroupActive ? 'true' : 'false' }} }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-4 text-xs font-semibold text-primary-300 uppercase tracking-wider mb-3">
            <span>Evaluasi</span>
            <span class="material-symbols-outlined text-base transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
        </button>
        
        <div x-show="open" x-cloak class="space-y-1">
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
    </div>
@endif

@if($user->isStudent())
    <div class="mt-6" x-data="{ open: {{ $studentGroupActive ? 'true' : 'false' }} }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-4 text-xs font-semibold text-primary-300 uppercase tracking-wider mb-3">
            <span>Progress Saya</span>
            <span class="material-symbols-outlined text-base transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
        </button>
        
        <div x-show="open" x-cloak class="space-y-1">
            @if($user->hasPermission('analytics.view_trend'))
                <a href="{{ route('student.dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('student.*') ? 'bg-primary-600' : '' }}">
                    <span class="material-symbols-outlined mr-3">insights</span>
                    Lihat Progress
                </a>
            @endif
        </div>
    </div>
@endif

@if($user->isSuperadmin())
    <div class="mt-6" x-data="{ open: {{ $superadminGroupActive ? 'true' : 'false' }} }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-4 text-xs font-semibold text-primary-300 uppercase tracking-wider mb-3">
            <span>Superadmin</span>
            <span class="material-symbols-outlined text-base transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
        </button>
        
        <div x-show="open" x-cloak class="space-y-1">
            @if($user->hasPermission('access.manage_users'))
                <a href="{{ route('superadmin.users.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('superadmin.users.*') ? 'bg-primary-600' : '' }}">
                    <span class="material-symbols-outlined mr-3">group</span>
                    Manajemen User
                </a>
            @endif

            @if($user->hasPermission('access.manage_permissions'))
                <a href="{{ route('superadmin.permissions.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('superadmin.permissions.*') ? 'bg-primary-600' : '' }}">
                    <span class="material-symbols-outlined mr-3">admin_panel_settings</span>
                    Hak Akses
                </a>
            @endif
            
            @if($user->hasPermission('access.manage_settings'))
                <a href="{{ route('superadmin.settings.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg text-primary-100 hover:bg-primary-600 transition-colors {{ request()->routeIs('superadmin.settings.*') ? 'bg-primary-600' : '' }}">
                    <span class="material-symbols-outlined mr-3">settings</span>
                    Pengaturan
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
    </div>
@endif
