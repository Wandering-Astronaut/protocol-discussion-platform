'use client';
import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { protocolsApi } from '@/lib/api';
import { DEMO_USER_ID } from '@/lib/utils';
import { Loader2, X, Plus } from 'lucide-react';

const CATEGORIES = ['nutrition','sleep','mental-health','hormones','recovery','nootropics','detox','longevity','immunity','general'];
const DIFFICULTIES = ['beginner','intermediate','advanced'];

export default function NewProtocolPage() {
  const router = useRouter();
  const [form, setForm] = useState({ title:'', content:'', category:'nutrition', difficulty:'beginner', duration:'', tags:[] as string[] });
  const [tagInput, setTagInput] = useState('');
  const [saving, setSaving] = useState(false);
  const [errors, setErrors] = useState<Record<string,string>>({});

  function addTag() {
    const t = tagInput.trim().toLowerCase().replace(/\s+/g,'-');
    if (t && !form.tags.includes(t) && form.tags.length < 8) {
      setForm(f => ({ ...f, tags: [...f.tags, t] }));
    }
    setTagInput('');
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    const errs: Record<string,string> = {};
    if (!form.title.trim()) errs.title = 'Title is required';
    if (!form.content.trim()) errs.content = 'Content is required';
    if (Object.keys(errs).length) { setErrors(errs); return; }
    setSaving(true);
    try {
      const res = await protocolsApi.create({ ...form, user_id: DEMO_USER_ID });
      router.push(`/protocols/${res.data.id}`);
    } catch(e: any) {
      if (e.response?.data?.errors) setErrors(e.response.data.errors);
    } finally { setSaving(false); }
  }

  return (
    <div className="max-w-2xl mx-auto">
      <h1 className="text-3xl font-bold text-forest mb-2">Create a Protocol</h1>
      <p className="text-slate-light mb-8">Share your structured wellness approach with the community.</p>

      <form onSubmit={handleSubmit} className="space-y-6">
        <div className="card p-6 space-y-5">
          <div>
            <label className="block text-sm font-medium text-slate mb-1.5">Protocol Title *</label>
            <input value={form.title} onChange={e => setForm(f=>({...f,title:e.target.value}))}
              className="input" placeholder="e.g. 30-Day Gut Reset Protocol" />
            {errors.title && <p className="text-red-500 text-xs mt-1">{errors.title}</p>}
          </div>

          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-slate mb-1.5">Category</label>
              <select value={form.category} onChange={e => setForm(f=>({...f,category:e.target.value}))} className="select">
                {CATEGORIES.map(c => <option key={c} value={c}>{c.replace('-',' ')}</option>)}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-slate mb-1.5">Difficulty</label>
              <select value={form.difficulty} onChange={e => setForm(f=>({...f,difficulty:e.target.value}))} className="select">
                {DIFFICULTIES.map(d => <option key={d} value={d}>{d}</option>)}
              </select>
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-slate mb-1.5">Duration</label>
            <input value={form.duration} onChange={e => setForm(f=>({...f,duration:e.target.value}))}
              className="input" placeholder="e.g. 30 days, 8 weeks, Ongoing" />
          </div>

          <div>
            <label className="block text-sm font-medium text-slate mb-1.5">Tags</label>
            <div className="flex gap-2 mb-2 flex-wrap">
              {form.tags.map(tag => (
                <span key={tag} className="tag-pill flex items-center gap-1">
                  {tag}
                  <button type="button" onClick={() => setForm(f=>({...f,tags:f.tags.filter(t=>t!==tag)}))}>
                    <X className="w-3 h-3" />
                  </button>
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
            <label className="block text-sm font-medium text-slate mb-1.5">Protocol Content *</label>
            <p className="text-xs text-slate-light mb-2">Use ## for headings, **bold**, and - for bullet lists (Markdown supported)</p>
            <textarea value={form.content} onChange={e => setForm(f=>({...f,content:e.target.value}))}
              className="textarea h-64 font-mono text-sm" placeholder="## Overview&#10;Describe your protocol...&#10;&#10;## Week 1&#10;..." />
            {errors.content && <p className="text-red-500 text-xs mt-1">{errors.content}</p>}
          </div>
        </div>

        <div className="flex gap-3">
          <button type="submit" disabled={saving} className="btn-primary flex-1">
            {saving ? <><Loader2 className="w-4 h-4 animate-spin" /> Publishing...</> : 'Publish Protocol'}
          </button>
          <button type="button" onClick={() => router.back()} className="btn-secondary">Cancel</button>
        </div>
      </form>
    </div>
  );
}
