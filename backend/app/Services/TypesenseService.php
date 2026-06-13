<?php

namespace App\Services;

use Typesense\Client as TypesenseClient;
use Illuminate\Support\Facades\Log;

class TypesenseService
{
    protected TypesenseClient $client;

    public function __construct()
    {
        $this->client = new TypesenseClient([
            'api_key' => config('typesense.api_key'),
            'nodes'   => config('typesense.nodes'),
            'connection_timeout_seconds' => config('typesense.connection_timeout_seconds', 2),
        ]);
    }

    // ─── Collection Management ────────────────────────────────

    public function ensureCollectionsExist(): void
    {
        foreach (config('typesense.collections') as $key => $schema) {
            try {
                $this->client->collections[$schema['name']]->retrieve();
            } catch (\Exception $e) {
                $this->client->collections->create($schema);
                Log::info("Typesense: created collection '{$schema['name']}'");
            }
        }
    }

    public function deleteCollection(string $name): void
    {
        try {
            $this->client->collections[$name]->delete();
        } catch (\Exception $e) {
            Log::warning("Typesense: could not delete collection '{$name}': " . $e->getMessage());
        }
    }

    // ─── Document Operations ──────────────────────────────────

    public function upsertDocument(string $collection, array $document): void
    {
        try {
            $this->client->collections[$collection]->documents->upsert($document);
        } catch (\Exception $e) {
            Log::error("Typesense upsert failed for {$collection}: " . $e->getMessage());
        }
    }

    public function deleteDocument(string $collection, string $id): void
    {
        try {
            $this->client->collections[$collection]->documents[$id]->delete();
        } catch (\Exception $e) {
            Log::warning("Typesense delete failed for {$collection}#{$id}: " . $e->getMessage());
        }
    }

    // ─── Search ───────────────────────────────────────────────

    public function search(string $collection, array $params): array
    {
        try {
            $result = $this->client->collections[$collection]->documents->search($params);
            return $result;
        } catch (\Exception $e) {
            Log::error("Typesense search failed for {$collection}: " . $e->getMessage());
            return ['hits' => [], 'found' => 0];
        }
    }

    public function multiSearch(array $searches): array
    {
        try {
            return $this->client->multiSearch->perform(
                ['searches' => $searches],
                ['query_by' => ''] // per-search query_by overrides this
            );
        } catch (\Exception $e) {
            Log::error("Typesense multi-search failed: " . $e->getMessage());
            return ['results' => []];
        }
    }

    // ─── Bulk Import ──────────────────────────────────────────

    public function bulkImport(string $collection, array $documents): void
    {
        if (empty($documents)) {
            return;
        }
        try {
            $this->client->collections[$collection]->documents->import($documents, ['action' => 'upsert']);
        } catch (\Exception $e) {
            Log::error("Typesense bulk import failed for {$collection}: " . $e->getMessage());
        }
    }

    public function getClient(): TypesenseClient
    {
        return $this->client;
    }
}
