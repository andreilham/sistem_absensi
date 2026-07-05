// Cloudflare Worker Script untuk Reverse Proxy
// Deploy di: https://dash.cloudflare.com/workers

export default {
  async fetch(request) {
    const url = new URL(request.url);
    const path = url.pathname;
    
    // Konfigurasi backend (sesuaikan dengan Railway domain Anda)
    const LARAVEL_BACKEND = 'https://your-laravel-app.railway.app';
    const STREAMLIT_BACKEND = 'https://your-streamlit-app.railway.app';

    try {
      // Route ke Streamlit untuk /streamlit dan /scan
      if (path.startsWith('/streamlit') || path.startsWith('/scan')) {
        const streamlitUrl = new URL(path.replace('/streamlit', '') || '/', STREAMLIT_BACKEND);
        
        return fetch(new Request(streamlitUrl, {
          method: request.method,
          headers: {
            ...request.headers,
            'Host': new URL(STREAMLIT_BACKEND).host,
            'X-Forwarded-For': request.headers.get('CF-Connecting-IP') || '',
            'X-Forwarded-Proto': 'https',
            'X-Forwarded-Host': url.host,
          },
          body: request.body,
        }));
      }

      // Route ke Laravel untuk semua path lainnya
      const laravelUrl = new URL(path, LARAVEL_BACKEND);
      laravelUrl.search = url.search;

      return fetch(new Request(laravelUrl, {
        method: request.method,
        headers: {
          ...request.headers,
          'Host': new URL(LARAVEL_BACKEND).host,
          'X-Forwarded-For': request.headers.get('CF-Connecting-IP') || '',
          'X-Forwarded-Proto': 'https',
          'X-Forwarded-Host': url.host,
        },
        body: request.body,
      }));
    } catch (err) {
      return new Response('Gateway Error: ' + err.message, { status: 502 });
    }
  },
};
