# NPM Installation Guide for AI Text Editor

## 🚨 NPM Installation Issues

If you're experiencing NPM installation errors like:
```
npm error could not detect node name from path or package
```

## 🔧 Quick Fixes

### Option 1: Skip NPM Dependencies (Recommended)
```bash
php artisan ai-editor:install --no-npm
```

This will install the package without NPM dependencies, which is often sufficient for basic functionality.

### Option 2: Install Node.js and NPM
1. **Download Node.js**: Visit [nodejs.org](https://nodejs.org/) and download the LTS version
2. **Install Node.js**: Run the installer
3. **Verify Installation**:
   ```bash
   node --version
   npm --version
   ```
4. **Re-run Installation**:
   ```bash
   php artisan ai-editor:install
   ```

### Option 3: Manual NPM Installation
If the automatic installation fails, you can manually install NPM dependencies:

1. **Navigate to your project directory**
2. **Create package.json** (if it doesn't exist):
   ```bash
   npm init -y
   ```
3. **Install dependencies manually**:
   ```bash
   npm install @tailwindcss/forms
   # or for other stacks:
   npm install vue@^3.0 vue-router@^4.0 pinia@^2.0 axios
   npm install react@^18.0 react-dom@^18.0 next@^14.0
   ```

## 🎯 Stack-Specific NPM Dependencies

### Laravel Default Stack
```bash
npm install @tailwindcss/forms
```

### Livewire Stack
```bash
npm install @tailwindcss/forms alpinejs
```

### Vue.js Stack
```bash
npm install vue@^3.0 vue-router@^4.0 pinia@^2.0 axios @vitejs/plugin-vue
```

### React + Next.js Stack
```bash
npm install react@^18.0 react-dom@^18.0 next@^14.0 @types/react @types/react-dom
```

## 🚀 Alternative: Use --no-npm Flag

The easiest solution is to skip NPM dependencies entirely:

```bash
php artisan ai-editor:install --no-npm
```

This will:
- ✅ Install all Composer dependencies
- ✅ Set up authentication
- ✅ Run migrations
- ✅ Create admin user
- ✅ Apply theme
- ❌ Skip NPM dependencies (can be installed later)

## 🔍 Troubleshooting

### Common NPM Errors:

1. **"could not detect node name"**: Node.js not properly installed
2. **"npm command not found"**: NPM not in PATH
3. **"permission denied"**: Run as administrator (Windows) or use sudo (Linux/Mac)

### Windows Users:
- Install Node.js from [nodejs.org](https://nodejs.org/)
- Restart your terminal/command prompt
- Try running as administrator

### Linux/Mac Users:
```bash
# Install Node.js via package manager
# Ubuntu/Debian:
sudo apt install nodejs npm

# macOS (with Homebrew):
brew install node

# Or use Node Version Manager (nvm):
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
nvm install node
```

## 📝 Manual Installation Steps

If all else fails, you can manually complete the installation:

1. **Install the package**:
   ```bash
   composer require ai-editor/ai-text-editor
   php artisan ai-editor:post-install
   ```

2. **Skip NPM dependencies**:
   ```bash
   php artisan ai-editor:install --no-npm
   ```

3. **Install NPM dependencies later** (when needed):
   ```bash
   npm install
   npm run dev
   ```

## 🎉 Success!

Once installed, you can:
- Access your AI Text Editor at `/ai-editor`
- Customize themes and features
- Add NPM dependencies later when needed
- Use the package without frontend build tools

The package works perfectly without NPM dependencies for basic functionality!
