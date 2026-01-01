<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateAllDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:generate-all 
                            {--force : Force regeneration even if files exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate both web and mobile API documentation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force') ? ' --force' : '';

        $this->info('====> 📱 Generating Mobile API Documentation...');
        $this->call('scribe:generate', ['--config' => 'scribe_mobile'] + ($force ? ['--force' => true] : []));

        $this->info('====> 🌐 Generating Web API Documentation...');
        $this->call('scribe:generate' . $force);

        $this->info('✅ All documentation generated successfully!');
        $this->line('');
        $this->line('📖 Web API Documentation: ' . config('app.url') . '/docs/web');
        $this->line('📱 Mobile API Documentation: ' . config('app.url') . '/docs/mobile');
    }
}
