import { useSelect } from '@wordpress/data';

export const useSeriesTerms = () => {
  const { seriesTerms, isResolvingTerms } = useSelect((select) => {
    const terms = select('core').getEntityRecords('taxonomy', 'series', { per_page: -1 });
    return {
      seriesTerms: terms || [],
      isResolvingTerms: select('core').isResolving('getEntityRecords', ['taxonomy', 'series', { per_page: -1 }]),
    };
  }, []);

  return {
    seriesTerms,
    isResolvingTerms,
  };
};
