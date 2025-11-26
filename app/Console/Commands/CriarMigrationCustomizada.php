<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CriarMigrationCustomizada extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'criar:migration-customizada {table : Nome da tabela (ex: produtos, usuarios)} {--campos= : Campos da tabela separados por vírgula (opcional)} {--schema= : Schema da tabela (padrão: public)} {--completa : Gera migration pronta para o comando gerar:estrutura}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria uma migration customizada seguindo o padrão do projeto';

    private string $tableName;
    private string $className;
    private string $fileName;
    private string $schema;
    private array $campos;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->setupNames();
        $this->setupCampos();

        if ($this->migrationExists()) {
            $this->error("❌ Migration para '{$this->tableName}' já existe!");
            return Command::FAILURE;
        }

        $this->info("🚀 Criando migration customizada para: {$this->tableName}");

        $migrationContent = $this->generateMigrationContent();
        $migrationPath = $this->getMigrationPath();

        File::put($migrationPath, $migrationContent);

        $this->info("✅ Migration criada: " . basename($migrationPath));
        $this->line("📂 Localização: " . $this->getRelativePath($migrationPath));

        if ($this->option('completa')) {
            $this->line("\n🚀 Migration pronta para o comando gerar:estrutura!");
            $this->line("   Execute: php artisan gerar:estrutura {$this->tableName}");
        } else if (empty($this->campos)) {
            $this->line("\n💡 Use --campos para definir campos automaticamente:");
            $this->line("   --campos=\"nome:string,email:string,idade:integer,ativo:boolean\"");
            $this->line("\n📋 Tipos de campo disponíveis:");
            $this->line("   string, text, integer, bigInteger, decimal, boolean");
            $this->line("   date, dateTime, time, json, uuid, ip, year, enum");
            $this->line("\n✏️  Modificadores:");
            $this->line("   :nullable - campo pode ser nulo");
            $this->line("   Exemplo: --campos=\"telefone:string:nullable,idade:integer\"");
            $this->line("\n🎯 Use --completa para gerar migration pronta para gerar:estrutura");
        }

        $this->line("\n🔧 Execute: php artisan migrate para aplicar a migration");

        return Command::SUCCESS;
    }

    private function setupNames(): void
    {
        $input = $this->argument('table');

        // Normalizar para singular primeiro
        $singularName = Str::singular($input);

        // A tabela deve ser plural (convenção Laravel)
        $this->tableName = Str::snake(Str::plural($singularName));

        // A classe da migration usa o plural também
        $this->className = 'Create' . Str::studly(Str::plural($singularName)) . 'Table';
        $this->schema = $this->option('schema') ?? 'public';

        $timestamp = Carbon::now()->format('Y_m_d_His');
        $this->fileName = "{$timestamp}_create_{$this->tableName}_table.php";
    }

    private function setupCampos(): void
    {
        $camposInput = $this->option('campos');
        $this->campos = [];

        if ($camposInput) {
            $camposList = explode(',', $camposInput);
            foreach ($camposList as $campo) {
                $parts = explode(':', trim($campo));
                if (count($parts) >= 2) {
                    $this->campos[] = [
                        'name' => trim($parts[0]),
                        'type' => trim($parts[1]),
                        'nullable' => isset($parts[2]) && trim($parts[2]) === 'nullable'
                    ];
                }
            }
        }
    }

    private function migrationExists(): bool
    {
        $migrationPath = database_path('migrations');
        $pattern = "*_create_{$this->tableName}_table.php";
        $existing = glob($migrationPath . '/' . $pattern);

        return !empty($existing);
    }

    private function generateMigrationContent(): string
    {
        $schemaCheck = $this->schema !== 'public' ? $this->generateSchemaCheck() : '';
        $tableCreation = $this->generateTableCreation();
        $tableDropping = $this->generateTableDropping();

        return "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;" . ($this->schema !== 'public' ? "\nuse Illuminate\Support\Facades\DB;" : "") . "

return new class extends Migration {
    public function up(): void
    {" . $schemaCheck . $tableCreation . "
    }

    public function down(): void
    {" . $tableDropping . "
    }
};
";
    }

    private function generateSchemaCheck(): string
    {
        return "
        // Criar o schema {$this->schema} se não existir
        DB::statement('CREATE SCHEMA IF NOT EXISTS {$this->schema}');
        ";
    }

    private function generateTableCreation(): string
    {
        $fullTableName = $this->schema !== 'public' ? "{$this->schema}.{$this->tableName}" : $this->tableName;
        $campos = $this->generateCamposDefinition();

        return "
        // Verificar se a tabela já existe antes de criar
        if (!Schema::hasTable('{$fullTableName}')) {
            Schema::create('{$fullTableName}', function (Blueprint \$table) {
                \$table->id();{$campos}
                \$table->timestamps();
            });
        }";
    }

    private function generateCamposDefinition(): string
    {
        if ($this->option('completa')) {
            return $this->generateCamposCompletos();
        }

        if (empty($this->campos)) {
            return "
                // Adicione seus campos aqui
                // \$table->string('nome');
                // \$table->text('descricao')->nullable();
                // \$table->integer('idade');
                // \$table->boolean('ativo')->default(true);";
        }

        $camposCode = '';
        foreach ($this->campos as $campo) {
            $line = "\n                \$table->" . $this->mapFieldType($campo['type']) . "('{$campo['name']}')";

            if ($campo['nullable']) {
                $line .= "->nullable()";
            }

            $line .= ";";
            $camposCode .= $line;
        }

        return $camposCode;
    }

    private function generateCamposCompletos(): string
    {
        // Gera uma estrutura padrão para entidades de negócio
        return "
                \$table->string('nome');
                \$table->text('descricao');
                \$table->decimal('preco', 8, 2);
                \$table->integer('estoque');
                \$table->boolean('ativo')->default(true);";
    }

    private function mapFieldType(string $type): string
    {
        return match (strtolower($type)) {
            'string', 'varchar' => 'string',
            'text', 'longtext' => 'text',
            'int', 'integer' => 'integer',
            'bigint', 'biginteger' => 'bigInteger',
            'decimal', 'float', 'double' => 'decimal',
            'bool', 'boolean' => 'boolean',
            'date' => 'date',
            'datetime', 'timestamp' => 'dateTime',
            'time' => 'time',
            'json' => 'json',
            'uuid' => 'uuid',
            'email' => 'string',
            'url' => 'string',
            'ip' => 'ipAddress',
            'mac' => 'macAddress',
            'year' => 'year',
            'enum' => 'enum',
            'set' => 'set',
            'binary' => 'binary',
            'geometry' => 'geometry',
            'point' => 'point',
            'linestring' => 'lineString',
            'polygon' => 'polygon',
            'geometrycollection' => 'geometryCollection',
            'multipoint' => 'multiPoint',
            'multilinestring' => 'multiLineString',
            'multipolygon' => 'multiPolygon',
            default => 'string'
        };
    }

    private function generateTableDropping(): string
    {
        $fullTableName = $this->schema !== 'public' ? "{$this->schema}.{$this->tableName}" : $this->tableName;

        if ($this->schema !== 'public') {
            return "
        Schema::dropIfExists('{$fullTableName}');
        // Verificar se o schema existe e está vazio antes de removê-lo
        \$tablesInSchema = DB::select(\"SELECT table_name FROM information_schema.tables WHERE table_schema = '{$this->schema}'\");
        if (empty(\$tablesInSchema)) {
            DB::statement('DROP SCHEMA IF EXISTS {$this->schema}');
        }";
        }

        return "
        Schema::dropIfExists('{$fullTableName}');";
    }

    private function getMigrationPath(): string
    {
        return database_path('migrations/' . $this->fileName);
    }

    private function getRelativePath(string $path): string
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
    }
}
