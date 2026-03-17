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
    const formData = new URLSearchParams({
      action: 'sm_create_series_term',
      nonce,
      name: name.name || name,
    });

    const res = await fetchFn(ajaxurl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData.toString(),
    });

    const data = await res.json();
    if (data.success) {
      // Return object compatible with WordPress entity format
      return {
        id: data.data.id,
        name: data.data.name,
        slug: data.data.slug,
        taxonomy: data.data.taxonomy,
      };
    }
    throw new Error(data.data?.message || 'Failed to create series');
  };

  return {
    fetchSeriesPosts,
    updateSeriesOrder,
    createSeriesTerm,
  };
};