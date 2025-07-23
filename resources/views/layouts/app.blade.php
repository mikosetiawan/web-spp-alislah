@props(['title' => 'Dashboard SPP'])

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - SMK Al-Ishlah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'school-blue': '#1e3a8a',
                        'school-green': '#059669',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .status-paid {
            background: linear-gradient(45deg, #10b981, #059669);
        }

        .status-unpaid {
            background: linear-gradient(45deg, #ef4444, #dc2626);
        }

        .status-partial {
            background: linear-gradient(45deg, #f59e0b, #d97706);
        }

        .sidebar-item {
            transition: all 0.2s ease;
        }

        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }
    </style>
</head>

<body class="bg-gray-50">
    @include('layouts.sidebar')

    <!-- Main Content -->
    <div class="ml-64 min-h-screen">
        @include('layouts.navigation', ['title' => $title])

        <div class="container p-6">
            {{ $slot }}
        </div>
    </div>

    <script>
        // Simple animations and interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on page load
            const cards = document.querySelectorAll('.card-hover');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Add click effects to buttons
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Create ripple effect
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.5);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        pointer-events: none;
                    `;

                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Add search functionality
            const searchInput = document.querySelector('input[type="text"]');
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const tableRows = document.querySelectorAll('tbody tr');

                tableRows.forEach(row => {
                    const studentName = row.querySelector('.text-sm.font-medium').textContent
                        .toLowerCase();
                    const studentId = row.querySelector('.text-sm.text-gray-500').textContent
                        .toLowerCase();

                    if (studentName.includes(searchTerm) || studentId.includes(searchTerm)) {
                        row.style.display = '';
                        row.style.animation = 'fadeIn 0.3s ease';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Simulate real-time updates
            setInterval(() => {
                const notifications = document.querySelector('.relative span');
                if (notifications) {
                    notifications.classList.toggle('animate-pulse');
                }
            }, 3000);

            // Add mobile menu toggle functionality
            let mobileMenuOpen = false;
            const sidebar = document.querySelector('.fixed.inset-y-0.left-0');
            const mainContent = document.querySelector('.ml-64');

            // Create mobile menu button
            const mobileMenuButton = document.createElement('button');
            mobileMenuButton.innerHTML = `
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            `;
            mobileMenuButton.className =
                'lg:hidden p-2 text-gray-600 hover:text-gray-900 fixed top-4 left-4 z-50 bg-white rounded-lg shadow-md';

            document.body.appendChild(mobileMenuButton);

            mobileMenuButton.addEventListener('click', () => {
                mobileMenuOpen = !mobileMenuOpen;

                if (mobileMenuOpen) {
                    sidebar.style.transform = 'translateX(0)';
                    mainContent.style.marginLeft = '0';
                    mainContent.style.transform = 'translateX(256px)';
                } else {
                    sidebar.style.transform = 'translateX(-100%)';
                    mainContent.style.transform = 'translateX(0)';
                }
            });

            // Handle responsive design
            function handleResize() {
                if (window.innerWidth >= 1024) {
                    sidebar.style.transform = 'translateX(0)';
                    mainContent.style.marginLeft = '256px';
                    mainContent.style.transform = 'translateX(0)';
                    mobileMenuButton.style.display = 'none';
                } else {
                    sidebar.style.transform = 'translateX(-100%)';
                    mainContent.style.marginLeft = '0';
                    mobileMenuButton.style.display = 'block';
                    mobileMenuOpen = false;
                }
            }

            window.addEventListener('resize', handleResize);
            handleResize(); // Initial call

            // Add loading states to quick action buttons
            const quickActionButtons = document.querySelectorAll('.space-y-3 button');
            quickActionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const originalText = this.innerHTML;
                    this.innerHTML = `
                        <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    `;
                    this.disabled = true;

                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.disabled = false;

                        // Show success notification
                        showNotification('Aksi berhasil dilakukan!', 'success');
                    }, 2000);
                });
            });

            // Notification system
            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className =
                    `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full`;

                if (type === 'success') {
                    notification.classList.add('bg-green-500');
                } else if (type === 'error') {
                    notification.classList.add('bg-red-500');
                } else {
                    notification.classList.add('bg-blue-500');
                }

                notification.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>${message}</span>
                    </div>
                `;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                }, 100);

                setTimeout(() => {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 3000);
            }

            // Add hover effects to table rows
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.01)';
                    this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                    this.style.boxShadow = 'none';
                });
            });

            // Simulate dynamic data updates
            function updateStatistics() {
                const stats = [{
                    selector: '.text-2xl.font-bold.text-gray-900',
                    values: ['1,247', '1,248', '1,249']
                }, ];

                stats.forEach(stat => {
                    const elements = document.querySelectorAll(stat.selector);
                    elements.forEach((element, index) => {
                        if (index === 0) { // Only update first stat for demo
                            const currentValue = parseInt(element.textContent.replace(/[^\d]/g,
                                ''));
                            const newValue = currentValue + Math.floor(Math.random() * 3);
                            element.textContent = newValue.toLocaleString();
                        }
                    });
                });
            }

            // Update statistics every 30 seconds
            setInterval(updateStatistics, 30000);
        });
    </script>

    <style>
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mobile responsive adjustments */
        @media (max-width: 1024px) {
            .ml-64 {
                margin-left: 0 !important;
            }

            .fixed.inset-y-0.left-0 {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4 {
                grid-template-columns: 1fr;
            }

            .lg\\:col-span-2 {
                grid-column: span 1;
            }

            .px-6 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Loading animation */
        .loading-pulse {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Enhanced card shadows */
        .card-enhanced {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-enhanced:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-4px);
        }
    </style>
</body>

</html>
