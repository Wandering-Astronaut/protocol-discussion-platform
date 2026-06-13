'use client';
import { useState } from 'react';
import { Search, X } from 'lucide-react';

interface Props { value: string; onChange: (v: string) => void; placeholder?: string; }

export default function SearchBar({ value, onChange, placeholder = 'Search...' }: Props) {
  return (
    <div className="relative">
      <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-light" />
      <input value={value} onChange={e => onChange(e.target.value)}
        placeholder={placeholder}
        className="input pl-9 pr-9" />
      {value && (
        <button onClick={() => onChange('')}
          className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-light hover:text-slate">
          <X className="w-4 h-4" />
        </button>
      )}
    </div>
  );
}
