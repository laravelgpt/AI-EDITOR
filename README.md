# Laravel AI-Powered Text Editor

A comprehensive Laravel 12+ package that provides **AI-powered text editing** capabilities with **multi-stack support** for modern web development. Choose between **Blade+Livewire**, **Vue.js SPA**, or **React+Next.js** with intelligent content generation and editing features.

## 🚀 Features

### 🤖 **AI-Powered Features**
- **AI Text Generation**: Generate content with OpenAI, Anthropic, Google
- **Smart Editing**: AI-powered text editing and rewriting
- **Content Summarization**: Automatic content summarization
- **Text Completion**: Intelligent text completion
- **Memory System**: Version control and content history
- **Multi-Provider Support**: OpenAI, Anthropic, Google, HuggingFace, OpenRouter

### 🎯 **Interactive Installation**
- **CLI Installer**: `php artisan ai-editor:install`
- **Web Installer**: `php artisan ai-editor:web-installer`
- **Stack Selection**: Choose your preferred frontend stack
- **Theme Selection**: Multiple built-in themes
- **Dependency Management**: Auto-install Composer and NPM packages

### 📦 **Three Frontend Stacks**

#### **1. Blade + Livewire** ⚡
- **Real-time updates** with Livewire
- **Server-side rendering** with Blade
- **Alpine.js** for interactivity
- **Tailwind CSS** for styling
- **Component-based** architecture

#### **2. Vue.js SPA** 💚
- **Vue 3** with Composition API
- **Vue Router** for navigation
- **Pinia** for state management
- **Axios** for API calls
- **Vite** for fast development

#### **3. React + Next.js** ⚛️
- **Next.js 14** with App Router
- **React 18** with hooks
- **TypeScript** support
- **SSR** and static generation
- **API routes** integration

### 🎨 **Theme System**
- **Default**: Clean and modern
- **Dark**: Dark theme with modern aesthetics
- **Minimal**: Minimalist design
- **Colorful**: Vibrant theme with multiple colors
- **Custom**: Create your own themes

### 🔧 **Built-in Features**
- **Authentication**: Laravel Breeze integration
- **User Management**: Roles and permissions
- **Dashboard**: Analytics and quick actions
- **API Endpoints**: RESTful APIs for all stacks
- **Security**: CSRF, rate limiting, validation
- **Performance**: Caching, optimization

## 📦 Installation

### 1. Install via Composer

```bash
composer require ai-editor/ai-text-editor
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --provider="LaravelStarterKit\MultiStack\MultiStackServiceProvider" --tag="config"
```

### 3. Run Installation

#### CLI Installer (Recommended)
```bash
php artisan ai-editor:install
```

#### Web Installer
```bash
php artisan ai-editor:web-installer
# Visit http://localhost:8001/multi-stack/installer
```

## 🎯 Quick Start

### Basic Installation

```bash
# Install with default settings
php artisan ai-editor:install

# Choose your stack:
# 1 → Blade + Livewire
# 2 → Vue.js SPA  
# 3 → React + Next.js
```

### Advanced Installation

```bash
# Install specific stack
php artisan ai-editor:install --stack=vue-spa --theme=dark

# Skip dependencies
php artisan ai-editor:install --no-deps

# Skip migrations
php artisan ai-editor:install --no-migrate
```

### Web Installer

```bash
# Start web installer
php artisan ai-editor:web-installer

# Visit http://localhost:8001/multi-stack/installer
# Select stack, theme, and options
# Click "Install Laravel Multi-Stack"
```

## 🏗️ Architecture

### Core Services
- **InstallerService**: Handles installation process
- **StackService**: Manages frontend stacks
- **ThemeService**: Handles theme management

### Commands
- **InstallCommand**: CLI installer
- **WebInstallerCommand**: Web installer server
- **StackCommand**: Stack management
- **ThemeCommand**: Theme management

### Controllers
- **InstallerController**: Web installer interface
- **StackController**: Stack management API
- **ThemeController**: Theme management API

## 🔧 Configuration

### Environment Variables

```env
# Multi-Stack Settings
MULTI_STACK_DEBUG=false
MULTI_STACK_AUTO_INSTALL=true
MULTI_STACK_INSTALL_NPM=true
MULTI_STACK_INSTALL_COMPOSER=true
MULTI_STACK_RUN_MIGRATIONS=true
MULTI_STACK_SEED_DATABASE=true
MULTI_STACK_SETUP_AUTH=true
MULTI_STACK_CREATE_ADMIN=true
MULTI_STACK_SETUP_THEME=true
```

### Configuration File

```php
// config/multi-stack.php
return [
    'stacks' => [
        'blade-livewire' => [
            'name' => 'Blade + Livewire',
            'description' => 'Traditional Laravel with Blade templates and Livewire',
            'dependencies' => [
                'composer' => ['livewire/livewire', 'spatie/laravel-permission'],
                'npm' => ['alpinejs', 'tailwindcss', '@tailwindcss/forms']
            ]
        ],
        // ... other stacks
    ],
    'themes' => [
        'default' => [
            'name' => 'Default',
            'colors' => [
                'primary' => '#3b82f6',
                'secondary' => '#6b7280',
                // ... more colors
            ]
        ],
        // ... other themes
    ]
];
```

## 🎨 Frontend Stacks

### Blade + Livewire
```bash
php artisan ai-editor:install --stack=blade-livewire
```

**Features:**
- Real-time updates with Livewire
- Server-side rendering
- Blade components
- Alpine.js integration
- Tailwind CSS

**Dependencies:**
- `livewire/livewire`
- `spatie/laravel-permission`
- `alpinejs`
- `tailwindcss`

### Vue.js SPA
```bash
php artisan ai-editor:install --stack=vue-spa
```

**Features:**
- Vue 3 with Composition API
- Vue Router for navigation
- Pinia for state management
- Axios for API calls
- Vite for fast development

**Dependencies:**
- `laravel/sanctum`
- `vue@3`
- `vue-router@4`
- `pinia`
- `axios`

### React + Next.js
```bash
php artisan ai-editor:install --stack=react-nextjs
```

**Features:**
- Next.js 14 with App Router
- React 18 with hooks
- TypeScript support
- SSR and static generation
- API routes

**Dependencies:**
- `laravel/sanctum`
- `next@14`
- `react@18`
- `typescript`

## 🎨 Theme System

### Available Themes

```bash
# List all themes
php artisan ai-editor:themes --list

# Apply theme
php artisan ai-editor:themes --apply=dark

# Show theme details
php artisan ai-editor:themes dark
```

### Custom Themes

```php
// config/multi-stack.php
'themes' => [
    'custom' => [
        'name' => 'Custom Theme',
        'description' => 'Your custom theme',
        'colors' => [
            'primary' => '#your-color',
            'secondary' => '#your-color',
            // ... more colors
        ]
    ]
]
```

## 🔧 Commands

### Installation Commands
```bash
# CLI installer
php artisan ai-editor:install

# Web installer
php artisan ai-editor:web-installer

# With options
php artisan ai-editor:install --stack=vue-spa --theme=dark --no-deps
```

### Stack Management
```bash
# List available stacks
php artisan ai-editor:stacks

# Show stack details
php artisan ai-editor:stacks vue-spa

# Switch stack (future feature)
php artisan ai-editor:stacks --switch=react-nextjs
```

### Theme Management
```bash
# List themes
php artisan ai-editor:themes --list

# Apply theme
php artisan ai-editor:themes --apply=dark

# Show theme details
php artisan ai-editor:themes dark
```

## 🌐 Web Installer

### Starting the Web Installer

```bash
php artisan ai-editor:web-installer
# Visit http://localhost:8001/multi-stack/installer
```

### Web Installer Features
- **Interactive UI**: Beautiful web interface
- **Stack Selection**: Visual stack comparison
- **Theme Preview**: Live theme previews
- **Progress Tracking**: Real-time installation progress
- **Options Configuration**: Customize installation options

### Web Installer Routes
- `/multi-stack/installer` - Main installer page
- `/multi-stack/installer/select-stack` - Stack selection API
- `/multi-stack/installer/install` - Installation API
- `/multi-stack/installer/progress/{id}` - Progress tracking
- `/multi-stack/installer/complete` - Installation complete

## 🔒 Security

### Built-in Security Features
- **CSRF Protection**: All forms and API endpoints
- **Rate Limiting**: Configurable limits for API calls
- **Authentication**: Laravel Breeze integration
- **Authorization**: Role-based permissions
- **Input Validation**: Sanitize all user inputs
- **XSS Protection**: Built-in XSS prevention

### Security Configuration
```php
'security' => [
    'csrf_protection' => true,
    'rate_limiting' => [
        'enabled' => true,
        'max_attempts' => 60,
        'decay_minutes' => 1,
    ],
    'input_validation' => true,
    'xss_protection' => true,
],
```

## 🚀 Performance

### Optimization Features
- **Asset Optimization**: Minified CSS/JS
- **Caching**: File-based caching
- **Database Optimization**: Efficient queries
- **Queue Processing**: Background jobs
- **CDN Support**: Static asset delivery

### Performance Configuration
```php
'performance' => [
    'caching' => [
        'enabled' => true,
        'driver' => 'file',
        'ttl' => 3600,
    ],
    'asset_optimization' => true,
    'database_optimization' => true,
],
```

## 📚 API Documentation

### Stack Management API
```bash
# Get available stacks
GET /multi-stack/stacks

# Switch stack
POST /multi-stack/stacks/switch
{
    "stack": "vue-spa"
}

# Get stack status
GET /multi-stack/stacks/status
```

### Theme Management API
```bash
# Get available themes
GET /multi-stack/themes

# Apply theme
POST /multi-stack/themes/apply
{
    "theme": "dark"
}

# Preview theme
GET /multi-stack/themes/preview/dark
```

### Installation API
```bash
# Start installation
POST /multi-stack/installer/install
{
    "stack": "vue-spa",
    "theme": "dark",
    "options": {
        "install_composer": true,
        "install_npm": true,
        "run_migrations": true
    }
}

# Get installation progress
GET /multi-stack/installer/progress/{id}
```

## 🧪 Testing

```bash
# Run tests
php artisan test

# Run stack tests
php artisan test --filter=StackTest

# Run installer tests
php artisan test --filter=InstallerTest
```

## 📈 Monitoring

### Built-in Analytics
- Installation statistics
- Stack usage metrics
- Performance monitoring
- Error tracking
- User activity logs

### Custom Metrics
```php
// Track stack usage
$stackService->trackUsage($stack);

// Track installation metrics
$installerService->trackInstallation($stack, $theme);
```

## 🔄 Updates & Maintenance

### Stack Updates
```bash
# Update all stacks
php artisan ai-editor:stacks update-all

# Update specific stack
php artisan ai-editor:stacks update vue-spa
```

### Theme Updates
```bash
# Update all themes
php artisan ai-editor:themes update-all

# Update specific theme
php artisan ai-editor:themes update dark
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## 🆘 Support

- **Documentation**: [Package Documentation](https://github.com/ai-editor/ai-text-editor)
- **Issues**: [GitHub Issues](https://github.com/ai-editor/ai-text-editor/issues)
- **Discussions**: [GitHub Discussions](https://github.com/ai-editor/ai-text-editor/discussions)

## 🎉 What's Included

### Complete Stack Scaffolding
- **Models**: User, Role, Permission models
- **Controllers**: API controllers for all stacks
- **Views**: Blade templates for Blade+Livewire
- **Components**: Vue/React components
- **Routes**: Web and API routes
- **Migrations**: Database migrations
- **Assets**: CSS, JavaScript, images

### Authentication System
- **Laravel Breeze**: Complete auth scaffolding
- **User Management**: Registration, login, logout
- **Password Reset**: Email-based password reset
- **Email Verification**: Email verification system
- **Role Management**: Spatie Laravel Permission

### Dashboard & UI
- **Dashboard**: Analytics and quick actions
- **User Profile**: Profile management
- **Settings**: Application settings
- **Navigation**: Responsive navigation
- **Components**: Reusable UI components

### API Integration
- **RESTful APIs**: Complete API endpoints
- **Authentication**: Sanctum API authentication
- **Rate Limiting**: API rate limiting
- **CORS**: Cross-origin resource sharing
- **Validation**: Request validation

---

**🚀 Ready to build amazing Laravel applications with multiple frontend stacks? Get started today!**