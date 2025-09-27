<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Multi-Stack Installer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#6b7280',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    🚀 Laravel Multi-Stack Installer
                </h1>
                <p class="text-xl text-gray-600 mb-8">
                    Choose your frontend stack and get started with Laravel
                </p>
            </div>

            <!-- Installation Form -->
            <div class="bg-white shadow-xl rounded-lg p-8">
                <form id="installer-form" class="space-y-8">
                    <!-- Stack Selection -->
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Choose Your Stack</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($stacks as $stackKey => $stackInfo)
                            <div class="stack-option border-2 border-gray-200 rounded-lg p-6 cursor-pointer hover:border-primary transition-colors" data-stack="{{ $stackKey }}">
                                <div class="text-center">
                                    <div class="text-4xl mb-4">{{ $stackInfo['icon'] }}</div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $stackInfo['name'] }}</h3>
                                    <p class="text-gray-600 text-sm mb-4">{{ $stackInfo['description'] }}</p>
                                    <div class="text-xs text-gray-500">
                                        <div class="mb-2">
                                            <strong>Features:</strong>
                                        </div>
                                        <ul class="text-left space-y-1">
                                            @foreach($stackInfo['features'] as $feature)
                                            <li>• {{ ucwords(str_replace('_', ' ', $feature)) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Theme Selection -->
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Choose Your Theme</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($themes as $themeKey => $themeInfo)
                            <div class="theme-option border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-primary transition-colors" data-theme="{{ $themeKey }}">
                                <div class="text-center">
                                    <div class="w-16 h-16 mx-auto mb-3 rounded-lg" style="background: linear-gradient(45deg, {{ $themeInfo['colors']['primary'] }}, {{ $themeInfo['colors']['secondary'] }})"></div>
                                    <h4 class="font-semibold text-gray-900">{{ $themeInfo['name'] }}</h4>
                                    <p class="text-sm text-gray-600">{{ $themeInfo['description'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Installation Options -->
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Installation Options</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="install_composer" checked class="rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="text-sm text-gray-700">Install Composer Dependencies</span>
                            </label>
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="install_npm" checked class="rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="text-sm text-gray-700">Install NPM Dependencies</span>
                            </label>
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="run_migrations" checked class="rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="text-sm text-gray-700">Run Database Migrations</span>
                            </label>
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="seed_database" checked class="rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="text-sm text-gray-700">Seed Database</span>
                            </label>
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="setup_authentication" checked class="rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="text-sm text-gray-700">Setup Authentication</span>
                            </label>
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="create_admin_user" checked class="rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="text-sm text-gray-700">Create Admin User</span>
                            </label>
                        </div>
                    </div>

                    <!-- Install Button -->
                    <div class="text-center">
                        <button type="submit" id="install-btn" class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            Install Laravel Multi-Stack
                        </button>
                    </div>
                </form>
            </div>

            <!-- Progress Modal -->
            <div id="progress-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Installing...</h3>
                    <div class="mb-4">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div id="progress-bar" class="bg-primary h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <div id="progress-text" class="text-sm text-gray-600 mt-2">Starting installation...</div>
                    </div>
                    <div id="progress-logs" class="bg-gray-100 rounded p-4 max-h-40 overflow-y-auto text-sm">
                        <!-- Logs will appear here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedStack = null;
        let selectedTheme = null;

        // Stack selection
        document.querySelectorAll('.stack-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.stack-option').forEach(opt => opt.classList.remove('border-primary', 'bg-blue-50'));
                this.classList.add('border-primary', 'bg-blue-50');
                selectedStack = this.dataset.stack;
            });
        });

        // Theme selection
        document.querySelectorAll('.theme-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.theme-option').forEach(opt => opt.classList.remove('border-primary', 'bg-blue-50'));
                this.classList.add('border-primary', 'bg-blue-50');
                selectedTheme = this.dataset.theme;
            });
        });

        // Form submission
        document.getElementById('installer-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!selectedStack) {
                alert('Please select a stack');
                return;
            }

            if (!selectedTheme) {
                alert('Please select a theme');
                return;
            }

            const formData = new FormData(this);
            const options = {};
            
            for (let [key, value] of formData.entries()) {
                options[key] = value === 'on';
            }

            // Show progress modal
            document.getElementById('progress-modal').classList.remove('hidden');
            document.getElementById('progress-modal').classList.add('flex');

            try {
                // Start installation
                const response = await fetch('/multi-stack/installer/install', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        stack: selectedStack,
                        theme: selectedTheme,
                        options: options
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Poll for progress
                    pollProgress(result.installation_id);
                } else {
                    alert('Installation failed: ' + result.message);
                    hideProgressModal();
                }
            } catch (error) {
                alert('Installation failed: ' + error.message);
                hideProgressModal();
            }
        });

        async function pollProgress(installationId) {
            try {
                const response = await fetch(`/multi-stack/installer/progress/${installationId}`);
                const result = await response.json();

                if (result.success) {
                    const installation = result.installation;
                    
                    // Update progress bar
                    document.getElementById('progress-bar').style.width = installation.progress + '%';
                    document.getElementById('progress-text').textContent = installation.current_step || 'Processing...';
                    
                    // Update logs
                    const logsContainer = document.getElementById('progress-logs');
                    logsContainer.innerHTML = installation.logs.map(log => 
                        `<div class="text-xs text-gray-600">[${log.timestamp}] ${log.message}</div>`
                    ).join('');
                    logsContainer.scrollTop = logsContainer.scrollHeight;

                    if (installation.status === 'completed') {
                        // Installation completed
                        setTimeout(() => {
                            window.location.href = '/multi-stack/installer/complete';
                        }, 2000);
                    } else if (installation.status === 'running') {
                        // Continue polling
                        setTimeout(() => pollProgress(installationId), 1000);
                    }
                } else {
                    alert('Failed to get installation progress');
                    hideProgressModal();
                }
            } catch (error) {
                alert('Failed to get installation progress: ' + error.message);
                hideProgressModal();
            }
        }

        function hideProgressModal() {
            document.getElementById('progress-modal').classList.add('hidden');
            document.getElementById('progress-modal').classList.remove('flex');
        }
    </script>
</body>
</html>