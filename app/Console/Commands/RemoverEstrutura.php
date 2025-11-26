<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RemoverEstrutura extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remover:estrutura {name : Nome da entidade (ex: produtos)} {--with-migration : Remover também a migration} {--force : Não solicitar confirmação para remoção da migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove toda a estrutura gerada para uma entidade (modelo, controlador, testes, etc.)';

    private string $entityName;
    private string $studlyName;
    private string $snakeName;
    private string $kebabName;
    private string $camelName;
    private string $pluralName;
    private string $tableName;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->setupNames();

        // Lista de arquivos para remover
        $filesToRemove = $this->getFilesToRemove();

        // Verificar quais arquivos existem
        $existingFiles = array_filter($filesToRemove, fn($file) => File::exists($file));

        if (empty($existingFiles)) {
            $this->warn("⚠️  Nenhum arquivo encontrado para a entidade '{$this->studlyName}'.");
            $this->line("💡 Verifique se a estrutura foi gerada corretamente ou se o nome está correto.");
            return Command::FAILURE;
        }

        // Mostrar arquivos que serão removidos
        $this->info("📋 Arquivos que serão removidos:");
        foreach ($existingFiles as $file) {
            $this->line("  - " . $this->getRelativePath($file));
        }

        if ($this->option('with-migration')) {
            $migrationPath = database_path('migrations');
            $pattern = "*_create_{$this->tableName}_table.php";
            $migrations = glob($migrationPath . '/' . $pattern);
            if (!empty($migrations)) {
                $this->line("  - " . $this->getRelativePath($migrations[0]) . " (migration)");
            }
        }

        $this->newLine();

        // Verificação de segurança
        if (!$this->option('force') && !$this->confirm("⚠️  Tem certeza que deseja remover TODA a estrutura para '{$this->studlyName}'?")) {
            $this->info("❌ Operação cancelada.");
            return Command::FAILURE;
        }

        $this->info("🗑️  Removendo estrutura para: {$this->studlyName}");

        $removedFiles = [];
        $notFoundFiles = [];

        // Remover arquivos
        foreach ($filesToRemove as $file) {
            if (File::exists($file)) {
                File::delete($file);
                $removedFiles[] = $file;
                $this->line("🗑️  " . $this->getRelativePath($file));
            } else {
                $notFoundFiles[] = $file;
            }
        }

        // Remover migration se solicitado
        if ($this->option('with-migration')) {
            $migrationRemoved = $this->removeMigration();
            if ($migrationRemoved) {
                $removedFiles[] = $migrationRemoved;
            }
        }

        // Remover diretórios vazios
        $this->removeEmptyDirectories();

        // Exibir resultado
        $this->displayResults($removedFiles, $notFoundFiles);

        return Command::SUCCESS;
    }

    private function setupNames(): void
    {
        $this->entityName = $this->argument('name');

        // Normalizar para singular (mesma lógica do gerar:estrutura)
        $singularName = Str::singular($this->entityName);

        // Gerar nomes baseados no singular
        $this->studlyName = Str::studly($singularName);
        $this->kebabName = Str::kebab($singularName);
        $this->snakeName = Str::snake($singularName);
        $this->camelName = Str::camel($singularName);

        // Plural apenas para tabela
        $this->pluralName = Str::plural($this->snakeName);
        $this->tableName = $this->pluralName;
    }

    private function getFilesToRemove(): array
    {
        return [
            // Models
            app_path("Models/{$this->studlyName}.php"),

            // Controllers
            app_path("Http/Controllers/{$this->studlyName}Controller.php"),

            // Services e Repositories
            app_path("Services/{$this->studlyName}Service.php"),
            app_path("Repositories/{$this->studlyName}Repository.php"),

            // DTOs
            app_path("DTO/{$this->studlyName}DTO.php"),

            // Exceptions
            app_path("Exceptions/{$this->studlyName}NotFoundException.php"),
            app_path("Exceptions/{$this->studlyName}ValidationException.php"),

            // Factories
            database_path("factories/{$this->studlyName}Factory.php"),

            // Testes
            base_path("tests/Feature/{$this->studlyName}CrudTest.php"),
            base_path("tests/Unit/Services/{$this->studlyName}ServiceTest.php"),

            // Rotas
            base_path("routes/auth/{$this->camelName}.php"),
        ];
    }

    private function removeMigration(): ?string
    {
        $migrationPath = database_path('migrations');
        $pattern = "*_create_{$this->tableName}_table.php";

        $migrations = glob($migrationPath . '/' . $pattern);

        if (!empty($migrations)) {
            $migrationFile = $migrations[0];

            if ($this->option('force') || $this->confirm("⚠️  Tem certeza que deseja remover a migration: " . basename($migrationFile) . "?")) {

                // Verificar se a migration foi executada
                $migrationName = basename($migrationFile, '.php');
                if ($this->migrationWasRun($migrationName)) {
                    $this->info("🔄 Fazendo rollback da migration...");

                    // Fazer rollback da migration específica
                    $rollbackResult = $this->call('migrate:rollback', [
                        '--path' => 'database/migrations/' . basename($migrationFile),
                        '--force' => true
                    ]);

                    if ($rollbackResult === 0) {
                        $this->info("✅ Rollback realizado com sucesso");
                    } else {
                        $this->warn("⚠️  Não foi possível fazer rollback automático. Verifique manualmente.");
                    }
                } else {
                    $this->info("ℹ️  Migration não foi executada, não é necessário rollback");
                }

                // Remover o arquivo da migration
                File::delete($migrationFile);
                $this->line("🗑️  " . $this->getRelativePath($migrationFile));
                return $migrationFile;
            } else {
                $this->line("⏭️  Migration mantida: " . basename($migrationFile));
            }
        } else {
            $this->warn("⚠️  Migration não encontrada para: {$this->tableName}");
        }

        return null;
    }

    private function removeEmptyDirectories(): void
    {
        $directories = [
            app_path('DTO'),
            app_path('Exceptions'),
            app_path('Services'),
            app_path('Repositories'),
            database_path('factories'),
            base_path('tests/Unit/Services'),
            base_path('routes/auth'),
            base_path('routes/guest'),
        ];

        foreach ($directories as $dir) {
            if (File::isDirectory($dir) && $this->isDirectoryEmpty($dir)) {
                File::deleteDirectory($dir);
                $this->line("📁 Diretório vazio removido: " . $this->getRelativePath($dir));
            }
        }
    }

    private function isDirectoryEmpty(string $directory): bool
    {
        $files = array_diff(scandir($directory), ['.', '..']);
        return count($files) === 0;
    }

    private function migrationWasRun(string $migrationName): bool
    {
        try {
            // Verificar se a migration existe na tabela migrations
            $result = \DB::table('migrations')
                ->where('migration', $migrationName)
                ->exists();

            return $result;
        } catch (\Exception $e) {
            // Se der erro (ex: tabela migrations não existe), assumir que não foi executada
            return false;
        }
    }

    private function getRelativePath(string $path): string
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
    }

    private function displayResults(array $removedFiles, array $notFoundFiles): void
    {
        if (!empty($removedFiles)) {
            $this->info("\n✅ Arquivos removidos com sucesso: " . count($removedFiles));
        }

        if (!empty($notFoundFiles)) {
            $this->warn("\n⚠️  Arquivos não encontrados: " . count($notFoundFiles));

            if ($this->output->isVerbose()) {
                $this->line("\nArquivos não encontrados:");
                foreach ($notFoundFiles as $file) {
                    $this->line("  - " . $this->getRelativePath($file));
                }
            }
        }

        if (empty($removedFiles)) {
            $this->warn("⚠️  Nenhum arquivo foi removido. A estrutura para '{$this->studlyName}' pode não existir.");
        } else {
            $this->info("\n🎉 Estrutura para '{$this->studlyName}' removida com sucesso!");

            if (!$this->option('with-migration')) {
                $this->line("\n💡 Use --with-migration para remover também a migration.");
            }
        }
    }
}
