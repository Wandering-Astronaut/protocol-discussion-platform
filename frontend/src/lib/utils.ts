import { clsx, type ClassValue } from 'clsx';

export function cn(...inputs: ClassValue[]) { return clsx(inputs); }

export function formatDate(dateStr: string): string {
  const d = new Date(dateStr);
  const now = new Date();
  const diff = Math.floor((now.getTime() - d.getTime()) / 1000);
  if (diff < 60) return 'just now';
  if (diff < 3600) return `${Math.floor(diff/60)}m ago`;
  if (diff < 86400) return `${Math.floor(diff/3600)}h ago`;
  if (diff < 604800) return `${Math.floor(diff/86400)}d ago`;
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

export function truncate(str: string, n: number) {
  return str.length > n ? str.slice(0, n) + '…' : str;
}

export function getDifficultyColor(difficulty: string) {
  return { beginner: 'text-emerald-600 bg-emerald-50', intermediate: 'text-amber-600 bg-amber-50', advanced: 'text-red-600 bg-red-50' }[difficulty] || 'text-gray-600 bg-gray-50';
}

export function getCategoryIcon(category: string) {
  const icons: Record<string, string> = {
    nutrition: '🥗', sleep: '😴', 'mental-health': '🧠', hormones: '⚡',
    recovery: '🔄', nootropics: '💊', detox: '🌿', longevity: '♾️', immunity: '🛡️',
  };
  return icons[category] || '📋';
}

// Demo user id - in a real app this would come from auth
export const DEMO_USER_ID = 1;
