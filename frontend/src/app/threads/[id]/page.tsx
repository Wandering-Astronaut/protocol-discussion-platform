'use client';
import { useEffect, useState } from 'react';
import { useParams, useRouter } from 'next/navigation';
import { threadsApi, Thread, Comment } from '@/lib/api';
import { formatDate, DEMO_USER_ID } from '@/lib/utils';
import VoteButton from '@/components/voting/VoteButton';
import CommentItem from '@/components/threads/CommentItem';
import { ChevronLeft, MessageCircle, Loader2, Send } from 'lucide-react';
import Link from 'next/link';

export default function ThreadDetailPage() {
  const { id } = useParams<{ id: string }>();
  const router = useRouter();
  const [thread, setThread] = useState<Thread | null>(null);
  const [comments, setComments] = useState<Comment[]>([]);
  const [loading, setLoading] = useState(true);
  const [commentBody, setCommentBody] = useState('');
  const [submitting, setSubmitting] = useState(false);

  async function loadThread() {
    try {
      const [tRes, cRes] = await Promise.all([
        threadsApi.get(Number(id)),
        threadsApi.comments(Number(id)),
      ]);
      setThread(tRes.data);
      setComments(cRes.data);
    } catch(e){ console.error(e); } finally { setLoading(false); }
  }

  useEffect(() => { loadThread(); }, [id]);

  async function submitComment() {
    if (!commentBody.trim() || !thread) return;
    setSubmitting(true);
    try {
      await threadsApi.addComment(thread.id, { body: commentBody, user_id: DEMO_USER_ID });
      setCommentBody('');
      const cRes = await threadsApi.comments(thread.id);
      setComments(cRes.data);
      const tRes = await threadsApi.get(thread.id);
      setThread(tRes.data);
    } catch(e){ console.error(e); } finally { setSubmitting(false); }
  }

  if (loading) return <div className="flex justify-center py-20"><Loader2 className="w-8 h-8 animate-spin text-sage" /></div>;
  if (!thread) return <div className="text-center py-20 text-slate-light">Thread not found.</div>;

  return (
    <div className="max-w-3xl mx-auto">
      <button onClick={() => router.back()} className="flex items-center gap-1.5 text-sm text-slate-light hover:text-forest mb-6 transition-colors">
        <ChevronLeft className="w-4 h-4" /> Back
      </button>

      {/* Thread body */}
      <div className="card p-6 mb-6">
        {thread.protocol && (
          <Link href={`/protocols/${thread.protocol.id}`}
            className="inline-flex items-center gap-1.5 text-xs text-sage bg-sage-muted px-3 py-1 rounded-full mb-3 hover:bg-sage hover:text-white transition-colors">
            📋 {thread.protocol.title}
          </Link>
        )}
        <h1 className="text-2xl font-bold text-forest mb-3">{thread.title}</h1>
        <p className="text-slate leading-relaxed whitespace-pre-line mb-4">{thread.body}</p>
        {thread.tags?.length > 0 && (
          <div className="flex flex-wrap gap-2 mb-4">
            {thread.tags.map(tag => <span key={tag} className="tag-pill">{tag}</span>)}
          </div>
        )}
        <div className="flex items-center justify-between pt-4 border-t border-sage-muted">
          <div className="flex items-center gap-3 text-sm text-slate-light">
            <img src={`https://ui-avatars.com/api/?name=${encodeURIComponent(thread.user?.name||'U')}&background=1A3C34&color=fff&size=28`}
              alt="" className="w-7 h-7 rounded-full" />
            <span>by <span className="font-medium text-slate">{thread.user?.name}</span></span>
            <span>·</span><span>{formatDate(thread.created_at)}</span>
            <span>·</span>
            <span className="flex items-center gap-1"><MessageCircle className="w-3.5 h-3.5" />{thread.comment_count}</span>
          </div>
          <VoteButton voteableType="thread" voteableId={thread.id} initialScore={thread.vote_score} />
        </div>
      </div>

      {/* Comment box */}
      <div className="card p-5 mb-6">
        <h3 className="font-semibold text-forest mb-3">Leave a Comment</h3>
        <textarea value={commentBody} onChange={e => setCommentBody(e.target.value)}
          className="textarea h-24 mb-3" placeholder="Share your thoughts, experience, or questions..." />
        <button onClick={submitComment} disabled={submitting || !commentBody.trim()} className="btn-primary">
          {submitting ? <><Loader2 className="w-4 h-4 animate-spin" /> Posting...</> : <><Send className="w-4 h-4" /> Post Comment</>}
        </button>
      </div>

      {/* Comments */}
      <div className="card p-5">
        <h2 className="font-semibold text-forest mb-4">{comments.length} Comment{comments.length !== 1 ? 's' : ''}</h2>
        {comments.length === 0 ? (
          <p className="text-slate-light text-sm text-center py-6">No comments yet. Be the first!</p>
        ) : (
          <div className="divide-y divide-sage-muted">
            {comments.map(c => (
              <CommentItem key={c.id} comment={c} threadId={thread.id} onReplyAdded={() => {
                threadsApi.comments(thread.id).then(r => setComments(r.data));
              }} />
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
