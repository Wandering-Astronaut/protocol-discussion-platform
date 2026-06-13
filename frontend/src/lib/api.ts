import axios from 'axios';

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api',
  headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
});

export default api;

// ── Types ────────────────────────────────────────────────────
export interface User {
  id: number; name: string; username: string;
  email?: string; avatar?: string; bio?: string; avatar_url?: string;
}
export interface Protocol {
  id: number; user_id: number; title: string; content: string;
  tags: string[]; category: string; difficulty: string; duration: string;
  avg_rating: number; review_count: number; vote_score: number; status: string;
  created_at: string; updated_at: string; user?: User;
  threads?: Thread[]; reviews?: Review[];
}
export interface Thread {
  id: number; user_id: number; protocol_id?: number; title: string; body: string;
  tags: string[]; vote_score: number; comment_count: number; status: string;
  created_at: string; updated_at: string; user?: User;
  protocol?: { id: number; title: string; category?: string };
  comments?: Comment[];
}
export interface Comment {
  id: number; user_id: number; thread_id: number; parent_id?: number;
  body: string; vote_score: number; depth: number; is_deleted: boolean;
  created_at: string; user?: User; replies?: Comment[];
}
export interface Review {
  id: number; user_id: number; protocol_id: number; rating: number;
  title?: string; body?: string; verified_user: boolean;
  created_at: string; user?: User;
}
export interface PaginatedResponse<T> {
  data: T[]; current_page: number; last_page: number;
  per_page: number; total: number;
}

// ── API helpers ──────────────────────────────────────────────
export const protocolsApi = {
  list: (params?: Record<string, string>) => api.get<PaginatedResponse<Protocol>>('/protocols', { params }),
  search: (params?: Record<string, string>) => api.get('/protocols/search', { params }),
  get: (id: number) => api.get<Protocol>(`/protocols/${id}`),
  create: (data: Partial<Protocol>) => api.post<Protocol>('/protocols', data),
  update: (id: number, data: Partial<Protocol>) => api.put<Protocol>(`/protocols/${id}`, data),
  delete: (id: number) => api.delete(`/protocols/${id}`),
  reviews: (id: number, params?: Record<string, string>) =>
    api.get<PaginatedResponse<Review>>(`/protocols/${id}/reviews`, { params }),
  addReview: (id: number, data: Partial<Review>) =>
    api.post<Review>(`/protocols/${id}/reviews`, data),
};

export const threadsApi = {
  list: (params?: Record<string, string>) => api.get<PaginatedResponse<Thread>>('/threads', { params }),
  search: (params?: Record<string, string>) => api.get('/threads/search', { params }),
  get: (id: number) => api.get<Thread>(`/threads/${id}`),
  create: (data: Partial<Thread>) => api.post<Thread>('/threads', data),
  update: (id: number, data: Partial<Thread>) => api.put<Thread>(`/threads/${id}`, data),
  delete: (id: number) => api.delete(`/threads/${id}`),
  comments: (id: number) => api.get<Comment[]>(`/threads/${id}/comments`),
  addComment: (id: number, data: Partial<Comment>) =>
    api.post<Comment>(`/threads/${id}/comments`, data),
};

export const votesApi = {
  vote: (data: { user_id: number; voteable_type: 'thread'|'comment'; voteable_id: number; value: 1|-1 }) =>
    api.post('/vote', data),
  userVotes: (userId: number) => api.get<Record<string, number>>(`/votes/user/${userId}`),
};

export const commentsApi = {
  update: (id: number, data: { body: string }) => api.put<Comment>(`/comments/${id}`, data),
  delete: (id: number) => api.delete(`/comments/${id}`),
};
