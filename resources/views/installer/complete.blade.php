<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Complete - Laravel Multi-Stack</title>
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
        <div class="max-w-2xl w-full space-y-8">
            <!-- Success Message -->
            <div class="text-center">
                <div class="text-6xl mb-6">🎉</div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    Installation Complete!
                </h1>
                <p class="text-xl text-gray-600 mb-8">
                    Your Laravel Multi-Stack application is ready to go
                </p>
            </div>

            <!-- Next Steps -->
            <div class="bg-white shadow-xl rounded-lg p-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Next Steps</h2>
                
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-green-600 font-semibold">1</span>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Start the Development Server</h3>
                            <p class="text-gray-600 text-sm">Run the following command in your terminal:</p>
                            <code class="block bg-gray-100 p-2 rounded mt-2 text-sm">php artisan serve</code>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold">2</span>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Start Frontend Build (if applicable)</h3>
                            <p class="text-gray-600 text-sm">If you're using Vue.js or React, run:</p>
                            <code class="block bg-gray-100 p-2 rounded mt-2 text-sm">npm run dev</code>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-purple-600 font-semibold">3</span>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Visit Your Application</h3>
                            <p class="text-gray-600 text-sm">Open your browser and go to:</p>
                            <a href="http://localhost:8000" class="block text-blue-600 hover:text-blue-800 mt-2" target="_blank">
                                http://localhost:8000
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <span class="text-yellow-600 font-semibold">4</span>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Admin Credentials</h3>
                            <p class="text-gray-600 text-sm">Use these credentials to log in:</p>
                            <div class="bg-gray-100 p-3 rounded mt-2">
                                <div class="text-sm">
                                    <div><strong>Email:</strong> admin@example.com</div>
                                    <div><strong>Password:</strong> password</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <a href="http://localhost:8000" 
                       class="bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center"
                       target="_blank">
                        Open Application
                    </a>
                    <a href="/multi-stack/installer" 
                       class="bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-700 transition-colors text-center">
                        Install Another Stack
                    </a>
                </div>
            </div>

            <!-- Documentation -->
            <div class="bg-white shadow-xl rounded-lg p-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Documentation & Resources</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Laravel Documentation</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li><a href="https://laravel.com/docs" class="text-blue-600 hover:text-blue-800" target="_blank">Laravel Official Docs</a></li>
                            <li><a href="https://laravel.com/docs/routing" class="text-blue-600 hover:text-blue-800" target="_blank">Routing</a></li>
                            <li><a href="https://laravel.com/docs/eloquent" class="text-blue-600 hover:text-blue-800" target="_blank">Eloquent ORM</a></li>
                            <li><a href="https://laravel.com/docs/blade" class="text-blue-600 hover:text-blue-800" target="_blank">Blade Templates</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Frontend Documentation</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li><a href="https://livewire.laravel.com" class="text-blue-600 hover:text-blue-800" target="_blank">Livewire</a></li>
                            <li><a href="https://vuejs.org" class="text-blue-600 hover:text-blue-800" target="_blank">Vue.js</a></li>
                            <li><a href="https://nextjs.org" class="text-blue-600 hover:text-blue-800" target="_blank">Next.js</a></li>
                            <li><a href="https://tailwindcss.com" class="text-blue-600 hover:text-blue-800" target="_blank">Tailwind CSS</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-gray-500 text-sm">
                <p>Laravel Multi-Stack Starter Kit - Built with ❤️</p>
                <p class="mt-2">
                    <a href="https://github.com/laravel-starter-kit/multi-stack" class="text-blue-600 hover:text-blue-800" target="_blank">
                        View on GitHub
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
