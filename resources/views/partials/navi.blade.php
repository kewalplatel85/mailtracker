<div class="min-h-full">
    <nav class="bg-gray-800">
        <div class="mx-auto max-w-full px-6 sm:px-8 lg:px-12">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <img class="size-8" src="https://tailwindui.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company">
                    </div>
                    @if(isset($currentCompany))
                    <div class="ml-3 hidden md:block">
                        <div class="text-sm font-medium text-gray-300">{{ $currentCompany->name }}</div>
                    </div>
                    @endif
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="{{route('dashboard')}}" data-route="{{url(route('dashboard'))}}" class="nav-link rounded-md px-3 py-2 text-sm font-medium text-white" aria-current="page">Dashboard</a>
                            <a href="{{route('labels.index')}}" data-route="{{url(route('labels.index'))}}" class="nav-link rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Storage Labels</a>
                            @can('users.view')
                            <a href="{{ route('admin.users.index') }}" class="nav-link rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Users</a>
                            @endcan
                            {{-- <a href="#" class="nav-link rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Custom SMS</a> --}}
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6">
                        <!-- User Info Section -->
                        <div class="flex items-center space-x-3 mr-4">
                            <div class="text-right">
                                <div class="text-sm font-medium text-white">{{ Auth::user()->name ?? 'User' }}</div>
                                <div class="text-xs text-gray-300">
                                    @if(Auth::user()->company)
                                        {{ Auth::user()->company->name }}
                                    @endif
                                    @if(Auth::user()->isCompanyAdmin())
                                        <span class="ml-1 text-blue-300">• Admin</span>
                                    @elseif(Auth::user()->isSuperAdmin())
                                        <span class="ml-1 text-purple-300">• Super Admin</span>
                                    @else
                                        <span class="ml-1 text-green-300">• User</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Company Switcher for Super Admin -->
                        @if(Auth::user()->isSuperAdmin())
                        <div class="flex items-center space-x-2 mr-4">
                            <select id="companySwitcher" class="px-2 py-1 text-xs border border-gray-600 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">{{ Auth::user()->company ? Auth::user()->company->name : 'Select Company' }}</option>
                                @foreach(\App\Models\Company::where('status', 'active')->get() as $company)
                                    <option value="{{ $company->id }}"
                                            {{ session('current_company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <button type="button" class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden">
                            <span class="absolute -inset-1.5"></span>
                            <span class="sr-only">View notifications</span>
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                        </button>

                        <!-- Profile dropdown -->
                        <div class="relative ml-3">
                            <div>
                                <button type="button" class="relative flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="absolute -inset-1.5"></span>
                                    <span class="sr-only">Open user menu</span>
                                    @if(Auth::user()->profile_picture ?? false)
                                        <img class="size-8 rounded-full" src="{{ Auth::user()->profile_picture }}" alt="{{ Auth::user()->name }}">
                                    @else
                                        <div class="size-8 rounded-full bg-indigo-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name ?? 'User')[1] ?? '', 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </button>
                            </div>

                            <!-- Dropdown menu -->
                            <div class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 ring-1 shadow-lg ring-black/5 focus:outline-hidden hidden" id="user-menu">
                                @if(Auth::user()->is_super_admin || Auth::user()->isCompanyAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-0">
                                    <span class="flex items-center">
                                        <span class="mr-2">⚙️</span>
                                        Admin Dashboard
                                    </span>
                                </a>
                                @endif
                                <div class="border-t border-gray-200"></div>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign out</a>
                                    <form id="logout-form" action="{{route('logout')}}" method="POST" style="display: none">
                                        @csrf
                                    </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <!-- Mobile menu button -->
                    <button type="button" class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="absolute -inset-0.5"></span>
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg class="hidden size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
                @if(isset($currentCompany))
                <div class="block px-3 py-2 text-base font-medium text-gray-300 border-b border-gray-700 mb-2">
                    {{ $currentCompany->name }}
                </div>
                @endif
                <a href="{{route('dashboard')}}" data-route="{{url(route('dashboard'))}}" class="nav-link block rounded-md px-3 py-2 text-base font-medium text-white" aria-current="page">Dashboard</a>
                <a href="{{route('labels.index')}}" data-route="{{url(route('labels.index'))}}" class="nav-link block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Storage Labels</a>
                @can('users.view')
                <a href="{{ route('admin.users.index') }}" class="nav-link block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Users</a>
                @endcan
                {{-- <a href="#" class="nav-link block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Custom SMS</a> --}}
            </div>
            <div class="border-t border-gray-700 pt-4 pb-3">
                <div class="flex items-center px-5">
                    <div class="shrink-0">
                        @if(Auth::user()->profile_picture ?? false)
                            <img class="size-10 rounded-full" src="{{ Auth::user()->profile_picture }}" alt="{{ Auth::user()->name }}">
                        @else
                            <div class="size-10 rounded-full bg-indigo-600 flex items-center justify-center">
                                <span class="text-base font-medium text-white">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name ?? 'User')[1] ?? '', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="ml-3">
                        <div class="text-base/5 font-medium text-white">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="text-sm font-medium text-gray-400">{{ Auth::user()->email ?? 'user@example.com' }}</div>
                        @if(isset($currentCompany))
                        <div class="text-xs font-medium text-gray-500">{{ $currentCompany->name }}</div>
                        @endif
                    </div>
                    <button type="button" class="relative ml-auto shrink-0 rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">View notifications</span>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </button>
                </div>
                <div class="mt-3 space-y-1 px-2">
                    {{-- <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Your Profile</a>
                    <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Settings</a> --}}
                    <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign out</a>
                        <form id="logout-form" action="{{route('logout')}}" method="POST" style="display: none">
                            @csrf
                        </form>
                </div>

            </div>
        </div>
    </nav>
</div>

<script>
// Company switcher functionality for super admins
@if(Auth::user()->isSuperAdmin())
document.addEventListener('DOMContentLoaded', function() {
    const companySwitcher = document.getElementById('companySwitcher');

    if (companySwitcher) {
        companySwitcher.addEventListener('change', function() {
            const companyId = this.value;

            if (companyId) {
                // Create form and submit to switch company
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/companies/${companyId}/switch`;
                form.style.display = 'none';

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
});
@endif
</script>
