import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  /* config options here */
  // Disable source maps in development to reduce memory usage
  productionBrowserSourceMaps: false,
  // Improve build stability
};

export default nextConfig;
