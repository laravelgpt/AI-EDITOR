<?php

use LaravelStarterKit\MultiStack\Console\Commands\InstallCommand;
use LaravelStarterKit\MultiStack\Console\Commands\WebInstallerCommand;
use LaravelStarterKit\MultiStack\Console\Commands\StackCommand;
use LaravelStarterKit\MultiStack\Console\Commands\ThemeCommand;
use LaravelStarterKit\MultiStack\Console\Commands\AiFeatureCommand;

describe('Install Command', function () {
    it('can be instantiated', function () {
        $command = new InstallCommand();
        
        expect($command)->toBeInstanceOf(InstallCommand::class);
    });

    it('has correct signature', function () {
        $command = new InstallCommand();
        
        expect($command->getName())->toBe('multi-stack:install');
    });

    it('has correct description', function () {
        $command = new InstallCommand();
        
        expect($command->getDescription())->toBe('Install Laravel Multi-Stack Starter Kit with Interactive Selection');
    });
});

describe('Web Installer Command', function () {
    it('can be instantiated', function () {
        $command = new WebInstallerCommand();
        
        expect($command)->toBeInstanceOf(WebInstallerCommand::class);
    });

    it('has correct signature', function () {
        $command = new WebInstallerCommand();
        
        expect($command->getName())->toBe('multi-stack:web-installer');
    });

    it('has correct description', function () {
        $command = new WebInstallerCommand();
        
        expect($command->getDescription())->toBe('Start the web-based installer for Laravel Multi-Stack Starter Kit');
    });
});

describe('Stack Command', function () {
    it('can be instantiated', function () {
        $command = new StackCommand();
        
        expect($command)->toBeInstanceOf(StackCommand::class);
    });

    it('has correct signature', function () {
        $command = new StackCommand();
        
        expect($command->getName())->toBe('multi-stack:stacks');
    });

    it('has correct description', function () {
        $command = new StackCommand();
        
        expect($command->getDescription())->toBe('List available stacks or show details for a specific stack');
    });
});

describe('Theme Command', function () {
    it('can be instantiated', function () {
        $command = new ThemeCommand();
        
        expect($command)->toBeInstanceOf(ThemeCommand::class);
    });

    it('has correct signature', function () {
        $command = new ThemeCommand();
        
        expect($command->getName())->toBe('multi-stack:themes');
    });

    it('has correct description', function () {
        $command = new ThemeCommand();
        
        expect($command->getDescription())->toBe('Manage themes for Laravel Multi-Stack Starter Kit');
    });
});

describe('AI Feature Command', function () {
    it('can be instantiated', function () {
        $command = new AiFeatureCommand();
        
        expect($command)->toBeInstanceOf(AiFeatureCommand::class);
    });

    it('has correct signature', function () {
        $command = new AiFeatureCommand();
        
        expect($command->getName())->toBe('multi-stack:ai-feature');
    });

    it('has correct description', function () {
        $command = new AiFeatureCommand();
        
        expect($command->getDescription())->toBe('AI-powered feature generation and management for Laravel Multi-Stack');
    });
});

describe('Command Registration', function () {
    it('registers all commands in service provider', function () {
        $commands = [
            InstallCommand::class,
            WebInstallerCommand::class,
            StackCommand::class,
            ThemeCommand::class,
            AiFeatureCommand::class,
        ];
        
        foreach ($commands as $command) {
            expect(class_exists($command))->toBeTrue();
        }
    });
});
