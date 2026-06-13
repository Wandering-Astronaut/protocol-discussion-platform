'use client';
export const dynamic = 'force-dynamic';
import { useState, useEffect, useCallback } from 'react';
import { protocolsApi, threadsApi, Protocol, Thread } from '@/lib/api';
import ProtocolCard from '@/components/protocols/ProtocolCard';
import ThreadCard from '@/components/threads/ThreadCard';
import { Search, Loader2 } from 'lucide-react';

type Tab = 'protocols' | 'threads';

export default function SearchPage() {
  const [q, setQ] = useState('');
  const [tab, setTab] = useState<Tab>('protocols');
  const [sort, setSort] = useState('recent');
  const [protocols, setProtocols] = useState<Protocol[]>([]);
  const [threads, setThreads] = useState<Thread[]>([]);
  const [loading, setLoading] = useState(false);

  const doSearch = useCallback(async () => {
    if (!q.trim()) { setProtocols([]); setThreads([]); return; }
    setLoading(true);
    try {
      if (tab === 'protocols') {
        const res = await protocolsApi.search({ q, sort });
        const hits = res.data.hits?.map((h: any) => ({ ...h.document, user: { name: h.document.author } })) || [];
        setProtocols(hits);
      } else {
        const res = await threadsApi.search({ q, sort });
        const hits = res.data.hits?.map((h: any) => ({ ...h.document, user: { name: h.document.author } })) || [];
        setThreads(hits);
      }
    } catch(e){ console.error(e); } finally { setLoading(false); }
  }, [q, tab, sort]);

  useEffect(() => {
    const t = setTimeout(doSearch, 300);
    return () => clearTimeout(t);
  }, [doSearch]);

  const SORTS_P = [{ value:'recent', label:'Recent' },{ value:'most_upvoted', label:'Most Upvoted' },{ value:'highest_rated', label:'Highest Rated' },{ value:'most_reviewed', label:'Most Reviewed' }];
  const SORTS_T = [{ value:'recent', label:'Recent' },{ value:'most_upvoted', label:'Most Upvoted' },{ value:'most_commented', label:'Most Commented' }];
  const sorts = tab === 'protocols' ? SORTS_P : SORTS_T;

  return (
    <div className="max-w-4xl mx-auto">
      <h1 className="text-3xl font-bold text-forest mb-6">Search</h1>

      {/* Search input */}
      <div className="relative mb-6">
        <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-light" />
        <input value={q} onChange={e => setQ(e.target.value)} autoFocus
          className="input pl-12 py-3 text-lg shadow-sm"
          placeholder="Search protocols, threads, tags..." />
        {loading && <Loader2 className="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 animate-spin text-sage" />}
      </div>

      {/* Tabs + sort */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div className="flex rounded-lg border border-sage-muted overflow-hidden w-fit">
          {(['protocols','threads'] as Tab[]).map(t => (
            <button key={t} onClick={() => { setTab(t); setSort('recent'); }}
              className={`px-5 py-2 text-sm font-medium transition-colors capitalize ${tab === t ? 'bg-forest text-white' : 'bg-white text-slate hover:bg-cream-warm'}`}>
              {t}
            </button>
          ))}
        </div>
        <select value={sort} onChange={e => setSort(e.target.value)} className="select w-auto">
          {sorts.map(s => <option key={s.value} value={s.value}>{s.label}</option>)}
        </select>
      </div>

      {/* Results */}
      {!q.trim() ? (
        <div className="text-center py-20 text-slate-light">
          <Search className="w-12 h-12 mx-auto mb-4 opacity-30" />
          <p>Type to search protocols and discussions</p>
        </div>
      ) : tab === 'protocols' ? (
        protocols.length === 0 && !loading ? (
          <p className="text-center py-16 text-slate-light">No protocols found for "{q}"</p>
        ) : (
          <div className="grid sm:grid-cols-2 gap-5">
            {protocols.map((p,i) => <ProtocolCard key={p.id ?? i} protocol={p} />)}
          </div>
        )
      ) : (
        threads.length === 0 && !loading ? (
          <p className="text-center py-16 text-slate-light">No threads found for "{q}"</p>
        ) : (
          <div className="space-y-3">
            {threads.map((t,i) => <ThreadCard key={t.id ?? i} thread={t} />)}
          </div>
        )
      )}
    </div>
  );
}
