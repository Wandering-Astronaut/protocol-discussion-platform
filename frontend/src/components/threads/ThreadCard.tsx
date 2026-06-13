import Link from 'next/link';
import { MessageCircle, ChevronUp, Tag } from 'lucide-react';
import { Thread } from '@/lib/api';
import { formatDate, truncate } from '@/lib/utils';

export default function ThreadCard({ thread }: { thread: Thread }) {
  return (
    <Link href={`/threads/${thread.id}`} className="card p-5 block group">
      <div className="flex gap-4">
        {/* Vote score */}
        <div className="flex flex-col items-center gap-1 min-w-[2.5rem]">
          <ChevronUp className="w-4 h-4 text-slate-light" />
          <span className={`text-sm font-bold ${thread.vote_score > 0 ? 'text-forest' : thread.vote_score < 0 ? 'text-red-500' : 'text-slate-light'}`}>
            {thread.vote_score}
          </span>
        </div>

        <div className="flex-1 min-w-0">
          {thread.protocol && (
            <div className="flex items-center gap-1.5 mb-1.5">
              <span className="text-xs text-sage font-medium bg-sage-muted px-2 py-0.5 rounded-full">
                📋 {thread.protocol.title}
              </span>
            </div>
          )}
          <h3 className="font-semibold text-forest group-hover:text-sage transition-colors mb-1.5 line-clamp-2">
            {thread.title}
          </h3>
          <p className="text-sm text-slate-light line-clamp-2 mb-3">
            {truncate(thread.body.replace(/[#*`]/g, ''), 150)}
          </p>

          {thread.tags?.length > 0 && (
            <div className="flex flex-wrap gap-1.5 mb-3">
              {thread.tags.slice(0, 3).map(tag => (
                <span key={tag} className="tag-pill">{tag}</span>
              ))}
            </div>
          )}

          <div className="flex items-center gap-4 text-xs text-slate-light">
            <span className="flex items-center gap-1">
              <MessageCircle className="w-3 h-3" />{thread.comment_count} comments
            </span>
            <span>by <span className="font-medium text-slate">{thread.user?.name}</span></span>
            <span>{formatDate(thread.created_at)}</span>
          </div>
        </div>
      </div>
    </Link>
  );
}
