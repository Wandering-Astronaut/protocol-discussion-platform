'use client';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useState } from 'react';
import { Menu, X, Leaf, Search } from 'lucide-react';
import { cn } from '@/lib/utils';

const links = [
  { href: '/protocols', label: 'Protocols' },
  { href: '/threads', label: 'Discussions' },
];

export default function Navbar() {
  const pathname = usePathname();
  const [open, setOpen] = useState(false);

  return (
    <nav className="sticky top-0 z-50 bg-forest text-white shadow-lg">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-2.5 font-semibold text-lg tracking-tight">
            <div className="w-8 h-8 bg-amber rounded-lg flex items-center justify-center">
              <Leaf className="w-4 h-4 text-white" />
            </div>
            <span>Just<span className="text-sage-light font-bold">Holistics</span></span>
          </Link>

          {/* Desktop links */}
          <div className="hidden md:flex items-center gap-1">
            {links.map(l => (
              <Link key={l.href} href={l.href}
                className={cn('px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                  pathname.startsWith(l.href)
                    ? 'bg-forest-light text-white'
                    : 'text-white/70 hover:text-white hover:bg-white/10'
                )}>
                {l.label}
              </Link>
            ))}
          </div>

          {/* Actions */}
          <div className="hidden md:flex items-center gap-2">
            <Link href="/search" className="p-2 rounded-lg hover:bg-white/10 transition-colors">
              <Search className="w-5 h-5 text-white/80" />
            </Link>
            <Link href="/protocols/new" className="btn-primary bg-amber hover:bg-amber-dark border-0 text-white">
              + New Protocol
            </Link>
          </div>

          {/* Mobile menu button */}
          <button className="md:hidden p-2 rounded-lg hover:bg-white/10" onClick={() => setOpen(!open)}>
            {open ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
          </button>
        </div>
      </div>

      {/* Mobile menu */}
      {open && (
        <div className="md:hidden border-t border-white/10 bg-forest-dark px-4 py-4 space-y-2">
          {links.map(l => (
            <Link key={l.href} href={l.href} onClick={() => setOpen(false)}
              className="block px-3 py-2 rounded-lg text-sm font-medium text-white/80 hover:text-white hover:bg-white/10">
              {l.label}
            </Link>
          ))}
          <Link href="/protocols/new" onClick={() => setOpen(false)}
            className="block mt-2 btn-primary bg-amber border-0 text-center">
            + New Protocol
          </Link>
        </div>
      )}
    </nav>
  );
}
