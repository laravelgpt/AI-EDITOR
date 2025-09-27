<?php

namespace LaravelStarterKit\MultiStack\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use LaravelStarterKit\MultiStack\Services\ThemeService;

class ThemeController
{
    public function __construct(
        protected ThemeService $themeService
    ) {}

    public function index(): JsonResponse
    {
        $themes = $this->themeService->getAvailableThemes();
        $themeDetails = [];

        foreach ($themes as $theme) {
            $themeDetails[$theme] = $this->themeService->getThemeInfo($theme);
        }

        return response()->json([
            'success' => true,
            'themes' => $themeDetails,
            'current_theme' => $this->themeService->getCurrentTheme()
        ]);
    }

    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'theme' => 'required|string|in:' . implode(',', $this->themeService->getAvailableThemes())
        ]);

        $theme = $request->input('theme');

        try {
            $this->themeService->applyTheme($theme);
            $this->themeService->setCurrentTheme($theme);

            return response()->json([
                'success' => true,
                'message' => "Theme '{$theme}' applied successfully",
                'theme' => $theme,
                'theme_info' => $this->themeService->getThemeInfo($theme)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Failed to apply theme: {$e->getMessage()}"
            ], 500);
        }
    }

    public function preview(string $theme): JsonResponse
    {
        try {
            $preview = $this->themeService->getThemePreview($theme);

            return response()->json([
                'success' => true,
                'preview' => $preview
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Failed to get theme preview: {$e->getMessage()}"
            ], 500);
        }
    }
}
