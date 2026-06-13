'use client';
import { useState } from 'react';
import { Comment, threadsApi, commentsApi } from '@/lib/api';
import { formatDate, DEMO_USER_ID } from '@/lib/utils';
import VoteButton from '@/components/voting/VoteButton';
import { Reply, ChevronDown, ChevronUp } from 'lucide-react';

interface Props { comment: Comment; threadId: number; depth?: number; onReplyAdded?: () => void; }

export default function CommentItem({ comment, threadId, depth = 0, onReplyAdded }: Props) {
  const [showReply, setShowReply] = useState(false);
  const [replyText, setReplyText] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [showReplies, setShowReplies] = useState(true);

  async function submitReply() {
    if (!replyText.trim()) return;
    setSubmitting(true);
    try {
      await threadsApi.addComment(threadId, { body: replyText, parent_id: comment.id, user_id: DEMO_USER_ID });
      setReplyText(''); setShowReply(false);
      onReplyAdded?.();
    } catch(e){ console.error(e); } finally { setSubmitting(false); }
  }

  const indentClass = depth === 0 ? '' : depth === 1 ? 'ml-6 border-l-2 border-sage-muted pl-4' : 'ml-10 border-l-2 border-gray-100 pl-4';

  return (
    <div className={indentClass}>
      <div className="py-3">
        <div className="flex items-start gap-3">
          <img src={`https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user?.name||'U')}&background=4A7C6F&color=fff&size=32`}
            alt="" className="w-7 h-7 rounded-full flex-shrink-0 mt-0.5" />
          <div className="flex-1 min-w-0">
            <div className="flex items-center gap-2 mb-1.5">
              <span className="text-sm font-medium text-forest">{comment.user?.name || 'Unknown'}</span>
              <span className="text-xs text-slate-light">{formatDate(comment.created_at)}</span>
            </div>
            <p className={`text-sm leading-relaxed ${comment.is_deleted ? 'text-slate-light italic' : 'text-slate'}`}>
              {comment.body}
            </p>
            {!comment.is_deleted && (
              <div className="flex items-center gap-3 mt-2">
                <VoteButton voteableType="comment" voteableId={comment.id}
                  initialScore={comment.vote_score} size="sm" />
                {depth < 3 && (
                  <button onClick={() => setShowReply(!showReply)}
                    className="flex items-center gap-1 text-xs text-slate-light hover:text-forest transition-colors">
                    <Reply className="w-3.5 h-3.5" /> Reply
                  </button>
                )}
              </div>
            )}
            {showReply && (
              <div className="mt-3 space-y-2">
                <textarea value={replyText} onChange={e => setReplyText(e.target.value)}
                  className="textarea text-sm h-20" placeholder="Write a reply..." />
                <div className="flex gap-2">
                  <button onClick={submitReply} disabled={submitting || !replyText.trim()}
                    className="btn-primary text-xs py-1.5 px-3">
                    {submitting ? 'Posting...' : 'Post Reply'}
                  </button>
                  <button onClick={() => setShowReply(false)} className="btn-ghost text-xs py-1.5 px-3">Cancel</button>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Nested replies */}
      {comment.replies && comment.replies.length > 0 && (
        <div>
          <button onClick={() => setShowReplies(!showReplies)}
            className="flex items-center gap-1 text-xs text-sage mb-1 ml-10 hover:text-forest transition-colors">
            {showReplies ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />}
            {comment.replies.length} {comment.replies.length === 1 ? 'reply' : 'replies'}
          </button>
          {showReplies && comment.replies.map(reply => (
            <CommentItem key={reply.id} comment={reply} threadId={threadId} depth={depth + 1} onReplyAdded={onReplyAdded} />
          ))}
        </div>
      )}
    </div>
  );
}
