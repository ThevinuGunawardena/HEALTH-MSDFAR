const PROXY_CONFIG = [
  {
    context: [
      "/api"
    ],
    target: "https://localhost:7239",
    secure: false,
    changeOrigin: true
  }
];

module.exports = PROXY_CONFIG;