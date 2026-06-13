'use client';
import { useState, useEffect } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import { threadsApi, protocolsApi, Protocol } from '@/lib/api';
import { DEMO_USER_ID } from '@/lib/utils';
import { Loader2, X, Plus } from 'lucide-react';

export default function NewThreadPage() {
  const router = useRouter();
  const sp = useSearchParams();
  const [form, setForm] = useState({ title:'', body:'', protocol_id: sp.get('protocol_id')||'', tags:[] as string[] });
  const [tagInput, setTagInput] = useState('');
  const [protocols, setProtocols] = useState<Protocol[]>([]);
  const [saving, setSaving] = useState(false);
  const [errors, setErrors] = useState<Record<string,string>>({});

  useEffect(() => {
    protocolsApi.list({ per_page:'50', sort:'recent' }).then(r => setProtocols(r.data.data)).catch(()=>{});
  }, []);

  function addTag() {
    const t = tagInput.trim().toLowerCase().replace(/\s+/g,'-');
    if (t && !form.tags.includes(t) && form.tags.length < 6) setForm(f=>({...f, tags:[...f.tags,t]}));
    setTagInput('');
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    const errs: Record<string,string> = {};
    if (!form.title.trim()) errs.title = 'Title is required';
    if (!form.body.trim()) errs.body = 'Body is required';
    if (Object.keys(errs).length) { setErrors(errs); return; }
    setSaving(true);
    try {
      const payload: any = { ...form, user_id: DEMO_USER_ID };
      if (!payload.protocol_id) delete payload.protocol_id;
      const res = await threadsApi.create(payload);
      router.push(`/threads/${res.data.id}`);
    } catch(e: any) {
      if (e.response?.data?.errors) setErrors(e.response.data.errors);
    } finally { setSaving(false); }
  }

  return (
    <div className="max-w-2xl mx-auto">
      <h1 className="text-3xl font-bold text-forest mb-2">Start a Discussion</h1>
      <p className="text-slate-light mb-8">Ask questions, share experiences, or start a conversation.</p>

      <form onSubmit={handleSubmit} className="space-y-6">
        <div className="card p-6 space-y-5">
          <div>
            <label className="block text-sm font-medium text-slate mb-1.5">Thread Title *</label>
            <input value={form.title} onChange={e => setForm(f=>({...f,title:e.target.value}))}
              className="input" placeholder="What's your discussion about?" />
            {errors.title && <p className="text-red-500 text-xs mt-1">{errors.title}</p>}
          </div>

          <div>
            <label className="block text-sm font-medium text-slate mb-1.5">Related Protocol (optional)</label>
            <select value={form.protocol_id} onChange={e => setForm(f=>({...f,protocol_id:e.target.value}))} className="select">
              <option value="">No specific protocol</option>
              {protocols.map(p => <option key={p.id} value={p.id}>{p.title}</option>)}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-slate mb-1.5">Tags</label>
            <div className="flex gap-2 mb-2 flex-wrap">
              {form.tags.map(tag => (
                <span key={tag} className="tag-pill flex items-center gap-1">
                  {tag}<button type="button" onClick={() => setForm(f=>({...f,tags:f.tags.filter(t=>t!==tag)}))}>
                    <X className="w-3 h-3" /></button>
                </span>
              ))}
            </div>
            <div className="flex gap-2">
              <input value={tagInput} onChange={e => setTagInput(e.target.value)}
                onKeyDown={e => { if(e.key==='Enter'){e.preventDefault();addTag();}}}
                className="input flex-1" placeholder="Add tag and press Enter" />
              <button type="button" onClick={addTag} className="btn-secondary"><Plus className="w-4 h-4" /></button>
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-slate mb-1.5">Body *</label>
            <textarea value={form.body} onChange={e => setForm(f=>({...f,body:e.target.value}))}
              className="textarea h-40" placeholder="Share your thoughts, question, or experience in detail..." />
            {errors.body && <p className="text-red-500 text-xs mt-1">{errors.body}</p>}
          </div>
        </div>

        <div className="flex gap-3">
          <button type="submit" disabled={saving} className="btn-primary flex-1">
            {saving ? <><Loader2 className="w-4 h-4 animate-spin" /> Posting...</> : 'Post Thread'}
          </button>
          <button type="button" onClick={() => router.back()} className="btn-secondary">Cancel</button>
        </div>
      </form>
    </div>
  );
}
