'use client';
import { Star } from 'lucide-react';
import { cn } from '@/lib/utils';

interface Props { rating: number; max?: number; size?: 'sm'|'md'|'lg'; interactive?: boolean; onChange?: (r: number) => void; }

export default function StarRating({ rating, max = 5, size = 'md', interactive = false, onChange }: Props) {
  const sizes = { sm: 'w-3 h-3', md: 'w-4 h-4', lg: 'w-5 h-5' };
  return (
    <div className="flex items-center gap-0.5">
      {Array.from({ length: max }).map((_, i) => (
        <Star key={i}
          className={cn(sizes[size], 'transition-colors',
            i < Math.round(rating) ? 'fill-amber text-amber' : 'text-gray-300',
            interactive && 'cursor-pointer hover:text-amber')}
          onClick={() => interactive && onChange?.(i + 1)} />
      ))}
    </div>
  );
}
