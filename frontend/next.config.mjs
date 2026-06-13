/** @type {import('next').NextConfig} */
const nextConfig = {
  images: { domains: ['www.gravatar.com', 'ui-avatars.com'] },
  output: 'standalone',
  experimental: {
    missingSuspenseWithCSRBailout: false,
  },
};
export default nextConfig;