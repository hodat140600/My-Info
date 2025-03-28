const axios = require('axios');

exports.handler = async function(event, context) {
  // Lấy package ID từ query params
  const packageId = event.queryStringParameters.id;
  if (!packageId) {
    return {
      statusCode: 400,
      body: 'Missing package ID'
    };
  }

  try {
    // Gọi API
    const response = await axios({
      method: 'POST',
      url: 'https://google-play-store-scraper-api.p.rapidapi.com/app-details',
      headers: {
        'Content-Type': 'application/json',
        'x-rapidapi-host': 'google-play-store-scraper-api.p.rapidapi.com',
        'x-rapidapi-key': '30186755c2msh02639d89f5c19a5p1b87fejsn590da5ff8808'
      },
      data: {
        language: 'en',
        country: 'us',
        appID: packageId
      }
    });

    // Lấy URL biểu tượng
    if (response.data && response.data.data && response.data.data.icon) {
      const iconUrl = response.data.data.icon;
      
      // Chuyển hướng đến URL biểu tượng
      return {
        statusCode: 302,
        headers: {
          'Location': iconUrl,
          'Cache-Control': 'public, max-age=86400'
        },
        body: ''
      };
    } else {
      return {
        statusCode: 404,
        body: 'Icon not found'
      };
    }
  } catch (error) {
    return {
      statusCode: 500,
      body: 'Error: ' + (error.message || 'Unknown error')
    };
  }
};
