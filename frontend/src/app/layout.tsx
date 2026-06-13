import type { Metadata } from 'next';
import { Inter } from 'next/font/google';
import './globals.css';
import Navbar from '@/components/layout/Navbar';

const inter = Inter({ subsets: ['latin'], variable: '--font-inter' });

export const metadata: Metadata = {
  title: 'JustHolistics — Community Protocols & Discussions',
  description: 'Discover evidence-based holistic wellness protocols, join discussions, and share your healing journey.',
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className={inter.variable}>
      <body className="bg-cream min-h-screen text-slate font-sans antialiased">
        <Navbar />
        <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          {children}
        </main>
        <footer className="border-t border-sage-muted mt-16 py-8 text-center text-sm text-slate-light bg-cream-warm">
          <p>© 2024 JustHolistics · Built with care for community wellness</p>
        </footer>
      </body>
    </html>
  );
}
