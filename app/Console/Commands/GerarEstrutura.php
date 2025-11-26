<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class GerarEstrutura extends Command
{
    protected $signature = 'gerar:estrutura {name} {--migration= : Nome da migration existente}';
    protected $description = 'Gera toda a estrutura baseada em uma migration existente';

    private $entityName;
    private $studlyName;
    private $kebabName;
    private $snakeName;
    private $camelName;
    private $pluralName;
    private $tableName;
    private $migrationFields = [];

    public function handle()
    {
        $this->setupNames();

        if (!$this->findAndParseMigration()) {
            return Command::FAILURE;
        }

        $this->info("🚀 Gerando estrutura para: {$this->studlyName}");

        $this->generateModel();
        $this->generateController();
        $this->generateRepository();
        $this->generateService();
        $this->generateDTO();
        $this->generateExceptions();
        $this->generateFactory();
        $this->generateRoutes();
        $this->generateTests();

        $this->info("✅ Estrutura criada com sucesso!");

        return Command::SUCCESS;
    }

    private function setupNames(): void
    {
        $this->entityName = $this->argument('name');

        // Normalizar para singular (convenção Laravel para modelos)
        $singularName = Str::singular($this->entityName);

        // Gerar nomes baseados no singular
        $this->studlyName = Str::studly($singularName);
        $this->kebabName = Str::kebab($singularName);
        $this->snakeName = Str::snake($singularName);
        $this->camelName = Str::camel($singularName);

        // Plural apenas para tabela (convenção Laravel)
        $this->pluralName = Str::plural($this->snakeName);
        $this->tableName = $this->pluralName;
    }

    private function findAndParseMigration(): bool
    {
        $migrationName = $this->option('migration') ?: "create_{$this->tableName}_table";
        $migrationPath = $this->findMigrationFile($migrationName);

        if (!$migrationPath) {
            $this->error("Migration não encontrada: {$migrationName}");
            return false;
        }

        $this->info("Migration encontrada: " . basename($migrationPath));
        $this->parseMigrationFields($migrationPath);

        return true;
    }

    private function findMigrationFile(string $migrationName): ?string
    {
        $files = File::files(database_path('migrations'));

        foreach ($files as $file) {
            if (Str::contains($file->getFilename(), $migrationName)) {
                return $file->getPathname();
            }
        }

        return null;
    }

    private function parseMigrationFields(string $migrationPath): void
    {
        $content = File::get($migrationPath);

        // Extrair o nome real da tabela da migration
        if (preg_match('/Schema::create\([\'"]([^\'"]+)[\'"]/', $content, $tableMatch)) {
            $this->tableName = $tableMatch[1];
        }

        // Regex mais específica para capturar apenas definições de campos, incluindo foreignId
        preg_match_all('/\$table->(string|text|integer|bigInteger|unsignedBigInteger|foreignId|boolean|decimal|timestamp|date|time|json|id)\([\'"]([^\'"]+)[\'"]/', $content, $matches, PREG_SET_ORDER);

        $processedFields = [];

        foreach ($matches as $match) {
            $type = $match[1];
            $fieldName = $match[2];

            // Ignorar campos especiais, IDs automáticos e duplicados
            if (in_array($fieldName, ['created_at', 'updated_at']) || isset($processedFields[$fieldName])) {
                continue;
            }

            $processedFields[$fieldName] = true;
            $this->migrationFields[] = [
                'name' => $fieldName,
                'type' => $type
            ];
        }

        $this->info("Campos detectados: " . count($this->migrationFields));
    }

    private function generateFromStub(string $stubName, string $outputPath, array $replacements = []): void
    {
        $stubPath = base_path("stubs/{$stubName}.stub");

        if (!File::exists($stubPath)) {
            $this->warn("Stub não encontrada: {$stubName}");
            return;
        }

        $content = File::get($stubPath);

        $defaultReplacements = [
            '{{modelName}}' => $this->studlyName,
            '{{controllerName}}' => $this->studlyName . 'Controller',
            '{{serviceName}}' => $this->studlyName . 'Service',
            '{{repositoryName}}' => $this->studlyName . 'Repository',
            '{{dtoName}}' => $this->studlyName . 'DTO',
            '{{testName}}' => $this->studlyName . 'ServiceTest',
            '{{notFoundExceptionName}}' => $this->studlyName . 'NotFoundException',
            '{{exceptionName}}' => $this->studlyName . 'ValidationException',
            '{{variableName}}' => $this->snakeName,
            '{{pluralVariable}}' => $this->snakeName,
            '{{routeName}}' => $this->kebabName,
            '{{studlyName}}' => $this->studlyName,
            '{{kebabName}}' => $this->kebabName,
            '{{snakeName}}' => $this->snakeName,
            '{{pluralName}}' => $this->pluralName,
            '{{tableName}}' => $this->tableName,
        ];

        $allReplacements = array_merge($defaultReplacements, $replacements);

        $content = str_replace(array_keys($allReplacements), array_values($allReplacements), $content);

        File::ensureDirectoryExists(dirname($outputPath));
        File::put($outputPath, $content);

        $this->line("✅ " . basename($outputPath));
    }

    private function generateModel(): void
    {
        $fillableFields = array_map(fn($field) => "        '{$field['name']}'", $this->migrationFields);

        $this->generateFromStub('model', app_path("Models/{$this->studlyName}.php"), [
            '{{fillable}}' => implode(",\n", $fillableFields)
        ]);
    }

    private function generateController(): void
    {
        $validationRules = [];
        $updateValidationRules = [];

        foreach ($this->migrationFields as $field) {
            // Regras para criar (required)
            $rules = ['required'];
            if ($field['type'] === 'string')
                $rules[] = 'string|max:255';
            if ($field['type'] === 'text')
                $rules[] = 'string';
            if (in_array($field['type'], ['integer', 'bigInteger', 'foreignId', 'unsignedBigInteger']))
                $rules[] = 'integer|min:0';
            if ($field['type'] === 'decimal')
                $rules[] = 'numeric|min:0';
            if ($field['type'] === 'boolean')
                $rules[] = 'boolean';

            $validationRules[] = "            '{$field['name']}' => '" . implode('|', $rules) . "'";

            // Regras para atualizar (sometimes)
            $updateRules = ['sometimes'] + $rules;
            $updateValidationRules[] = "            '{$field['name']}' => '" . implode('|', $updateRules) . "'";
        }

        $this->generateFromStub('controller', app_path("Http/Controllers/{$this->studlyName}Controller.php"), [
            '{{validationRules}}' => implode(",\n", $validationRules),
            '{{updateValidationRules}}' => implode(",\n", $updateValidationRules)
        ]);
    }

    private function generateRepository(): void
    {
        $this->generateFromStub('repository', app_path("Repositories/{$this->studlyName}Repository.php"));
    }

    private function generateService(): void
    {
        $this->generateFromStub('service', app_path("Services/{$this->studlyName}Service.php"));
    }

    private function generateDTO(): void
    {
        $properties = [];
        $constructorParams = [];
        $assignments = [];
        $fromRequestMapping = [];
        $toArrayMapping = [];

        foreach ($this->migrationFields as $field) {
            $phpType = match ($field['type']) {
                'string', 'text' => 'string',
                'integer', 'bigInteger', 'foreignId', 'unsignedBigInteger' => 'int',
                'boolean' => 'bool',
                'decimal' => 'float',
                default => 'string'
            };

            $properties[] = "    public readonly {$phpType} \${$field['name']};";
            $constructorParams[] = "        {$phpType} \${$field['name']}";
            $assignments[] = "        \$this->{$field['name']} = \${$field['name']};";
            $fromRequestMapping[] = "            \$data['{$field['name']}']";
            $toArrayMapping[] = "            '{$field['name']}' => \$this->{$field['name']}";
        }

        $this->generateFromStub('dto', app_path("DTO/{$this->studlyName}DTO.php"), [
            '{{properties}}' => implode("\n", $properties),
            '{{constructor}}' => implode(",\n", $constructorParams),
            '{{assignProperties}}' => implode("\n", $assignments),
            '{{fromRequestMapping}}' => implode(",\n", $fromRequestMapping),
            '{{toArrayMapping}}' => implode(",\n", $toArrayMapping)
        ]);
    }

    private function generateExceptions(): void
    {
        $this->generateFromStub('not-found-exception', app_path("Exceptions/{$this->studlyName}NotFoundException.php"), [
            '{{exceptionName}}' => $this->studlyName . 'NotFoundException'
        ]);
        $this->generateFromStub('validation-exception', app_path("Exceptions/{$this->studlyName}ValidationException.php"), [
            '{{exceptionName}}' => $this->studlyName . 'ValidationException'
        ]);
    }

    private function generateFactory(): void
    {
        $fakeData = [];
        foreach ($this->migrationFields as $field) {
            $fake = match ($field['type']) {
                'string' => '$this->faker->words(3, true)',
                'text' => '$this->faker->paragraph()',
                'integer', 'foreignId', 'unsignedBigInteger' => '$this->faker->numberBetween(1, 10)',
                'bigInteger' => '$this->faker->numberBetween(1, 999999)',
                'decimal' => '$this->faker->randomFloat(2, 10, 1000)',
                'boolean' => '$this->faker->boolean(80)',
                default => '$this->faker->word()'
            };
            $fakeData[] = "            '{$field['name']}' => {$fake}";
        }

        $this->generateFromStub('factory', database_path("factories/{$this->studlyName}Factory.php"), [
            '{{factoryDefinition}}' => implode(",\n", $fakeData),
            '{{factoryName}}' => "{$this->studlyName}Factory"
        ]);
    }

    private function generateRoutes(): void
    {
        $this->generateFromStub('auth-route', base_path("routes/auth/{$this->camelName}.php"));
    }

    private function generateTests(): void
    {
        $testFields = [];
        $dtoParams = [];
        foreach ($this->migrationFields as $field) {
            $testValue = match ($field['type']) {
                'string' => "'Teste " . ucfirst($field['name']) . "'",
                'text' => "'Descrição de teste'",
                'integer' => '10',
                'decimal' => '99.99',
                'boolean' => 'true',
                default => "'valor_teste'"
            };
            $testFields[] = "            '{$field['name']}' => {$testValue}";
            $dtoParams[] = "            {$testValue}";
        }

        $this->generateFromStub('feature-test', base_path("tests/Feature/{$this->studlyName}CrudTest.php"), [
            '{{testFields}}' => implode(",\n", $testFields),
            '{{testName}}' => "{$this->studlyName}CrudTest"
        ]);
        $this->generateFromStub('unit-test', base_path("tests/Unit/Services/{$this->studlyName}ServiceTest.php"), [
            '{{dtoParams}}' => implode(",\n", $dtoParams)
        ]);
    }
}
