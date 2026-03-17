import { apiFetch } from '@wordpress/api-fetch';

export const createSeriesApi = ({
  ajaxurl,
  nonce,
  fetchFn,
  wpData,
}) => {

  //fetchSeriesPosts, updateSeriesOrder, createSeriesTerm
  const fetchSeriesPosts = async (termId) => {
    const formData = new URLSearchParams({
      action: 'sm_get_series_posts',
      nonce,
      term_id: termId,
    });



    const res = await fetchFn(ajaxurl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData.toString(),
    });

    return res.json();
  };

  const updateSeriesOrder = async (termId, posts) => {
    const formData = new URLSearchParams({
      action: 'sm_update_series_order',
      nonce,
      term_id: termId,
      post_ids: posts.map((p) => p.id).join(','),
    });

    return fetchFn(ajaxurl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData.toString(),
    });
  };

  const createSeriesTerm = async (name) => {
    try {
      const term = await apiFetch({
        path: '/wp/v2/series',
        method: 'POST',
        data: { name: name.name || name },
      });
      return term;
    } catch (error) {
      throw new Error(error.message || 'Failed to create series');
    }
  };

  return {
    fetchSeriesPosts,
    updateSeriesOrder,
    createSeriesTerm,
  };
};