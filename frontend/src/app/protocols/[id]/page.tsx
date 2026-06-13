'use client';
import { useEffect, useState } from 'react';
import { useParams, useRouter } from 'next/navigation';
import { protocolsApi, threadsApi, Protocol, Review, Thread } from '@/lib/api';
import { formatDate, getDifficultyColor, getCategoryIcon, DEMO_USER_ID } from '@/lib/utils';
import StarRating from '@/components/ui/StarRating';
import ThreadCard from '@/components/threads/ThreadCard';
import VoteButton from '@/components/voting/VoteButton';
import Link from 'next/link';
import { Clock, ChevronLeft, Plus, Star, Loader2, CheckCircle } from 'lucide-react';

export default function ProtocolDetailPage() {
  const { id } = useParams<{ id: string }>();
  const router = useRouter();
  const [protocol, setProtocol] = useState<Protocol | null>(null);
  const [threads, setThreads] = useState<Thread[]>([]);
  const [loading, setLoading] = useState(true);
  const [reviewRating, setReviewRating] = useState(5);
  const [reviewTitle, setReviewTitle] = useState('');
  const [reviewBody, setReviewBody] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [reviewSuccess, setReviewSuccess] = useState(false);
  const [reviews, setReviews] = useState<Review[]>([]);

  useEffect(() => {
    async function load() {
      setLoading(true);
      try {
        const [pRes, rRes, tRes] = await Promise.all([
          protocolsApi.get(Number(id)),
          protocolsApi.reviews(Number(id)),
          threadsApi.list({ protocol_id: id, sort: 'most_upvoted', per_page: '5' }),
        ]);
        setProtocol(pRes.data);
        setReviews(rRes.data.data);
        setThreads(tRes.data.data);
      } catch(e) { console.error(e); }
      finally { setLoading(false); }
    }
    load();
  }, [id]);

  async function submitReview() {
    if (!protocol) return;
    setSubmitting(true);
    try {
      await protocolsApi.addReview(protocol.id, { rating: reviewRating, title: reviewTitle, body: reviewBody, user_id: DEMO_USER_ID });
      setReviewSuccess(true);
      const rRes = await protocolsApi.reviews(protocol.id);
      setReviews(rRes.data.data);
      const pRes = await protocolsApi.get(protocol.id);
      setProtocol(pRes.data);
      setReviewTitle(''); setReviewBody('');
    } catch(e) { console.error(e); } finally { setSubmitting(false); }
  }

  if (loading) return <div className="flex justify-center py-20"><Loader2 className="w-8 h-8 animate-spin text-sage" /></div>;
  if (!protocol) return <div className="text-center py-20 text-slate-light">Protocol not found.</div>;

  const contentHtml = protocol.content
    .replace(/^## (.+)$/gm, '<h2>$1</h2>')
    .replace(/^### (.+)$/gm, '<h3>$1</h3>')
    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
    .replace(/^- (.+)$/gm, '<li>$1</li>')
    .replace(/(<li>.*<\/li>\n?)+/g, m => `<ul>${m}</ul>`)
    .replace(/\n\n/g, '</p><p>')
    .replace(/^(?!<[hul])(.+)$/gm, (m) => m.startsWith('<') ? m : `<p>${m}</p>`);

  return (
    <div className="max-w-4xl mx-auto">
      <button onClick={() => router.back()} className="flex items-center gap-1.5 text-sm text-slate-light hover:text-forest mb-6 transition-colors">
        <ChevronLeft className="w-4 h-4" /> Back to Protocols
      </button>

      {/* Header */}
      <div className="card p-8 mb-6">
        <div className="flex items-start gap-4 mb-6">
          <div className="text-4xl">{getCategoryIcon(protocol.category)}</div>
          <div className="flex-1">
            <div className="flex flex-wrap gap-2 mb-3">
              <span className={`px-2.5 py-0.5 rounded-full text-xs font-medium ${getDifficultyColor(protocol.difficulty)}`}>{protocol.difficulty}</span>
              <span className="px-2.5 py-0.5 rounded-full text-xs font-medium bg-sage-muted text-forest">{protocol.category}</span>
              {protocol.duration && <span className="flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-slate"><Clock className="w-3 h-3" />{protocol.duration}</span>}
            </div>
            <h1 className="text-3xl font-bold text-forest mb-2">{protocol.title}</h1>
            <div className="flex items-center gap-4 flex-wrap">
              <div className="flex items-center gap-2">
                <img src={`https://ui-avatars.com/api/?name=${encodeURIComponent(protocol.user?.name||'U')}&background=1A3C34&color=fff&size=32`} alt="" className="w-7 h-7 rounded-full" />
                <span className="text-sm text-slate-light">by <span className="font-medium text-slate">{protocol.user?.name}</span></span>
              </div>
              <span className="text-sm text-slate-light">{formatDate(protocol.created_at)}</span>
              <div className="flex items-center gap-1.5">
                <StarRating rating={protocol.avg_rating} size="sm" />
                <span className="text-sm text-slate-light">({protocol.review_count} reviews)</span>
              </div>
              <VoteButton voteableType="thread" voteableId={protocol.id} initialScore={protocol.vote_score} />
            </div>
          </div>
        </div>
        {protocol.tags?.length > 0 && (
          <div className="flex flex-wrap gap-2">
            {protocol.tags.map(tag => <span key={tag} className="tag-pill">{tag}</span>)}
          </div>
        )}
      </div>

      {/* Content */}
      <div className="card p-8 mb-6">
        <div className="protocol-content prose max-w-none" dangerouslySetInnerHTML={{ __html: contentHtml }} />
      </div>

      {/* Threads under this protocol */}
      <div className="mb-6">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-xl font-bold text-forest">Discussions ({threads.length})</h2>
          <Link href={`/threads/new?protocol_id=${protocol.id}`} className="btn-secondary text-sm">
            <Plus className="w-4 h-4" /> Start Thread
          </Link>
        </div>
        {threads.length === 0 ? (
          <div className="card p-8 text-center text-slate-light">No discussions yet. <Link href={`/threads/new?protocol_id=${protocol.id}`} className="text-forest underline">Start one!</Link></div>
        ) : (
          <div className="space-y-3">{threads.map(t => <ThreadCard key={t.id} thread={t} />)}</div>
        )}
      </div>

      {/* Reviews */}
      <div className="card p-8 mb-6">
        <h2 className="text-xl font-bold text-forest mb-6">Reviews ({protocol.review_count})</h2>
        {reviews.map(r => (
          <div key={r.id} className="border-b border-sage-muted last:border-0 py-4 first:pt-0">
            <div className="flex items-start gap-3">
              <img src={`https://ui-avatars.com/api/?name=${encodeURIComponent(r.user?.name||'U')}&background=4A7C6F&color=fff&size=36`} alt="" className="w-9 h-9 rounded-full" />
              <div className="flex-1">
                <div className="flex items-center gap-2 mb-1">
                  <span className="font-medium text-slate">{r.user?.name}</span>
                  {r.verified_user && <span className="flex items-center gap-0.5 text-xs text-sage"><CheckCircle className="w-3 h-3" /> Verified</span>}
                  <span className="text-xs text-slate-light ml-auto">{formatDate(r.created_at)}</span>
                </div>
                <StarRating rating={r.rating} size="sm" />
                {r.title && <p className="font-medium text-slate mt-1.5">{r.title}</p>}
                {r.body && <p className="text-sm text-slate-light mt-1">{r.body}</p>}
              </div>
            </div>
          </div>
        ))}

        {/* Write review */}
        <div className="mt-6 pt-6 border-t border-sage-muted">
          <h3 className="font-semibold text-forest mb-4">Write a Review</h3>
          {reviewSuccess && <div className="bg-emerald-50 text-emerald-700 rounded-lg p-3 text-sm mb-4">✓ Review submitted!</div>}
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-slate mb-2">Rating</label>
              <StarRating rating={reviewRating} size="lg" interactive onChange={setReviewRating} />
            </div>
            <input value={reviewTitle} onChange={e => setReviewTitle(e.target.value)} className="input" placeholder="Review title (optional)" />
            <textarea value={reviewBody} onChange={e => setReviewBody(e.target.value)} className="textarea h-24" placeholder="Share your experience with this protocol..." />
            <button onClick={submitReview} disabled={submitting} className="btn-primary">
              {submitting ? <><Loader2 className="w-4 h-4 animate-spin" /> Submitting...</> : 'Submit Review'}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
