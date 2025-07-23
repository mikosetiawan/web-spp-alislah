 <!-- Sidebar -->
 <div class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-school-blue to-blue-900 text-white z-50">
     <div class="p-6">
         <div class="flex items-center space-x-3 mb-8">
             <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                 {{-- <span class="text-school-blue font-bold text-lg">SA</span> --}}
                 <img src="{{ asset('logo.png') }}" alt="">
             </div>
             <div>
                 <h1 class="text-xl font-bold">SMK Al-Ishlah</h1>
                 <p class="text-blue-200 text-sm">Sistem SPP</p>
             </div>
         </div>

         <nav class="space-y-2">
             <a href="#"
                 class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg bg-white bg-opacity-20">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                 </svg>
                 <span>Dashboard</span>
             </a>

             <a href="{{ route('students.index') }}"
                 class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                     </path>
                 </svg>
                 <span>Data Siswa</span>
             </a>

             <a href="{{ route('payments.index') }}"
                 class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                     </path>
                 </svg>
                 <span>Pembayaran SPP</span>
             </a>

             <a href="{{ route('spp-costs.index') }}"
                 class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                     </path>
                 </svg>
                 <span>Kelola SPP Siswa</span>
             </a>

             <a href="{{ route('payments.report') }}"
                 class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                     </path>
                 </svg>
                 <span>Laporan</span>
             </a>

             {{-- <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Pengaturan</span>
                </a> --}}
         </nav>
     </div>

     <div class="absolute bottom-0 w-full p-6">
         <div class="flex items-center space-x-3 mb-4">
             <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                 <span class="text-white text-sm font-medium">A</span>
             </div>
             <div>
                 <p class="text-sm font-medium">Admin</p>
                 <p class="text-xs text-blue-200">Administrator</p>
             </div>
         </div>

         <!-- Tombol Logout -->
         <form method="POST" action="{{ route('logout') }}">
             @csrf
             <button type="submit"
                 class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-green-600 hover:bg-green-700 text-white font-medium">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2h6a2 2 0 002-2v-1">
                     </path>
                 </svg>
                 <span>Logout</span>
             </button>
         </form>
         
     </div>
 </div>
