import TypesenseInstantSearchAdapter from 'typesense-instantsearch-adapter';

export const typesenseAdapter = new TypesenseInstantSearchAdapter({
  server: {
    apiKey: process.env.NEXT_PUBLIC_TYPESENSE_SEARCH_KEY!,
    nodes: [{
      host: process.env.NEXT_PUBLIC_TYPESENSE_HOST!,
      port: Number(process.env.NEXT_PUBLIC_TYPESENSE_PORT || 443),
      protocol: process.env.NEXT_PUBLIC_TYPESENSE_PROTOCOL || 'https',
    }],
    cacheSearchResultsForSeconds: 30,
  },
  additionalSearchParameters: {
    query_by: 'title,content,tags,author',
    query_by_weights: '4,2,3,1',
    highlight_full_fields: 'title',
    num_typos: 2,
  },
});

export const searchClient = typesenseAdapter.searchClient;
