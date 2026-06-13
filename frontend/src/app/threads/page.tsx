'use client';
export const dynamic = 'force-dynamic';
import { useEffect, useState, useCallback } from 'react';
import { useSearchParams } from 'next/navigation';
import { threadsApi, Thread } from '@/lib/api';
import ThreadCard from '@/components/threads/ThreadCard';
import SearchBar from '@/components/search/SearchBar';
import Link from 'next/link';
import { Plus, Loader2 } from 'lucide-react';

const SORTS = [
  { value: 'recent', label: 'Most Recent' },
  { value: 'most_upvoted', label: 'Most Upvoted' },
  { value: 'most_commented', label: 'Most Discussed' },
];

export default function ThreadsPage() {
  const sp = useSearchParams();
  const [threads, setThreads] = useState<Thread[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [sort, setSort] = useState('recent');
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const fetchThreads = useCallback(async () => {
    setLoading(true);
    try {
      const params: Record<string,string> = { sort, page: String(page), per_page: '15' };
      if (search) params.search = search;
      if (sp.get('protocol_id')) params.protocol_id = sp.get('protocol_id')!;
      const res = await threadsApi.list(params);
      setThreads(res.data.data);
      setLastPage(res.data.last_page);
    } catch(e){ console.error(e); } finally { setLoading(false); }
  }, [search, sort, page, sp]);

  useEffect(() => { fetchThreads(); }, [fetchThreads]);
  useEffect(() => { setPage(1); }, [search, sort]);

  return (
    <div>
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
          <h1 className="text-3xl font-bold text-forest">Discussions</h1>
          <p className="text-slate-light mt-1">Community threads on protocols and wellness</p>
        </div>
        <Link href="/threads/new" className="btn-primary self-start sm:self-auto">
          <Plus className="w-4 h-4" /> New Thread
        </Link>
      </div>

      <div className="card p-4 mb-6 space-y-3">
        <SearchBar value={search} onChange={setSearch} placeholder="Search discussions..." />
        <select value={sort} onChange={e => setSort(e.target.value)} className="select w-auto">
          {SORTS.map(s => <option key={s.value} value={s.value}>{s.label}</option>)}
        </select>
      </div>

      {loading ? (
        <div className="flex justify-center py-20"><Loader2 className="w-8 h-8 animate-spin text-sage" /></div>
      ) : threads.length === 0 ? (
        <div className="text-center py-20 text-slate-light">
          <p className="text-lg mb-2">No threads found</p>
          <Link href="/threads/new" className="text-forest underline">Start a discussion</Link>
        </div>
      ) : (
        <div className="space-y-3">{threads.map(t => <ThreadCard key={t.id} thread={t} />)}</div>
      )}

      {lastPage > 1 && (
        <div className="flex justify-center gap-2 mt-8">
          <button disabled={page <= 1} onClick={() => setPage(p => p-1)} className="btn-secondary disabled:opacity-40">Previous</button>
          <span className="flex items-center px-4 text-sm text-slate-light">Page {page} of {lastPage}</span>
          <button disabled={page >= lastPage} onClick={() => setPage(p => p+1)} className="btn-secondary disabled:opacity-40">Next</button>
        </div>
      )}
    </div>
  );
}
