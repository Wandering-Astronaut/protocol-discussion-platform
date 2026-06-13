'use client';
export const dynamic = 'force-dynamic';
import { useEffect, useState, useCallback } from 'react';
import { protocolsApi, Protocol } from '@/lib/api';
import ProtocolCard from '@/components/protocols/ProtocolCard';
import SearchBar from '@/components/search/SearchBar';
import { Filter, Plus, Loader2 } from 'lucide-react';
import Link from 'next/link';
import { cn } from '@/lib/utils';

const SORTS = [
  { value: 'recent', label: 'Most Recent' },
  { value: 'most_upvoted', label: 'Most Upvoted' },
  { value: 'highest_rated', label: 'Highest Rated' },
  { value: 'most_reviewed', label: 'Most Reviewed' },
];
const CATEGORIES = ['nutrition','sleep','mental-health','hormones','recovery','nootropics','detox','longevity','immunity'];

export default function ProtocolsPage() {
  const [protocols, setProtocols] = useState<Protocol[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [sort, setSort] = useState('recent');
  const [category, setCategory] = useState('');
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const fetchProtocols = useCallback(async () => {
    setLoading(true);
    try {
      const params: Record<string,string> = { sort, page: String(page), per_page: '12' };
      if (search) params.search = search;
      if (category) params.category = category;
      const res = await protocolsApi.list(params);
      setProtocols(res.data.data);
      setLastPage(res.data.last_page);
    } catch (e) { console.error(e); }
    finally { setLoading(false); }
  }, [search, sort, category, page]);

  useEffect(() => { fetchProtocols(); }, [fetchProtocols]);
  useEffect(() => { setPage(1); }, [search, sort, category]);

  return (
    <div>
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
          <h1 className="text-3xl font-bold text-forest">Protocols</h1>
          <p className="text-slate-light mt-1">Evidence-based wellness protocols from the community</p>
        </div>
        <Link href="/protocols/new" className="btn-primary self-start sm:self-auto">
          <Plus className="w-4 h-4" /> New Protocol
        </Link>
      </div>

      {/* Filters */}
      <div className="card p-4 mb-6 space-y-4">
        <SearchBar value={search} onChange={setSearch} placeholder="Search protocols by title, content, or tags..." />
        <div className="flex flex-wrap gap-3">
          <select value={sort} onChange={e => setSort(e.target.value)} className="select w-auto">
            {SORTS.map(s => <option key={s.value} value={s.value}>{s.label}</option>)}
          </select>
          <select value={category} onChange={e => setCategory(e.target.value)} className="select w-auto">
            <option value="">All Categories</option>
            {CATEGORIES.map(c => <option key={c} value={c}>{c.replace('-', ' ')}</option>)}
          </select>
        </div>
        {category && (
          <div className="flex gap-2">
            <span className="tag-pill" onClick={() => setCategory('')}>✕ {category}</span>
          </div>
        )}
      </div>

      {/* Grid */}
      {loading ? (
        <div className="flex justify-center py-20"><Loader2 className="w-8 h-8 animate-spin text-sage" /></div>
      ) : protocols.length === 0 ? (
        <div className="text-center py-20 text-slate-light">
          <p className="text-lg mb-2">No protocols found</p>
          <p className="text-sm">Try a different search or <Link href="/protocols/new" className="text-forest underline">create one</Link>.</p>
        </div>
      ) : (
        <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
          {protocols.map(p => <ProtocolCard key={p.id} protocol={p} />)}
        </div>
      )}

      {/* Pagination */}
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
