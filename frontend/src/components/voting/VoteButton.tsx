'use client';
import { useState } from 'react';
import { ChevronUp, ChevronDown } from 'lucide-react';
import { votesApi } from '@/lib/api';
import { DEMO_USER_ID, cn } from '@/lib/utils';

interface Props {
  voteableType: 'thread' | 'comment';
  voteableId: number;
  initialScore: number;
  initialVote?: number | null;
  size?: 'sm' | 'md';
}

export default function VoteButton({ voteableType, voteableId, initialScore, initialVote = null, size = 'md' }: Props) {
  const [score, setScore] = useState(initialScore);
  const [userVote, setUserVote] = useState<number | null>(initialVote);
  const [loading, setLoading] = useState(false);

  async function handleVote(value: 1 | -1) {
    if (loading) return;
    setLoading(true);
    try {
      const res = await votesApi.vote({ user_id: DEMO_USER_ID, voteable_type: voteableType, voteable_id: voteableId, value });
      setScore(res.data.vote_score);
      setUserVote(res.data.user_vote);
    } catch (e) { console.error(e); }
    finally { setLoading(false); }
  }

  const iconSize = size === 'sm' ? 'w-3.5 h-3.5' : 'w-4 h-4';
  const textSize = size === 'sm' ? 'text-xs' : 'text-sm';

  return (
    <div className="flex items-center gap-0.5">
      <button onClick={() => handleVote(1)} disabled={loading}
        className={cn('vote-btn', userVote === 1 ? 'vote-btn-up-active' : 'vote-btn-up')}>
        <ChevronUp className={iconSize} />
      </button>
      <span className={cn('font-semibold min-w-[2rem] text-center', textSize,
        score > 0 ? 'text-forest' : score < 0 ? 'text-red-500' : 'text-slate-light')}>
        {score}
      </span>
      <button onClick={() => handleVote(-1)} disabled={loading}
        className={cn('vote-btn', userVote === -1 ? 'vote-btn-down-active' : 'vote-btn-down')}>
        <ChevronDown className={iconSize} />
      </button>
    </div>
  );
}
