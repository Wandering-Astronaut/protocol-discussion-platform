<?php

namespace App\Console\Commands;

use App\Models\Protocol;
use App\Models\Thread;
use App\Services\TypesenseService;
use Illuminate\Console\Command;

class TypesenseReindex extends Command
{
    protected $signature = 'typesense:reindex
                            {--collection= : Specific collection to reindex (protocols|threads)}
                            {--fresh : Drop and recreate collections before indexing}';

    protected $description = 'Reindex all data into Typesense collections';

    public function __construct(protected TypesenseService $typesense)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $collection = $this->option('collection');
        $fresh      = $this->option('fresh');

        $this->info('🔍 Starting Typesense reindex...');

        if ($fresh) {
            $this->warn('⚠️  Dropping existing collections...');
            if (!$collection || $collection === 'protocols') {
                $this->typesense->deleteCollection('protocols');
            }
            if (!$collection || $collection === 'threads') {
                $this->typesense->deleteCollection('threads');
            }
        }

        $this->info('📦 Ensuring collections exist...');
        $this->typesense->ensureCollectionsExist();

        if (!$collection || $collection === 'protocols') {
            $this->reindexProtocols();
        }

        if (!$collection || $collection === 'threads') {
            $this->reindexThreads();
        }

        $this->info('✅ Reindex complete!');

        return Command::SUCCESS;
    }

    private function reindexProtocols(): void
    {
        $total = Protocol::published()->count();
        $this->info("📋 Indexing {$total} protocols...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $batch = [];
        Protocol::with('user')->published()->chunk(100, function ($protocols) use ($bar, &$batch) {
            foreach ($protocols as $protocol) {
                $batch[] = $protocol->toTypesenseDocument();
                $bar->advance();
            }
            $this->typesense->bulkImport('protocols', $batch);
            $batch = [];
        });

        $bar->finish();
        $this->newLine();
    }

    private function reindexThreads(): void
    {
        $total = Thread::count();
        $this->info("💬 Indexing {$total} threads...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $batch = [];
        Thread::with(['user', 'protocol'])->chunk(100, function ($threads) use ($bar, &$batch) {
            foreach ($threads as $thread) {
                $batch[] = $thread->toTypesenseDocument();
                $bar->advance();
            }
            $this->typesense->bulkImport('threads', $batch);
            $batch = [];
        });

        $bar->finish();
        $this->newLine();
    }
}
