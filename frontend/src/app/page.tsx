'use client';
import Link from 'next/link';
import { ArrowRight, Star, MessageCircle, TrendingUp, Shield, Zap, Heart } from 'lucide-react';

const features = [
  { icon: Shield, title: 'Evidence-Based Protocols', desc: 'Structured wellness protocols backed by community experience and research.' },
  { icon: MessageCircle, title: 'Community Discussions', desc: 'Ask questions, share results, and learn from thousands of practitioners.' },
  { icon: TrendingUp, title: 'Intelligent Search', desc: 'Find exactly what you need with Typesense-powered search and filters.' },
  { icon: Zap, title: 'Vote & Review', desc: 'Community voting surfaces the most effective protocols and discussions.' },
];

const categories = [
  { name: 'Nutrition', icon: '🥗', slug: 'nutrition' },
  { name: 'Sleep', icon: '😴', slug: 'sleep' },
  { name: 'Mental Health', icon: '🧠', slug: 'mental-health' },
  { name: 'Hormones', icon: '⚡', slug: 'hormones' },
  { name: 'Recovery', icon: '🔄', slug: 'recovery' },
  { name: 'Longevity', icon: '♾️', slug: 'longevity' },
  { name: 'Immunity', icon: '🛡️', slug: 'immunity' },
  { name: 'Nootropics', icon: '💊', slug: 'nootropics' },
];

export default function HomePage() {
  return (
    <div>
      {/* Hero */}
      <section className="text-center py-16 md:py-24">
        <div className="inline-flex items-center gap-2 px-4 py-1.5 bg-sage-muted text-forest rounded-full text-sm font-medium mb-6">
          <Heart className="w-4 h-4" /> Community-Powered Wellness
        </div>
        <h1 className="text-4xl md:text-6xl font-bold text-forest leading-tight mb-6">
          Discover Protocols.<br />
          <span className="text-amber">Share Your Journey.</span>
        </h1>
        <p className="text-lg text-slate-light max-w-2xl mx-auto mb-10">
          Browse structured healing protocols, join evidence-based discussions, and contribute to a growing library of community wellness knowledge.
        </p>
        <div className="flex flex-col sm:flex-row gap-4 justify-center">
          <Link href="/protocols" className="btn-primary text-base px-6 py-3">
            Browse Protocols <ArrowRight className="w-5 h-5" />
          </Link>
          <Link href="/threads" className="btn-secondary text-base px-6 py-3">
            Join Discussions
          </Link>
        </div>
      </section>

      {/* Categories */}
      <section className="mb-16">
        <h2 className="text-2xl font-bold text-forest mb-6 text-center">Browse by Category</h2>
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
          {categories.map(cat => (
            <Link key={cat.slug} href={`/protocols?category=${cat.slug}`}
              className="card p-4 flex items-center gap-3 hover:border-sage cursor-pointer group">
              <span className="text-2xl">{cat.icon}</span>
              <span className="font-medium text-slate group-hover:text-forest transition-colors">{cat.name}</span>
            </Link>
          ))}
        </div>
      </section>

      {/* Features */}
      <section className="mb-16">
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          {features.map(f => (
            <div key={f.title} className="card p-6">
              <div className="w-10 h-10 rounded-lg bg-sage-muted flex items-center justify-center mb-4">
                <f.icon className="w-5 h-5 text-forest" />
              </div>
              <h3 className="font-semibold text-forest mb-2">{f.title}</h3>
              <p className="text-sm text-slate-light">{f.desc}</p>
            </div>
          ))}
        </div>
      </section>

      {/* CTA */}
      <section className="bg-forest rounded-2xl p-10 text-center text-white">
        <h2 className="text-3xl font-bold mb-3">Ready to share your protocol?</h2>
        <p className="text-white/70 mb-6">Help others by documenting what worked for you.</p>
        <Link href="/protocols/new" className="btn-primary bg-amber hover:bg-amber-dark border-0 text-base px-6 py-3">
          Create a Protocol <ArrowRight className="w-5 h-5" />
        </Link>
      </section>
    </div>
  );
}
