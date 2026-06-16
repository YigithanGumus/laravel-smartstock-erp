<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepository extends Command
{
    protected $signature = 'make:repository {name}';
    protected $description = 'Repository ve Interface dosyalarını oluşturur';

    public function handle(): void
    {
        $name = $this->argument('name');
        $repositoryName = "{$name}Repository";
        $basePath = app_path("Repositories/{$repositoryName}");

        File::makeDirectory("{$basePath}/Interfaces", 0755, true, true);
        File::put("{$basePath}/{$repositoryName}.php", $this->repositoryTemplate($name));
        File::put("{$basePath}/Interfaces/I{$repositoryName}.php", $this->interfaceTemplate($name));

        $this->updateServiceProvider($name);
        $this->updateProvidersFile(); // ← eklendi

        $this->info("✅ {$repositoryName} oluşturuldu.");
        $this->info("📁 Repositories/{$repositoryName}/{$repositoryName}.php");
        $this->info("📁 Repositories/{$repositoryName}/Interfaces/I{$repositoryName}.php");
        $this->info("🔗 RepositoryServiceProvider güncellendi.");
    }

    private function updateServiceProvider(string $name): void
    {
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');

        // Provider yoksa oluştur
        if (!File::exists($providerPath)) {
            File::put($providerPath, $this->providerTemplate());
        }

        $repositoryName = "{$name}Repository";
        $bind = "\t\t\$this->app->bind(I{$repositoryName}::class, {$repositoryName}::class);";
        $useRepository = "use App\\Repositories\\{$repositoryName}\\{$repositoryName};";
        $useInterface  = "use App\\Repositories\\{$repositoryName}\\Interfaces\\I{$repositoryName};";

        $content = File::get($providerPath);

        // Use ifadelerini ekle
        if (!str_contains($content, $useRepository)) {
            $content = str_replace(
                "use Illuminate\\Support\\ServiceProvider;",
                "use Illuminate\\Support\\ServiceProvider;\n{$useRepository}\n{$useInterface}",
                $content
            );
        }

        // Bind'ı ekle
        if (!str_contains($content, $bind)) {
            $content = str_replace(
                "// bindings",
                "// bindings\n{$bind}",
                $content
            );
        }

        File::put($providerPath, $content);
    }

    private function providerTemplate(): string
    {
        return <<<PHP
        <?php

        namespace App\Providers;

        use Illuminate\Support\ServiceProvider;

        class RepositoryServiceProvider extends ServiceProvider
        {
            public function register(): void
            {
                // bindings
            }
        }
        PHP;
    }

    private function repositoryTemplate(string $name): string
    {
        $repositoryName = "{$name}Repository";

        return <<<PHP
        <?php

        namespace App\Repositories\\{$repositoryName};

        use App\Models\\{$name};
        use App\Repositories\BaseRepository;
        use App\Repositories\\{$repositoryName}\Interfaces\I{$repositoryName};

        class {$repositoryName} extends BaseRepository implements I{$repositoryName}
        {
            public function __construct({$name} \$model)
            {
                parent::__construct(\$model);
            }

            // {$name}'a özel sorgular buraya
        }
        PHP;
    }

    private function interfaceTemplate(string $name): string
    {
        $repositoryName = "{$name}Repository";

        return <<<PHP
        <?php

        namespace App\Repositories\\{$repositoryName}\Interfaces;

        interface I{$repositoryName}
        {
            // {$name}'a özel metotlar buraya
        }
        PHP;
    }

    private function updateProvidersFile(): void
    {
        $providersPath = base_path('bootstrap/providers.php');
        $content = File::get($providersPath);
        $providerClass = "App\\Providers\\RepositoryServiceProvider::class,";

        if (!str_contains($content, $providerClass)) {
            $content = str_replace(
                "App\\Providers\\AppServiceProvider::class,",
                "App\\Providers\\AppServiceProvider::class,\n    {$providerClass}",
                $content
            );
            File::put($providersPath, $content);
            $this->info("🔗 RepositoryServiceProvider bootstrap/providers.php'ye eklendi.");
        }
    }
}
