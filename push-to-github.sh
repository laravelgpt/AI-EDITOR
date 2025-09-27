#!/bin/bash

echo "🚀 Pushing Laravel Multi-Stack Starter Kit to GitHub..."
echo

echo "Setting up Git configuration..."
git config --global user.name "Laravel Multi-Stack"
git config --global user.email "laravelgpt@example.com"

echo
echo "Attempting to push to GitHub..."
echo "You may be prompted for GitHub credentials."
echo

git push origin main

if [ $? -eq 0 ]; then
    echo
    echo "✅ Successfully pushed to GitHub!"
    echo "Repository: https://github.com/laravelgpt/AI-EDITOR"
    echo "Tag: v1.0.0"
    echo
else
    echo
    echo "❌ Push failed. Please check your GitHub credentials."
    echo
    echo "Manual steps:"
    echo "1. Go to https://github.com/laravelgpt/AI-EDITOR"
    echo "2. Make sure you have write access to the repository"
    echo "3. Try: git push origin main"
    echo
fi
