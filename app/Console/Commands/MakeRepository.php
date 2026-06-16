<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRepository extends Command
{
    protected $signature = 'make:repository {name} {--add= : Mevcut repository\'e yeni dosya ekle}';
    protected $description = 'Repository ve Interface dosyalarını oluşturur';

    public function handle(): void
    {
        $name = $this->argument('name');

        // Geçersiz karakter kontrolü
        if (!preg_match('/^[A-Za-z]+$/', $name)) {
            $this->error("❌ Geçersiz isim: '{$name}'. Sadece harf kullanın. Örnek: php artisan make:repository Product");
            return;
        }

        // Repository suffix varsa temizle
        $name = Str::replaceLast('Repository', '', $name);
        $repositoryName = "{$name}Repository";
        $basePath = app_path("Repositories/{$repositoryName}");

        // --add modu
        if ($add = $this->option('add')) {
            $this->addToRepository($basePath, $repositoryName, $add);
            return;
        }

        // Zaten var mı?
        if (File::exists($basePath)) {
            $this->error("❌ {$repositoryName} zaten mevcut.");
            $this->info("💡 Ekstra dosya eklemek için: php artisan make:repository {$name} --add=DosyaAdi");
            return;
        }

        // Klasörleri oluştur
        File::makeDirectory("{$basePath}/Interfaces", 0755, true, true);

        // Dosyaları oluştur
        File::put("{$basePath}/{$repositoryName}.php", $this->repositoryTemplate($name));
        File::put("{$basePath}/Interfaces/I{$repositoryName}.php", $this->interfaceTemplate($name));

        // Provider güncelle
        $this->updateServiceProvider($name);
        $this->updateProvidersFile();

        $this->info("✅ {$repositoryName} oluşturuldu.");
        $this->info("📁 Repositories/{$repositoryName}/{$repositoryName}.php");
        $this->info("📁 Repositories/{$repositoryName}/Interfaces/I{$repositoryName}.php");
        $this->info("🔗 RepositoryServiceProvider güncellendi.");
    }

    private function addToRepository(string $basePath, string $repositoryName, string $add): void
    {
        if (!File::exists($basePath)) {
            $this->error("❌ {$repositoryName} bulunamadı. Önce oluşturun: php artisan make:repository " . Str::replaceLast('Repository', '', $repositoryName));
            return;
        }

        $addName = Str::studly($add);
        $addRepositoryName = "{$addName}Repository";
        $filePath = "{$basePath}/{$addRepositoryName}.php";
        $interfacePath = "{$basePath}/Interfaces/I{$addRepositoryName}.php";

        if (File::exists($filePath)) {
            $this->error("❌ {$addRepositoryName}.php zaten mevcut.");
            return;
        }

        $namespace = "App\\Repositories\\{$repositoryName}";

        // Repository dosyası
        File::put($filePath, <<<PHP
        <?php

        namespace {$namespace};

        use App\Repositories\BaseRepository\BaseRepository;
        use {$namespace}\Interfaces\I{$addRepositoryName};

        class {$addRepositoryName} extends BaseRepository implements I{$addRepositoryName}
        {
            public function __construct()
            {
                parent::__construct(new \\App\\Models\\{$addName}());
            }

            // {$addName}'a özel sorgular buraya
        }
        PHP);

        // Interface dosyası
        File::put($interfacePath, <<<PHP
        <?php

        namespace {$namespace}\Interfaces;

        use App\Repositories\BaseRepository\Interfaces\IBaseRepository;

        interface I{$addRepositoryName} extends IBaseRepository
        {
            // {$addName}'a özel metotlar buraya
        }
        PHP);

        $this->info("✅ {$addRepositoryName} oluşturuldu.");
        $this->info("📁 Repositories/{$repositoryName}/{$addRepositoryName}.php");
        $this->info("📁 Repositories/{$repositoryName}/Interfaces/I{$addRepositoryName}.php");
    }

    private function updateServiceProvider(string $name): void
    {
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');

        if (!File::exists($providerPath)) {
            File::put($providerPath, $this->providerTemplate());
        }

        $repositoryName = "{$name}Repository";
        $bind = "\t\t\$this->app->bind(I{$repositoryName}::class, {$repositoryName}::class);";
        $useRepository = "use App\\Repositories\\{$repositoryName}\\{$repositoryName};";
        $useInterface  = "use App\\Repositories\\{$repositoryName}\\Interfaces\\I{$repositoryName};";

        $content = File::get($providerPath);

        if (!str_contains($content, $useRepository)) {
            $content = str_replace(
                "use Illuminate\\Support\\ServiceProvider;",
                "use Illuminate\\Support\\ServiceProvider;\n{$useRepository}\n{$useInterface}",
                $content
            );
        }

        if (!str_contains($content, $bind)) {
            $content = str_replace(
                "// bindings",
                "// bindings\n{$bind}",
                $content
            );
        }

        File::put($providerPath, $content);
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
        use App\Repositories\BaseRepository\BaseRepository;
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

        use App\Repositories\BaseRepository\Interfaces\IBaseRepository;

        interface I{$repositoryName} extends IBaseRepository
        {
            // {$name}'a özel metotlar buraya
        }
        PHP;
    }
}
