import Link from 'next/link';
import { MessageCircle, Star, ChevronUp, Clock, BarChart } from 'lucide-react';
import { Protocol } from '@/lib/api';
import { formatDate, truncate, getDifficultyColor, getCategoryIcon } from '@/lib/utils';

export default function ProtocolCard({ protocol }: { protocol: Protocol }) {
  return (
    <Link href={`/protocols/${protocol.id}`} className="card p-5 block group">
      <div className="flex items-start gap-3 mb-3">
        <div className="text-2xl mt-0.5">{getCategoryIcon(protocol.category)}</div>
        <div className="flex-1 min-w-0">
          <h3 className="font-semibold text-forest group-hover:text-sage transition-colors leading-snug mb-1 line-clamp-2">
            {protocol.title}
          </h3>
          <p className="text-sm text-slate-light line-clamp-2">
            {truncate(protocol.content.replace(/[#*`]/g, ''), 120)}
          </p>
        </div>
      </div>

      {/* Tags */}
      {protocol.tags?.length > 0 && (
        <div className="flex flex-wrap gap-1.5 mb-3">
          {protocol.tags.slice(0, 4).map(tag => (
            <span key={tag} className="tag-pill">{tag}</span>
          ))}
        </div>
      )}

      {/* Meta row */}
      <div className="flex items-center justify-between text-xs text-slate-light">
        <div className="flex items-center gap-3">
          <span className={`px-2 py-0.5 rounded-full font-medium ${getDifficultyColor(protocol.difficulty)}`}>
            {protocol.difficulty}
          </span>
          {protocol.duration && (
            <span className="flex items-center gap-1"><Clock className="w-3 h-3" />{protocol.duration}</span>
          )}
        </div>
        <div className="flex items-center gap-3">
          <span className="flex items-center gap-1 text-amber">
            <Star className="w-3 h-3 fill-amber" />{protocol.avg_rating?.toFixed(1) || '0.0'}
          </span>
          <span className="flex items-center gap-1">
            <MessageCircle className="w-3 h-3" />{protocol.review_count}
          </span>
          <span className="flex items-center gap-1 text-forest font-medium">
            <ChevronUp className="w-3 h-3" />{protocol.vote_score}
          </span>
        </div>
      </div>

      {/* Author */}
      <div className="mt-3 pt-3 border-t border-sage-muted flex items-center gap-2">
        <img src={protocol.user?.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(protocol.user?.name||'U')}&background=1A3C34&color=fff&size=32`}
          alt="" className="w-6 h-6 rounded-full object-cover" />
        <span className="text-xs text-slate-light">by <span className="font-medium text-slate">{protocol.user?.name}</span> · {formatDate(protocol.created_at)}</span>
      </div>
    </Link>
  );
}
